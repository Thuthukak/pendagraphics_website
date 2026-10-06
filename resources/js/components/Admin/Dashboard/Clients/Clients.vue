<template>
  <div class="inv-root">
    <main class="inv-main">
      <section class="inv-section">
        <!-- Top bar -->
        <div class="inv-topbar">
          <div>
            <h1 class="inv-title">Customers</h1>
            <p class="inv-subtitle">{{ pagination.total }} customers total</p>
          </div>
          <button class="penda-btn penda-btn-primary" @click="openCreate">
            <font-awesome-icon :icon="['fas', 'plus']" /> New Customer
          </button>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
          <div class="search-wrap">
            <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
            <input
              v-model="filters.search"
              class="filter-search"
              placeholder="Search customer name or email…"
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Table -->
        <div class="inv-table-wrap">
          <table class="inv-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Tax Number</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="5" class="table-state">Loading…</td></tr>
              <tr v-else-if="!clients.length"><td colspan="5" class="table-state">No customers found</td></tr>
              <tr
                v-else
                v-for="client in clients"
                :key="client.id"
                class="inv-row"
                @click="viewClient(client)"
              >
                <td class="inv-num">{{ client.name }}</td>
                <td>{{ client.email || '—' }}</td>
                <td>{{ client.phone || '—' }}</td>
                <td>{{ client.tax_number || '—' }}</td>
                <td class="row-actions" @click.stop>
                  <button class="icon-btn" title="Edit" @click="editClient(client)"><font-awesome-icon :icon="['fas', 'pencil']" /></button>
                  <button class="icon-btn" title="Delete" @click="deleteClient(client)"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="pagination.last_page > 1">
          <span class="pag-info">Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}</span>
          <div class="pag-controls">
            <button class="pag-btn" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">‹</button>
            <span class="pag-page">Page {{ pagination.current_page }} / {{ pagination.last_page }}</span>
            <button class="pag-btn" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">›</button>
          </div>
        </div>
      </section>
    </main>

    <!-- Modals -->
    <ClientFormModal
      :show="showClientModal"
      :client="selectedClient"
      :is-editing="isEditing"
      @saved="handleSaved"
      @close="showClientModal = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useNotify } from '@/composables/useToast.js'
import axios from 'axios'
import ClientFormModal from './ClientFormModal.vue'

const notify = useNotify()

// ── State ────────────────────────────────────────────────────────────────────
const clients = ref([])
const loading = ref(false)

const showClientModal = ref(false)
const selectedClient  = ref(null)
const isEditing        = ref(false)

const filters    = reactive({ search: '' })
const pagination = reactive({ current_page: 1, per_page: 15, total: 0, last_page: 1, from: 0, to: 0 })

// ── Data loading ─────────────────────────────────────────────────────────────
async function loadClients() {
  loading.value = true
  try {
    const response = await axios.get('/api/clients', {
      params: { search: filters.search },
    })

    clients.value = response.data
    pagination.total = response.data.length
  } catch (e) {
    notify?.error?.('Failed to load customers')
  } finally {
    loading.value = false
  }
}

// ── Actions ──────────────────────────────────────────────────────────────────
function openCreate() {
  selectedClient.value = null
  isEditing.value = false
  showClientModal.value = true
}

function editClient(client) {
  selectedClient.value = client
  isEditing.value = true
  showClientModal.value = true
}

function viewClient(client) {
  editClient(client)
}

async function deleteClient(client) {
  if (!confirm(`Delete customer "${client.name}"?`)) return

  try {
    await axios.delete(`/api/clients/${client.id}`)
    notify?.success?.('Client deleted successfully')
    await loadClients()
  } catch (e) {
    notify?.error?.('Failed to delete customer')
  }
}

function handleSaved() {
  showClientModal.value = false
  loadClients()
}

function changePage(page) {
  pagination.current_page = page
  loadClients()
}

const debouncedSearch = (() => {
  let t
  return () => { clearTimeout(t); t = setTimeout(loadClients, 300) }
})()

onMounted(() => {
  loadClients()
})
</script>

<style scoped>
/* ── Root layout ─────────────────────────────────────────────────────────── */
.inv-root {
  display: block;
  min-height: 100vh;
  background: #f8f7f4;
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
}
.inv-subtitle { font-size: 13px; color: #888; margin: 0; }

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
.inv-num { font-weight: 600; color: #1a1a1a; }
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
  .inv-section { padding: 20px; }
}
</style>