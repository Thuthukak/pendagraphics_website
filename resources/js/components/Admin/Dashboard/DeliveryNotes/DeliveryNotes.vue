<template>
  <div class="dn-root">
    <section class="dn-section">
      <!-- Top bar -->
      <div class="dn-topbar">
        <div>
          <h1 class="dn-title">Delivery Notes</h1>
          <p class="dn-subtitle">{{ pagination.total }} delivery notes total</p>
        </div>
        <button class="penda-btn penda-btn-primary" @click="openCreate">
          <font-awesome-icon :icon="['fas', 'plus']" /> New Delivery Note
        </button>
      </div>

      <!-- Stats row -->
      <div class="stats-row" v-if="statistics">
        <StatCard label="Draft"     :value="statistics.draft_delivery_notes"     accent="gray" />
        <StatCard label="Pending"   :value="statistics.pending_delivery_notes"   accent="amber" />
        <StatCard label="Delivered" :value="statistics.delivered_delivery_notes" accent="teal" />
        <StatCard label="Cancelled" :value="statistics.cancelled_delivery_notes" accent="red" />
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <div class="search-wrap">
          <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
          <input
            v-model="filters.search"
            class="filter-search"
            placeholder="Search delivery # or client…"
            @input="debouncedSearch"
          />
        </div>

        <select v-model="filters.status" class="filter-select" @change="loadDeliveryNotes">
          <option value="">All statuses</option>
          <option value="draft">Draft</option>
          <option value="pending">Pending</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <select v-model="filters.client_id" class="filter-select" @change="loadDeliveryNotes">
          <option value="">All clients</option>
          <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>

        <input v-model="filters.date_from" type="date" class="filter-select" @change="loadDeliveryNotes" />
        <input v-model="filters.date_to"   type="date" class="filter-select" @change="loadDeliveryNotes" />

        <label class="filter-toggle">
          <input v-model="filters.pending_only" type="checkbox" @change="loadDeliveryNotes" />
          <span>Pending only</span>
        </label>
      </div>

      <!-- Table -->
      <div class="dn-table-wrap">
        <table class="dn-table">
          <thead>
            <tr>
              <th @click="sort('delivery_number')" class="sortable">Delivery #</th>
              <th>Client</th>
              <th @click="sort('delivery_date')" class="sortable">Date</th>
              <th>Source</th>
              <th>Items</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading"><td colspan="7" class="table-state">Loading…</td></tr>
            <tr v-else-if="!deliveryNotes.length"><td colspan="7" class="table-state">No delivery notes found</td></tr>
            <tr
              v-else
              v-for="dn in deliveryNotes"
              :key="dn.id"
              class="dn-row"
              @click="viewDeliveryNote(dn)"
            >
              <td class="dn-num">{{ dn.delivery_number }}</td>
              <td>{{ dn.client?.name }}</td>
              <td>{{ fmtDate(dn.delivery_date) }}</td>
              <td>
                <span v-if="dn.source" class="source-chip">
                  {{ dn.source.type === 'invoice' ? 'Inv' : 'Est' }} {{ dn.source.number }}
                </span>
                <span v-else class="source-chip source-chip-standalone">Standalone</span>
              </td>
              <td>{{ dn.items_count ?? '—' }}</td>
              <td class="text-dark capitalize">{{ dn.status }}</td>
              <td class="row-actions" @click.stop>
                <button class="icon-btn" title="Edit" @click="editDeliveryNote(dn)"><font-awesome-icon :icon="['fas', 'pencil']" /></button>
                <DeliveryNoteRowMenu
                  :delivery-note="dn"
                  @dispatch="dispatchDeliveryNote"
                  @deliver="openDeliverModal"
                  @cancel="cancelDeliveryNote"
                  @export="exportDeliveryNote"
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
          <select v-model="pagination.per_page" class="filter-select-sm" @change="loadDeliveryNotes">
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

    <!-- Modals -->
    <DeliveryNoteFormModal
      :show="showFormModal"
      :is-editing="isEditing"
      :delivery-note="selectedDeliveryNote"
      :clients="clients"
      :services="services"
      :saving="saving"
      @close="closeModals"
      @save="handleSave"
    />

    <DeliveryNoteViewModal
      :show="showViewModal"
      :delivery-note="selectedDeliveryNote"
      @close="closeModals"
    />

    <!-- Lightweight inline confirm for "mark delivered" — captures received_by/date -->
    <div v-if="showDeliverModal" class="dn-overlay" @click.self="closeModals">
      <div class="dn-modal">
        <h3>Mark as Delivered</h3>
        <p class="dn-modal-sub">{{ selectedDeliveryNote?.delivery_number }}</p>
        <label class="dn-field">
          <span>Received by</span>
          <input v-model="deliverForm.received_by" type="text" placeholder="Name of person who received it" />
        </label>
        <label class="dn-field">
          <span>Received date</span>
          <input v-model="deliverForm.received_date" type="date" />
        </label>
        <div class="dn-modal-actions">
          <button class="penda-btn" @click="closeModals">Cancel</button>
          <button class="penda-btn penda-btn-primary" @click="confirmDeliver">Confirm Delivery</button>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="dn-overlay" @click.self="closeModals">
      <div class="dn-modal">
        <h3>Delete Delivery Note?</h3>
        <p class="dn-modal-sub">{{ selectedDeliveryNote?.delivery_number }} will be permanently deleted.</p>
        <div class="dn-modal-actions">
          <button class="penda-btn" @click="closeModals">Cancel</button>
          <button class="penda-btn penda-btn-danger" @click="confirmDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import DeliveryNoteFormModal from './DeliveryNoteFormModal.vue'
import DeliveryNoteViewModal from './DeliveryNoteViewModal.vue'
import StatCard          from '../Invoices/partials/StatCard.vue'
import DeliveryNoteRowMenu from './DeliveryNoteRowMenu.vue'
import { useNotify } from '@/composables/useToast.js'

const notify = useNotify()

const deliveryNotes = ref([])
const clients        = ref([])
const services        = ref([])
const statistics       = ref(null)
const loading           = ref(false)
const saving            = ref(false)

const showFormModal    = ref(false)
const showViewModal    = ref(false)
const showDeliverModal = ref(false)
const showDeleteModal  = ref(false)
const isEditing         = ref(false)
const selectedDeliveryNote = ref(null)

const deliverForm = reactive({ received_by: '', received_date: new Date().toISOString().slice(0, 10) })

const filters    = reactive({ search: '', status: '', client_id: '', date_from: '', date_to: '', pending_only: false })
const sorting    = reactive({ sort_by: 'created_at', sort_order: 'desc' })
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

async function loadDeliveryNotes() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      ...filters,
      ...sorting,
      page:         pagination.current_page,
      per_page:     pagination.per_page,
      pending_only: filters.pending_only ? '1' : '',
    }).toString()
    const res = await api('GET', `/delivery-notes?${params}`)
    deliveryNotes.value = res.data
    Object.assign(pagination, {
      current_page: res.current_page,
      total:        res.total,
      last_page:    res.last_page,
      from:         res.from,
      to:           res.to,
    })
  } finally { loading.value = false }
}

async function loadClients()    { clients.value = await api('GET', '/invoices/clients') }
async function loadServices()   { try { services.value = await api('GET', '/services') } catch {} }
async function loadStatistics() {
  const res = await api('GET', '/delivery-notes/statistics')
  statistics.value = res.statistics
}

function openCreate() { isEditing.value = false; selectedDeliveryNote.value = null; showFormModal.value = true }

async function viewDeliveryNote(dn) {
  try {
    const res = await api('GET', `/delivery-notes/${dn.id}`)
    selectedDeliveryNote.value = res.delivery_note
    showViewModal.value = true
  } catch (err) {
    notify.error(err?.message ?? 'Failed to load delivery note.')
  }
}

function editDeliveryNote(dn) { selectedDeliveryNote.value = dn; isEditing.value = true; showFormModal.value = true }
function openDelete(dn)       { selectedDeliveryNote.value = dn; showDeleteModal.value = true }

function openDeliverModal(dn) {
  selectedDeliveryNote.value = dn
  deliverForm.received_by = ''
  deliverForm.received_date = new Date().toISOString().slice(0, 10)
  showDeliverModal.value = true
}

async function confirmDeliver() {
  try {
    await api('POST', `/delivery-notes/${selectedDeliveryNote.value.id}/deliver`, { ...deliverForm })
    closeModals()
    await Promise.all([loadDeliveryNotes(), loadStatistics()])
    notify.success('Delivery note marked as delivered.')
  } catch (err) {
    notify.error(err?.message ?? 'Failed to mark as delivered.')
  }
}

async function dispatchDeliveryNote(dn) {
  try {
    await api('POST', `/delivery-notes/${dn.id}/dispatch`)
    await Promise.all([loadDeliveryNotes(), loadStatistics()])
    notify.success('Delivery note marked as dispatched.')
  } catch (err) {
    notify.error(err?.message ?? 'Failed to dispatch delivery note.')
  }
}

async function cancelDeliveryNote(dn) {
  try {
    await api('POST', `/delivery-notes/${dn.id}/cancel`)
    await Promise.all([loadDeliveryNotes(), loadStatistics()])
    notify.success('Delivery note cancelled.')
  } catch (err) {
    notify.error(err?.message ?? 'Failed to cancel delivery note.')
  }
}

function exportDeliveryNote(dn) {
  window.open(`/api/delivery-notes/${dn.id}/export`, '_blank')
}

async function confirmDelete() {
  try {
    await api('DELETE', `/delivery-notes/${selectedDeliveryNote.value.id}`)
    closeModals()
    await Promise.all([loadDeliveryNotes(), loadStatistics()])
    notify.success('Delivery note deleted.')
  } catch (err) {
    notify.error(err?.message ?? 'Failed to delete delivery note.')
  }
}

async function handleSave(formData) {
  saving.value = true
  try {
    if (isEditing.value) {
      await api('PUT', `/delivery-notes/${selectedDeliveryNote.value.id}`, formData)
      notify.success('Delivery note updated successfully.')
    } else {
      await api('POST', '/delivery-notes', formData)
      notify.success('Delivery note created successfully.')
    }
    closeModals()
    await Promise.all([loadDeliveryNotes(), loadStatistics()])
  } catch (err) {
    notify.error(err?.message ?? 'Failed to save delivery note. Please check your inputs.')
  } finally {
    saving.value = false
  }
}

function closeModals() {
  showFormModal.value    = false
  showViewModal.value    = false
  showDeliverModal.value = false
  showDeleteModal.value  = false
  selectedDeliveryNote.value = null
}

function sort(field) {
  if (sorting.sort_by === field) sorting.sort_order = sorting.sort_order === 'asc' ? 'desc' : 'asc'
  else { sorting.sort_by = field; sorting.sort_order = 'asc' }
  loadDeliveryNotes()
}

function changePage(page) { pagination.current_page = page; loadDeliveryNotes() }

function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—' }

const debouncedSearch = (() => {
  let t; return () => { clearTimeout(t); t = setTimeout(loadDeliveryNotes, 300) }
})()

onMounted(async () => {
  await Promise.all([loadDeliveryNotes(), loadClients(), loadStatistics(), loadServices()])
})
</script>

<style scoped>
.dn-root { display: block; min-height: 100vh; background: #f8f7f4; }
.dn-section { padding: 36px 40px; max-width: 1280px; }

.dn-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
.dn-title { font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; margin: 0 0 4px; }
.dn-subtitle { font-size: 13px; color: #888; margin: 0; }

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }

.filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #aaa; }
.filter-search { width: 100%; padding: 8px 12px 8px 32px; border: 1px solid #e0e0d8; border-radius: 8px; background: white; font-size: 14px; color: #1a1a1a; outline: none; box-sizing: border-box; }
.filter-search:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
.filter-select { padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 8px; background: white; font-size: 13px; color: #333; outline: none; cursor: pointer; }
.filter-select-sm { padding: 5px 8px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 13px; }
.filter-toggle { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #555; cursor: pointer; }

.dn-table-wrap { background: white; border-radius: 12px; border: 1px solid #e8e8e0; overflow: hidden; margin-bottom: 16px; }
.dn-table { width: 100%; border-collapse: collapse; }
.dn-table thead th { padding: 12px 16px; font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f0f0e8; background: #fafaf7; text-align: left; white-space: nowrap; }
.dn-table thead th.sortable { cursor: pointer; user-select: none; }
.dn-table thead th.sortable:hover { color: #444; }
.dn-table tbody td { padding: 13px 16px; font-size: 14px; color: #333; border-bottom: 1px solid #f4f4ee; vertical-align: middle; }
.dn-row { cursor: pointer; transition: background 0.1s; }
.dn-row:hover { background: #fafaf7; }
.dn-row:last-child td { border-bottom: none; }
.dn-num { font-weight: 600; color: #1a1a1a; font-variant-numeric: tabular-nums; }
.table-state { text-align: center; padding: 48px; color: #aaa; font-style: italic; }

.source-chip { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 12px; background: #f0efe7; color: #555; }
.source-chip-standalone { background: #f4f4ee; color: #999; }

.row-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: none; background: none; border-radius: 6px; color: #aaa; cursor: pointer; transition: all 0.15s; }
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn svg { width: 15px; height: 15px; }

.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn { width: 30px; height: 30px; border: 1px solid #e0e0d8; border-radius: 6px; background: white; color: #555; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-btn:hover:not(:disabled) { border-color: #d4a853; color: #d4a853; }
.pag-page { font-size: 13px; color: #555; }

/* Inline confirm modals */
.dn-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 100; }
.dn-modal { background: white; border-radius: 12px; padding: 24px; width: 360px; max-width: 90vw; }
.dn-modal h3 { margin: 0 0 4px; font-size: 17px; color: #1a1a1a; }
.dn-modal-sub { margin: 0 0 16px; font-size: 13px; color: #888; }
.dn-field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 14px; font-size: 13px; color: #555; }
.dn-field input { padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; outline: none; }
.dn-field input:focus { border-color: #d4a853; }
.dn-modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px; }

@media (max-width: 900px) {
  .dn-section { padding: 20px; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>