<template>
  <div class="quo-root">
    <main class="quo-main">
      <section class="quo-section">
        <!-- Top bar -->
        <div class="quo-topbar">
          <div>
            <h1 class="quo-title">Quotations</h1>
            <p class="quo-subtitle">{{ pagination.total }} quotations total</p>
          </div>
          <button class="penda-btn penda-btn-primary " @click="openCreate">
            <font-awesome-icon :icon="['fas', 'plus']" /> New Quotation
          </button>
        </div>

        <!-- Stats row -->
        <div class="stats-row" v-if="statistics">
          <StatCard label="Total Quotes" :value="statistics.total_estimates" accent="gray" />
          <StatCard label="Pending" :value="statistics.pending" accent="amber" />
          <StatCard label="Email Failed" :value="statistics.email_failed" accent="red" />
          <StatCard label="Converted" :value="statistics.converted" accent="teal" />
          <StatCard label="Converted Value" :value="'R ' + fmt(statistics.converted_value)" accent="green" />
        </div>

        <!-- Filters -->
        <div class="filter-bar">
          <div class="search-wrap">
            <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
            <input
              v-model="filters.search"
              class="filter-search"
              placeholder="Search name, email or quote #…"
              @input="debouncedSearch"
            />
          </div>

          <select v-model="filters.status" class="filter-select" @change="loadEstimates">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="emailed">Emailed</option>
            <option value="email_failed">Email Failed</option>
            <option value="completed">Completed</option>
            <option value="converted">Converted</option>
            <option value="cancelled">Cancelled</option>
          </select>

          <input v-model="filters.date_from" type="date" class="filter-select" @change="loadEstimates" />
          <input v-model="filters.date_to" type="date" class="filter-select" @change="loadEstimates" />

          <label class="filter-toggle">
            <input v-model="filters.expired_only" type="checkbox" @change="loadEstimates" />
            <span>Expired only</span>
          </label>

          <button class="btn-reset" @click="resetFilters">Reset</button>
        </div>

        <!-- Bulk actions -->
        <div class="bulk-actions" v-if="selected.length > 0">
          <span>{{ selected.length }} selected</span>
          <button class="btn-success" @click="bulkUpdateStatus('completed')">Mark Completed</button>
          <button class="btn-danger" @click="bulkUpdateStatus('cancelled')">Mark Cancelled</button>
          <button class="btn-primary" @click="bulkSendEmails">Send Emails</button>
        </div>

        <!-- Table -->
        <div class="quo-table-wrap">
          <table class="quo-table">
            <thead>
              <tr>
                <th><input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" /></th>
                <th @click="sort('quote_number')" class="sortable">Quote #</th>
                <th @click="sort('name')" class="sortable">Name</th>
                <th>Email</th>
                <th @click="sort('total_amount')" class="sortable text-right">Amount</th>
                <th>Status</th>
                <th @click="sort('created_at')" class="sortable">Date</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="8" class="table-state">Loading…</td></tr>
              <tr v-else-if="!estimates.length"><td colspan="8" class="table-state">No quotations found</td></tr>
              <tr v-else v-for="est in estimates" :key="est.id" class="quo-row" :class="rowClass(est)">
                <td @click.stop>
                  <input type="checkbox" :value="est.id" v-model="selected" />
                </td>
                <td class="quo-num" @click="openView(est)">{{ est.quote_number }}</td>
                <td @click="openView(est)">{{ est.name }}</td>
                <td><a :href="`mailto:${est.email}`" @click.stop>{{ est.email }}</a></td>
                <td class="text-right font-med" @click="openView(est)">R{{ est.total_amount }}</td>
                <td @click="openView(est)"><StatusBadge :status="est.status" /></td>
                <td @click="openView(est)">{{ fmtDate(est.created_at) }}</td>
                <td class="row-actions" @click.stop>
                  <QuoteRowMenu
                    :estimate="est"
                    @view="openView"
                    @resend="resendEmail"
                    @download="downloadPDF"
                    @duplicate="duplicateEstimate"
                    @convert="openConvert"
                    @delete="openDelete"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="pagination.total > 0">
          <span class="pag-info">{{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}</span>
          <div class="pag-controls">
            <select v-model="pagination.per_page" class="filter-select-sm" @change="loadEstimates">
              <option :value="15">15</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
            <button class="pag-btn" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">‹</button>
            <span class="pag-page">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
            <button class="pag-btn" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">›</button>
          </div>
        </div>
      </section>
    </main>

    <!-- Modals -->
    <QuoteFormModal
      :show="showFormModal"
      :is-editing="isEditing"
      :estimate="selectedEstimate"
      :services="services"
      :saving="saving"
      @close="closeModals"
      @save="handleSave"
    />

    <QuoteViewModal
      :show="showViewModal"
      :estimate="selectedEstimate"
      @close="closeModals"
      @resend="resendEmail"
      @download="downloadPDF"
      @convert="openConvert"
    />

    <QuoteDeleteModal
      :show="showDeleteModal"
      :estimate="selectedEstimate"
      @close="closeModals"
      @deleted="onDeleted"
    />

    <ConvertToInvoiceModal
      :show="showConvertModal"
      :estimate="selectedEstimate"
      @close="closeModals"
      @converted="onConverted"
    />

    <!-- Toasts -->
    <div class="toast-container">
      <div v-for="toast in toasts" :key="toast.id" class="toast" :class="toast.type">{{ toast.message }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import StatCard from './StatCard.vue'
import StatusBadge from './StatusBadge.vue'
import QuoteRowMenu from './QuoteRowMenu.vue'
import QuoteFormModal from './QuoteFormModal.vue'
import QuoteViewModal from './QuoteViewModal.vue'
import QuoteDeleteModal from './QuoteDeleteModal.vue'
import ConvertToInvoiceModal from './ConvertToInvoiceModal.vue'

// If your project already has a shared toast composable (as InvoiceManager
// does via '@/composables/useToast.js'), swap the local implementation
// below for: import { useNotify } from '@/composables/useToast.js'
const toasts = ref([])
function notifySuccess(message) { pushToast(message, 'success') }
function notifyError(message) { pushToast(message, 'error') }
function pushToast(message, type) {
  const id = Date.now() + Math.random()
  toasts.value.push({ id, message, type })
  setTimeout(() => { toasts.value = toasts.value.filter(t => t.id !== id) }, 5000)
}

const estimates = ref([])
const services = ref([])
const statistics = ref(null)
const loading = ref(false)
const saving = ref(false)
const selected = ref([])

const showFormModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const showConvertModal = ref(false)
const isEditing = ref(false)
const selectedEstimate = ref(null)

const filters = reactive({ search: '', status: '', date_from: '', date_to: '', expired_only: false })
const sorting = reactive({ sort_by: 'created_at', sort_order: 'desc' })
const pagination = reactive({ current_page: 1, per_page: 15, total: 0, last_page: 1, from: 0, to: 0 })

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function api(method, path, data = null) {
  const opts = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': getCsrf(),
    },
  }
  if (data) opts.body = JSON.stringify(data)
  const res = await fetch('/api' + path, opts)
  if (!res.ok) throw await res.json()
  return res.json()
}

const isAllSelected = computed(() => estimates.value.length > 0 && selected.value.length === estimates.value.length)

async function loadEstimates() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      ...filters,
      ...sorting,
      page: pagination.current_page,
      per_page: pagination.per_page,
      expired_only: filters.expired_only ? '1' : '',
    }).toString()
    const res = await api('GET', `/estimates?${params}`)
    estimates.value = res.data
    Object.assign(pagination, {
      current_page: res.current_page,
      total: res.total,
      last_page: res.last_page,
      from: res.from,
      to: res.to,
    })
  } catch (err) {
    notifyError(err?.message ?? 'Failed to load quotations.')
  } finally {
    loading.value = false
  }
}

async function loadStatistics() {
  try {
    const res = await api('GET', '/estimates/statistics')
    statistics.value = res.statistics
  } catch { /* non-fatal */ }
}


async function loadServices() {
  try { services.value = await api('GET', '/services') } catch { /* optional endpoint */ }
}

function openCreate() { isEditing.value = false; selectedEstimate.value = null; showFormModal.value = true }
function editEstimate(est) { selectedEstimate.value = est; isEditing.value = true; showFormModal.value = true }

async function openView(est) {
  try {
    const res = await api('GET', `/estimates/${est.id}`)
    selectedEstimate.value = res.estimate
    showViewModal.value = true
  } catch (err) {
    notifyError(err?.message ?? 'Failed to load quotation.')
  }
}

function openDelete(est) { selectedEstimate.value = est; showDeleteModal.value = true }
function openConvert(est) { selectedEstimate.value = est; showConvertModal.value = true }

async function duplicateEstimate(est) {
  try {
    await api('POST', `/estimates/${est.id}/duplicate`)
    await Promise.all([loadEstimates(), loadStatistics()])
    notifySuccess('Quotation duplicated.')
  } catch (err) {
    notifyError(err?.message ?? 'Failed to duplicate quotation.')
  }
}

async function resendEmail(est) {
  try {
    await api('POST', `/estimates/${est.id}/resend-email`)
    await Promise.all([loadEstimates(), loadStatistics()])
    notifySuccess('Email sent successfully.')
  } catch (err) {
    notifyError(err?.message ?? 'Failed to send email.')
  }
}

async function downloadPDF(est) {
  window.open(`/api/estimates/${est.id}/pdf`, '_blank')
}

async function bulkUpdateStatus(status) {
  try {
    await api('PUT', '/estimates/bulk-status', { ids: selected.value, status })
    notifySuccess(`${selected.value.length} quotations updated.`)
    selected.value = []
    await Promise.all([loadEstimates(), loadStatistics()])
  } catch (err) {
    notifyError(err?.message ?? 'Failed to bulk update quotations.')
  }
}

async function bulkSendEmails() {
  try {
    const res = await api('POST', '/estimates/bulk-email', { ids: selected.value })
    notifySuccess(`${res.sent} emails sent successfully.`)
    selected.value = []
    await Promise.all([loadEstimates(), loadStatistics()])
  } catch (err) {
    notifyError(err?.message ?? 'Failed to send bulk emails.')
  }
}

async function handleSave(formData) {
  saving.value = true
  try {
    if (isEditing.value) {
      await api('PUT', `/estimates/${selectedEstimate.value.id}`, formData)
      notifySuccess('Quotation updated successfully.')
    } else {
      await api('POST', '/estimates', formData)
      notifySuccess('Quotation created successfully.')
    }
    closeModals()
    await Promise.all([loadEstimates(), loadStatistics()])
  } catch (err) {
    notifyError(err?.message ?? 'Failed to save quotation.')
  } finally {
    saving.value = false
  }
}

function onDeleted() {
  closeModals()
  Promise.all([loadEstimates(), loadStatistics()])
  notifySuccess('Quotation deleted successfully.')
}

function onConverted(data) {
  closeModals()
  Promise.all([loadEstimates(), loadStatistics()])
  notifySuccess(data?.message ?? 'Quotation converted to invoice.')
}

function toggleSelectAll() {
  selected.value = isAllSelected.value ? [] : estimates.value.map(e => e.id)
}

function closeModals() {
  showFormModal.value = false
  showViewModal.value = false
  showDeleteModal.value = false
  showConvertModal.value = false
  selectedEstimate.value = null
}

function sort(field) {
  if (sorting.sort_by === field) sorting.sort_order = sorting.sort_order === 'asc' ? 'desc' : 'asc'
  else { sorting.sort_by = field; sorting.sort_order = 'asc' }
  loadEstimates()
}

function changePage(page) { pagination.current_page = page; loadEstimates() }

function resetFilters() {
  filters.search = ''
  filters.status = ''
  filters.date_from = ''
  filters.date_to = ''
  filters.expired_only = false
  pagination.current_page = 1
  loadEstimates()
}

function rowClass(est) {
  return {
    'row-email-failed': est.status === 'email_failed',
    'row-converted': est.status === 'converted',
    'row-cancelled': est.status === 'cancelled',
  }
}

function fmt(v) { return parseFloat(v || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—' }

const debouncedSearch = (() => {
  let t
  return () => { clearTimeout(t); t = setTimeout(loadEstimates, 300) }
})()

onMounted(async () => {
  await Promise.all([loadEstimates(), loadStatistics(), loadServices()])
})
</script>

<style scoped>
.quo-root { display: block; min-height: 100vh; background: #f8f7f4; font-family: 'DM Sans', 'Söhne', system-ui, sans-serif; }
.quo-main { flex: 1; overflow: auto; }
.quo-section { padding: 36px 40px; max-width: 1280px; }

.quo-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
.quo-title { font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; margin: 0 0 4px; font-family: 'Playfair Display', Georgia, serif; }
.quo-subtitle { font-size: 13px; color: #888; margin: 0; }

.btn-create { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #1a1a1a; color: #f0efe7; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
.btn-create:hover { background: #333; }

.stats-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px; }

.filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #aaa; }
.filter-search { width: 100%; padding: 8px 12px 8px 32px; border: 1px solid #e0e0d8; border-radius: 8px; background: white; font-size: 14px; color: #1a1a1a; outline: none; box-sizing: border-box; }
.filter-search:focus { border-color: #2f9e8f; box-shadow: 0 0 0 3px rgba(47,158,143,0.1); }
.filter-select { padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 8px; background: white; font-size: 13px; color: #333; outline: none; cursor: pointer; }
.filter-select-sm { padding: 5px 8px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 13px; }
.filter-toggle { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #555; cursor: pointer; }
.btn-reset { padding: 8px 14px; border: 1px solid #e0e0d8; background: white; border-radius: 8px; font-size: 13px; color: #555; cursor: pointer; }
.btn-reset:hover { border-color: #d4a853; color: #333; }

.bulk-actions { background: #edf2f7; padding: 12px 18px; border-radius: 8px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
.bulk-actions span { font-weight: 500; color: #2d3748; font-size: 13px; }
.btn-success, .btn-danger, .btn-primary { padding: 7px 14px; border-radius: 6px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-success { background: #48bb78; color: white; }
.btn-danger { background: #f56565; color: white; }
.btn-primary { background: #4299e1; color: white; }

.quo-table-wrap { background: white; border-radius: 12px; border: 1px solid #e8e8e0; overflow: hidden; margin-bottom: 16px; }
.quo-table { width: 100%; border-collapse: collapse; }
.quo-table thead th { padding: 12px 16px; font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f0f0e8; background: #fafaf7; text-align: left; white-space: nowrap; }
.quo-table thead th.sortable { cursor: pointer; user-select: none; }
.quo-table thead th.sortable:hover { color: #444; }
.quo-table thead th.text-right { text-align: right; }
.quo-table tbody td { padding: 13px 16px; font-size: 14px; color: #333; border-bottom: 1px solid #f4f4ee; vertical-align: middle; }
.quo-row { cursor: pointer; transition: background 0.1s; }
.quo-row:hover { background: #fafaf7; }
.quo-row:last-child td { border-bottom: none; }
.quo-num { font-weight: 600; color: #1a1a1a; font-variant-numeric: tabular-nums; }
.text-right { text-align: right; }
.font-med { font-weight: 500; }
.table-state { text-align: center; padding: 48px; color: #aaa; font-style: italic; }

.row-email-failed { background: #fff5f5; }
.row-converted { background: #f0fdfa; }
.row-cancelled { background: #f7fafc; opacity: 0.7; }

.row-actions { display: flex; justify-content: flex-end; }

.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn { width: 30px; height: 30px; border: 1px solid #e0e0d8; border-radius: 6px; background: white; color: #555; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-btn:hover:not(:disabled) { border-color: #2f9e8f; color: #2f9e8f; }
.pag-page { font-size: 13px; color: #555; }

.toast-container { position: fixed; top: 20px; right: 20px; z-index: 2000000; }
.toast { background: white; padding: 15px 20px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 10px; border-left: 4px solid #4299e1; max-width: 300px; }
.toast.success { border-left-color: #48bb78; }
.toast.error { border-left-color: #f56565; }

@media (max-width: 900px) {
  .quo-section { padding: 20px; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>