<template>
  <div class="inv-root">
    <!-- Sidebar tab navigation -->
    <nav class="top-nav">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        class="tab-btn"
        :class="{ active: activeTab === tab.id }"
        @click="activeTab = tab.id"
      >
        {{ tab.label }}
        <span v-if="tab.badge" class="tab-badge">{{ tab.badge }}</span>
      </button>
    </nav>

    <!-- Main content -->
    <main class="inv-main">
      <!-- ── One-off Invoices tab ── -->
      <section v-if="activeTab === 'invoices'" class="inv-section">
        <!-- Top bar -->
        <div class="inv-topbar">
          <div>
            <h1 class="inv-title">Invoices</h1>
            <p class="inv-subtitle">{{ pagination.total }} invoices total</p>
          </div>
          <button class="btn-create" @click="openCreate">
            <font-awesome-icon :icon="['fas', 'plus']" /> New Invoice
          </button>
        </div>

        <!-- Stats row -->
        <div class="stats-row" v-if="statistics">
          <StatCard label="Total Revenue" :value="'R ' + fmt(statistics.total_revenue)" accent="teal" />
          <StatCard label="Pending"       :value="'R ' + fmt(statistics.pending_revenue)" accent="amber" />
          <StatCard label="Overdue"       :value="statistics.overdue_invoices"  accent="red" />
          <StatCard label="Draft"         :value="statistics.draft_invoices"    accent="gray" />
        </div>

        <!-- Filters -->
        <div class="filter-bar">
          <div class="search-wrap">
            <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
            <input
              v-model="filters.search"
              class="filter-search"
              placeholder="Search invoice # or client…"
              @input="debouncedSearch"
            />
          </div>

          <select v-model="filters.status" class="filter-select" @change="loadInvoices">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
          </select>

          <select v-model="filters.client_id" class="filter-select" @change="loadInvoices">
            <option value="">All clients</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>

          <input v-model="filters.date_from" type="date" class="filter-select" @change="loadInvoices" />
          <input v-model="filters.date_to"   type="date" class="filter-select" @change="loadInvoices" />

          <label class="filter-toggle">
            <input v-model="filters.overdue_only" type="checkbox" @change="loadInvoices" />
            <span>Overdue only</span>
          </label>
        </div>

        <!-- Table -->
        <div class="inv-table-wrap">
          <table class="inv-table">
            <thead>
              <tr>
                <th @click="sort('invoice_number')" class="sortable">Invoice #</th>
                <th>Client</th>
                <th @click="sort('invoice_date')" class="sortable">Date</th>
                <th @click="sort('due_date')" class="sortable">Due</th>
                <th @click="sort('total')" class="sortable text-right">Total</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="7" class="table-state">Loading…</td></tr>
              <tr v-else-if="!invoices.length"><td colspan="7" class="table-state">No invoices found</td></tr>
              <tr
                v-else
                v-for="inv in invoices"
                :key="inv.id"
                class="inv-row"
                @click="viewInvoice(inv)"
              >
                <td class="inv-num">{{ inv.invoice_number }}</td>
                <td>{{ inv.client?.name }}</td>
                <td>{{ fmtDate(inv.invoice_date) }}</td>
                <td :class="{ 'text-danger': inv.is_overdue }">{{ fmtDate(inv.due_date) }}</td>
                <td class="text-right font-med">R {{ fmt(inv.total) }}</td>
                <td><StatusBadge :status="inv.status" /></td>
                <td class="row-actions" @click.stop>
                  <button class="icon-btn" title="Edit" @click="editInvoice(inv)"><font-awesome-icon :icon="['fas', 'pencil']" /></button>
                  <button class="icon-btn" title="Duplicate" @click="duplicateInvoice(inv)"><font-awesome-icon :icon="['fas', 'clone']" /></button>
                  <InvoiceRowMenu 
                    :invoice="inv"
                    @send="sendInvoice"
                    @export="exportInvoice"
                    @reminder="sendReminder"
                    @print="printInvoice"
                    @recurring="makeRecurring"
                    @mark-sent="markAsSent" 
                    @payment="openPayment" 
                    @delete="openDelete" 
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="pagination.total > 0">
          <span class="pag-info">
            {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}
          </span>
          <div class="pag-controls">
            <select v-model="pagination.per_page" class="filter-select-sm" @change="loadInvoices">
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

      <!-- ── Recurring Invoices tab ── -->
      <section v-if="activeTab === 'recurring'" class="inv-section">
        <RecurringInvoiceManager :clients="clients" :services="services" />
      </section>
    </main>

    <!-- Modals -->
    <InvoiceFormModal
      :show="showFormModal"
      :is-editing="isEditing"
      :invoice="selectedInvoice"
      :clients="clients"
      :services="services"
      :saving="saving"
      @close="closeModals"
      @save="handleSave"
      @client-created="onClientCreated"
    />

    <InvoiceViewModal
      :show="showViewModal"
      :invoice="selectedInvoice"
      @close="closeModals"
    />

    <InvoicePaymentModal
      :show="showPaymentModal"
      :invoice="selectedInvoice"
      @close="closeModals"
      @saved="onPaymentSaved"
    />

    <InvoiceExportModal
      :show="showExportModal"
      :invoice="selectedInvoice"
      @close="closeModals"
      @mark-sent="markAsSent"
    />

    <InvoiceDeleteModal
      :show="showDeleteModal"
      :invoice="selectedInvoice"
      @close="closeModals"
      @deleted="deleteInvoice"
    />

    <MakeRecurringModal
      :show="showRecurringModal"
      :invoice="selectedInvoice"
      :saving="savingRecurring"
      @close="closeModals"
      @save="handleMakeRecurring"
    />

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import RecurringInvoiceManager from './RecurringInvoiceManager.vue'
import InvoiceFormModal        from './InvoiceFormModal.vue'
import InvoiceViewModal        from './InvoiceViewModal.vue'
import InvoicePaymentModal     from './InvoicePaymentModal.vue'
import StatCard                from './partials/StatCard.vue'
import StatusBadge             from './partials/StatusBadge.vue'
import InvoiceRowMenu          from './partials/InvoiceRowMenu.vue'
import InvoiceExportModal      from './InvoiceExportModal.vue'
import InvoiceDeleteModal      from './InvoiceDeleteModal.vue'
import MakeRecurringModal      from './MakeRecurringModal.vue'

// ── Tabs ────────────────────────────────────────────────────────────────────
const activeTab    = ref('invoices')
const overdueCount = computed(() => statistics.value?.overdue_invoices ?? 0)
const tabs = computed(() => [
  { id: 'invoices',  label: 'Invoices',  icon: 'div', badge: overdueCount.value > 0 ? overdueCount.value : null },
  { id: 'recurring', label: 'Recurring', icon: 'div', badge: null },
])

// ── State ────────────────────────────────────────────────────────────────────
const invoices   = ref([])
const clients    = ref([])
const services   = ref([])
const statistics = ref(null)
const loading    = ref(false)
const saving     = ref(false)

const showFormModal    = ref(false)
const showViewModal    = ref(false)
const showPaymentModal = ref(false)
const showExportModal  = ref(false)
const showDeleteModal  = ref(false)
const isEditing        = ref(false)
const selectedInvoice  = ref(null)
const showRecurringModal = ref(false)
const savingRecurring    = ref(false)

const filters    = reactive({ search: '', status: '', client_id: '', date_from: '', date_to: '', overdue_only: false })
const sorting    = reactive({ sort_by: 'created_at', sort_order: 'desc' })
const pagination = reactive({ current_page: 1, per_page: 15, total: 0, last_page: 1, from: 0, to: 0 })

// ── API helper (includes CSRF token for Laravel) ──────────────────────────────
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

// ── Data loading ─────────────────────────────────────────────────────────────
async function loadInvoices() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      ...filters,
      ...sorting,
      page:         pagination.current_page,
      per_page:     pagination.per_page,
      overdue_only: filters.overdue_only ? '1' : '',
    }).toString()
    const res = await api('GET', `/invoices?${params}`)
    invoices.value = res.data
    Object.assign(pagination, {
      current_page: res.current_page,
      total:        res.total,
      last_page:    res.last_page,
      from:         res.from,
      to:           res.to,
    })
  } finally { loading.value = false }
}

async function loadClients() {
  clients.value = await api('GET', '/invoices/clients')
}

async function loadStatistics() {
  const res = await api('GET', '/invoices/statistics')
  statistics.value = res.statistics
}

async function loadServices() {
  try { services.value = await api('GET', '/services') } catch {}
}

// ── Actions ──────────────────────────────────────────────────────────────────
function openCreate() { isEditing.value = false; selectedInvoice.value = null; showFormModal.value = true }

async function viewInvoice(inv) {
  const res = await api('GET', `/invoices/${inv.id}`)
  selectedInvoice.value = res.invoice
  showViewModal.value = true
}

function editInvoice(inv)  { selectedInvoice.value = inv; isEditing.value = true; showFormModal.value = true }
function openPayment(inv)  { selectedInvoice.value = inv; showPaymentModal.value = true }
function exportInvoice(inv) { selectedInvoice.value = inv; showExportModal.value = true }
function openDelete(inv) { selectedInvoice.value = inv; showDeleteModal.value = true }

async function duplicateInvoice(inv) {
  await api('POST', `/invoices/${inv.id}/duplicate`)
  await Promise.all([loadInvoices(), loadStatistics()])
}

async function sendInvoice(inv) {
  await api('POST', `/invoices/${inv.id}/send`)
  await Promise.all([loadInvoices(), loadStatistics()])
}

async function sendReminder(inv) {
  await api('POST', `/invoices/${inv.id}/send-reminder`)
  await Promise.all([loadInvoices(), loadStatistics()])
}

async function printInvoice(inv) {
  await api('POST', `/invoices/${inv.id}/print`)
  await Promise.all([loadInvoices(), loadStatistics()])
}

function makeRecurring(inv) {
  // Load full invoice (with items) so the modal can show the line item preview
  selectedInvoice.value    = inv
  showRecurringModal.value = true
} 

async function handleMakeRecurring(payload) {
  savingRecurring.value = true
  try {
    // POST /api/invoices/{id}/make-recurring  →  InvoiceController@makeRecurring
    await api('POST', `/invoices/${selectedInvoice.value.id}/make-recurring`, payload)
    showRecurringModal.value = false
    selectedInvoice.value    = null
    // Optionally switch to the recurring tab so the user sees the new schedule
    activeTab.value = 'recurring'
  } catch (err) {
    console.error('Failed to create recurring schedule:', err)
    alert(err?.message ?? 'Failed to create recurring schedule.')
  } finally {
    savingRecurring.value = false
  }
}

async function markAsSent(inv) {
  await api('POST', `/invoices/${inv.id}/mark-as-sent`)
  await Promise.all([loadInvoices(), loadStatistics()])
}

async function deleteInvoice() {
  await Promise.all([loadInvoices(), loadStatistics()])
}

async function handleSave(formData) {
  saving.value = true
  try {
    if (isEditing.value) {
      await api('PUT', `/invoices/${selectedInvoice.value.id}`, formData)
    } else {
      await api('POST', '/invoices', formData)
    }
    closeModals()
    await Promise.all([loadInvoices(), loadStatistics()])
  } catch (err) {
    console.error('Save failed:', err)
    alert(err?.message ?? 'Failed to save invoice. Please check your inputs.')
  } finally { saving.value = false }
}

async function onPaymentSaved() {
  closeModals()
  await Promise.all([loadInvoices(), loadStatistics()])
}

/**
 * Called when InvoiceFormModal successfully creates a new client inline.
 * Adds it to our clients list so it appears in filter dropdowns immediately.
 */
function onClientCreated(newClient) {
  if (!clients.value.find(c => c.id === newClient.id)) {
    clients.value.push(newClient)
    clients.value.sort((a, b) => a.name.localeCompare(b.name))
  }
}

function closeModals() {
  showFormModal.value    = false
  showViewModal.value    = false
  showPaymentModal.value = false
  showExportModal.value  = false
  showDeleteModal.value  = false
  selectedInvoice.value  = null
  showRecurringModal.value = false 
}

function sort(field) {
  if (sorting.sort_by === field) sorting.sort_order = sorting.sort_order === 'asc' ? 'desc' : 'asc'
  else { sorting.sort_by = field; sorting.sort_order = 'asc' }
  loadInvoices()
}

function changePage(page) { pagination.current_page = page; loadInvoices() }

// ── Utilities ────────────────────────────────────────────────────────────────
function fmt(v)     { return parseFloat(v || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—' }

const debouncedSearch = (() => {
  let t; return () => { clearTimeout(t); t = setTimeout(loadInvoices, 300) }
})()

onMounted(async () => {
  await Promise.all([loadInvoices(), loadClients(), loadStatistics(), loadServices()])
})
</script>

<style scoped>
/* ── Root layout ─────────────────────────────────────────────────────────── */
.inv-root {
  display: block;
  min-height: 100vh;
  background: #f8f7f4;
  font-family: 'DM Sans', 'Söhne', system-ui, sans-serif;
}

/* ── Sidebar tabs ────────────────────────────────────────────────────────── */

.top-nav {
  display: flex;
  gap: 12px;
  padding: 16px 24px;
  border-bottom: 1px solid #e8e8e0;
}

.inv-tab:hover { background: #2a2a2a; color: #e0dfd6; }
.inv-tab.active { background: #2e2e2e; color: #f0efe7; }
.inv-tab.active::before {
  content: '';
  position: absolute;
  left: 0; top: 50%; transform: translateY(-50%);
  width: 3px; height: 20px;
  background: #d4a853;
  border-radius: 0 3px 3px 0;
}

.tab-btn{position:relative;display:flex;align-items:center;gap:8px;border-radius:8px;padding:10px 18px;background:#1a1a1a;border:none;font-size:14px;color:#888;cursor:pointer;transition:color 0.15s}
.tab-btn.active{color:#f0efe7}
.tab-btn.active::after{content:'';position:absolute;bottom:-1px;left:18px;right:18px;height:2px;background:#d4a853;border-radius:2px 2px 0 0}
.badge{background:#3a3a3a;color:#888}
.tab-btn.active .badge{background:#d4a853;color:#1a1a1a}

.tab-icon { width: 16px; height: 16px; }
.tab-badge {
  margin-left: auto;
  background: #c0392b;
  color: white;
  font-size: 11px;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

/* ── Main area ───────────────────────────────────────────────────────────── */
.inv-main { flex: 1; overflow: auto; }
.inv-section { padding: 36px 40px; max-width: 1280px; }

/* ── Top bar ─────────────────────────────────────────────────────────────── */
.inv-topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 28px;
}
.inv-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  letter-spacing: -0.5px;
  margin: 0 0 4px;
  font-family: 'Playfair Display', Georgia, serif;
}
.inv-subtitle { font-size: 13px; color: #888; margin: 0; }

.btn-create {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #1a1a1a;
  color: #f0efe7;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-create:hover { background: #333; }
.btn-create svg { width: 16px; height: 16px; }

/* ── Stats row ───────────────────────────────────────────────────────────── */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}

/* ── Filter bar ──────────────────────────────────────────────────────────── */
.filter-bar {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #aaa; }
.filter-search {
  width: 100%;
  padding: 8px 12px 8px 32px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  color: #1a1a1a;
  outline: none;
  box-sizing: border-box;
}
.filter-search:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
.filter-select {
  padding: 8px 10px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  background: white;
  font-size: 13px;
  color: #333;
  outline: none;
  cursor: pointer;
}
.filter-select:focus { border-color: #d4a853; }
.filter-select-sm { padding: 5px 8px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 13px; }
.filter-toggle { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #555; cursor: pointer; }

/* ── Table ───────────────────────────────────────────────────────────────── */
.inv-table-wrap {
  background: white;
  border-radius: 12px;
  border: 1px solid #e8e8e0;
  overflow: hidden;
  margin-bottom: 16px;
}
.inv-table { width: 100%; border-collapse: collapse; }
.inv-table thead th {
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #f0f0e8;
  background: #fafaf7;
  text-align: left;
  white-space: nowrap;
}
.inv-table thead th.sortable { cursor: pointer; user-select: none; }
.inv-table thead th.sortable:hover { color: #444; }
.inv-table thead th.text-right { text-align: right; }
.inv-table tbody td {
  padding: 13px 16px;
  font-size: 14px;
  color: #333;
  border-bottom: 1px solid #f4f4ee;
  vertical-align: middle;
}
.inv-row { cursor: pointer; transition: background 0.1s; }
.inv-row:hover { background: #fafaf7; }
.inv-row:last-child td { border-bottom: none; }
.inv-num { font-weight: 600; color: #1a1a1a; font-variant-numeric: tabular-nums; }
.text-right { text-align: right; }
.text-danger { color: #c0392b; }
.font-med { font-weight: 500; }
.table-state { text-align: center; padding: 48px; color: #aaa; font-style: italic; }

/* ── Row actions ─────────────────────────────────────────────────────────── */
.row-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px;
  border: none; background: none; border-radius: 6px;
  color: #aaa; cursor: pointer; transition: all 0.15s;
}
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn svg { width: 15px; height: 15px; }

/* ── Pagination ──────────────────────────────────────────────────────────── */
.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn {
  width: 30px; height: 30px;
  border: 1px solid #e0e0d8; border-radius: 6px;
  background: white; color: #555; cursor: pointer; font-size: 16px;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
}
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-btn:hover:not(:disabled) { border-color: #d4a853; color: #d4a853; }
.pag-page { font-size: 13px; color: #555; }

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 900px) {
  .inv-tabs { width: 60px; padding: 20px 8px; }
  .inv-tab span:not(.tab-badge) { display: none; }
  .inv-section { padding: 20px; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>