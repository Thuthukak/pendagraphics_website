<template>
    <div>
        <!-- Top bar -->
        <div class="inv-topbar">
        <div>
            <h1 class="inv-title">Recurring Invoices</h1>
            <p class="inv-subtitle">Automated invoice schedules</p>
        </div>
        <button class="btn-create" @click="openCreate">
            <font-awesome-icon icon="plus" /> New Schedule
        </button>
        </div>

        <!-- Schedule list -->
        <div class="sched-grid">
        <div v-if="loading" class="sched-empty">Loading schedules…</div>
        <div v-else-if="!schedules.length" class="sched-empty">
            No recurring invoices yet. Create one to automate your billing.
        </div>

        <div
            v-else
            v-for="s in schedules"
            :key="s.id"
            class="sched-card"
            :class="{ paused: !s.is_active }"
        >
            <div class="sched-card-top">
            <div class="sched-name-wrap">
                <span class="sched-active-dot" :class="s.is_active ? 'active' : 'paused'"></span>
                <span class="sched-name">{{ s.name }}</span>
            </div>
            <div class="sched-actions">
                <button class="icon-btn" title="Edit" @click="openEdit(s)">
                    <font-awesome-icon :icon="['fas', 'pencil']" />
                </button>
                <button
                    class="icon-btn"
                    :title="s.is_active ? 'Pause' : 'Activate'"
                    @click="toggleActive(s)"
                >
                    <font-awesome-icon :icon="['fas', s.is_active ? 'pause' : 'play']" />
                </button>
                <button class="icon-btn danger" title="Delete" @click="deleteSchedule(s)">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </div>
            </div>

            <div class="sched-meta">
            <span class="sched-client">{{ s.client?.name }}</span>
            <span class="sched-divider">·</span>
            <span class="sched-freq">{{ s.schedule_description }}</span>
            </div>

            <div class="sched-amount">
            R {{ totalItems(s.items) }}
            <span class="sched-due-label">/ invoice</span>
            </div>

            <div class="sched-footer">
            <div class="sched-next">
                <span class="sched-label">Next run</span>
                <span class="sched-value" :class="{ overdue: isPast(s.next_run_date) && s.is_active }">
                {{ fmtDate(s.next_run_date) }}
                </span>
            </div>
            <div class="sched-next">
                <span class="sched-label">Action</span>
                <span class="sched-value">{{ actionLabel(s.action_on_create) }}</span>
            </div>
            <div class="sched-next" v-if="s.occurrences_limit">
                <span class="sched-label">Runs</span>
                <span class="sched-value">{{ s.occurrences_count }} / {{ s.occurrences_limit }}</span>
            </div>

            <button class="btn-logs" @click="viewLogs(s)">View logs</button>
            </div>
        </div>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="pagination.total > pagination.per_page">
        <span class="pag-info">{{ pagination.total }} schedules</span>
        <div class="pag-controls">
            <button class="pag-btn" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">‹</button>
            <span class="pag-page">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
            <button class="pag-btn" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">›</button>
        </div>
        </div>

        <!-- Create / Edit modal -->
        <RecurringInvoiceFormModal
        :show="showForm"
        :is-editing="isEditing"
        :schedule="selectedSchedule"
        :clients="clients"
        :services="services"
        :saving="saving"
        @close="closeModal"
        @save="handleSave"
        />

        <!-- Logs drawer -->
        <RecurringLogsModal
        :show="showLogs"
        :schedule="selectedSchedule"
        @close="showLogs = false"
        />

    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import RecurringInvoiceFormModal from './RecurringInvoiceFormModal.vue'
import RecurringLogsModal        from './RecurringLogsModal.vue'

const props = defineProps({ clients: Array, services: Array })

const schedules        = ref([])
const loading          = ref(false)
const saving           = ref(false)
const showForm         = ref(false)
const showLogs         = ref(false)
const isEditing        = ref(false)
const selectedSchedule = ref(null)
const pagination = reactive({ current_page: 1, per_page: 12, total: 0, last_page: 1 })

// ── API helper ────────────────────────────────────────────────────────────────

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function api(method, path, data) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrf(),
        },
    }
    // Only attach a body for methods that support it
    if (data !== undefined && !['GET', 'HEAD'].includes(method.toUpperCase())) {
        options.body = JSON.stringify(data)
    }

    const res = await fetch('/api' + path, options)

    // Handle empty responses (204 No Content) safely
    const json = res.status !== 204 ? await res.json() : {}

    if (!res.ok) throw json
    return json
}

// ── Data loading ──────────────────────────────────────────────────────────────

async function load() {
    loading.value = true
    try {
        const qs = new URLSearchParams({ page: pagination.current_page, per_page: pagination.per_page })
        // Laravel paginate() returns: { data, current_page, last_page, total, per_page, ... }
        const res = await api('GET', `/recurring-invoices?${qs}`)
        schedules.value = res.data
        Object.assign(pagination, {
            total: res.total,
            last_page: res.last_page,
            current_page: res.current_page,
        })
    } catch (err) {
        console.error('Failed to load schedules:', err)
    } finally {
        loading.value = false
    }
}

// ── Modal controls ────────────────────────────────────────────────────────────

function openCreate() {
    isEditing.value = false
    selectedSchedule.value = null
    showForm.value = true
}

function openEdit(s) {
    isEditing.value = true
    selectedSchedule.value = s
    showForm.value = true
}

function viewLogs(s) {
    selectedSchedule.value = s
    showLogs.value = true
}

function closeModal() {
    showForm.value = false
    selectedSchedule.value = null
}

// ── CRUD ──────────────────────────────────────────────────────────────────────

async function handleSave(data) {
    saving.value = true
    try {
        if (isEditing.value) {
            // Controller: PUT /api/recurring-invoices/{id}  → update()
            await api('PUT', `/recurring-invoices/${selectedSchedule.value.id}`, data)
        } else {
            // Controller: POST /api/recurring-invoices  → store()
            await api('POST', '/recurring-invoices', data)
        }
        closeModal()
        await load()
    } catch (err) {
        console.error('Save failed:', err)
        // Surface validation errors to user if available
        if (err?.message) alert(err.message)
    } finally {
        saving.value = false
    }
}

async function toggleActive(s) {
    try {
        // Controller: PATCH /api/recurring-invoices/{id}/toggle-active → toggleActive()
        const res = await api('PATCH', `/recurring-invoices/${s.id}/toggle-active`)
        // Optimistically update the card without a full reload
        s.is_active = res.is_active
    } catch (err) {
        console.error('Toggle failed:', err)
        await load() // Fall back to reload on error
    }
}

async function deleteSchedule(s) {
    if (!confirm(`Delete "${s.name}"? This won't remove already-generated invoices.`)) return
    try {
        // Controller: DELETE /api/recurring-invoices/{id}  → destroy()  (soft delete, returns 200 JSON)
        await api('DELETE', `/recurring-invoices/${s.id}`)
        await load()
    } catch (err) {
        console.error('Delete failed:', err)
    }
}

function changePage(p) {
    pagination.current_page = p
    load()
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function totalItems(items) {
    const t = (items ?? []).reduce(
        (sum, i) => sum + parseFloat(i.unit_price ?? 0) * parseFloat(i.quantity ?? 0),
        0
    )
    return t.toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function fmtDate(d) {
    return d
        ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' })
        : '—'
}

function isPast(d) {
    return d && new Date(d) < new Date()
}

function actionLabel(a) {
    return (
        { draft: 'Create draft', send: 'Auto-send', send_if_valid: 'Send if email' }[a] ?? a
    )
}

onMounted(load)
</script>

<style scoped>
.inv-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
.inv-title { font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; margin: 0 0 4px; font-family: 'Playfair Display', Georgia, serif; }
.inv-subtitle { font-size: 13px; color: #888; margin: 0; }
.btn-create { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #1a1a1a; color: #f0efe7; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
.btn-create:hover { background: #333; }
.btn-create svg { width: 16px; height: 16px; }

/* Schedule cards */
.sched-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-bottom: 20px; }
.sched-empty { color: #aaa; font-style: italic; padding: 40px 0; }

.sched-card {
  background: white;
  border: 1px solid #e8e8e0;
  border-radius: 12px;
  padding: 20px;
  transition: box-shadow 0.15s;
}
.sched-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
.sched-card.paused { opacity: 0.6; }

.sched-card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.sched-name-wrap { display: flex; align-items: center; gap: 8px; min-width: 0; }
.sched-active-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sched-active-dot.active { background: #27ae60; }
.sched-active-dot.paused { background: #bbb; }
.sched-name { font-weight: 600; font-size: 15px; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.sched-actions { display: flex; gap: 4px; }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: none; background: none; border-radius: 6px; color: #aaa; cursor: pointer; transition: all 0.15s; }
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn.danger:hover { background: #fef0ee; color: #c0392b; }
.icon-btn svg { width: 14px; height: 14px; }

.sched-meta { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #888; margin-bottom: 12px; }
.sched-client { font-weight: 500; color: #555; }
.sched-divider { color: #ccc; }
.sched-freq { color: #888; }

.sched-amount { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 16px; }
.sched-due-label { font-size: 13px; font-weight: 400; color: #aaa; }

.sched-footer { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; border-top: 1px solid #f0f0e8; padding-top: 14px; }
.sched-next { display: flex; flex-direction: column; gap: 2px; }
.sched-label { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
.sched-value { font-size: 13px; font-weight: 500; color: #444; }
.sched-value.overdue { color: #c0392b; }

.btn-logs { margin-left: auto; font-size: 12px; color: #888; background: none; border: 1px solid #e0e0d8; border-radius: 6px; padding: 4px 10px; cursor: pointer; transition: all 0.15s; }
.btn-logs:hover { border-color: #999; color: #444; }

/* Pagination */
.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn { width: 30px; height: 30px; border: 1px solid #e0e0d8; border-radius: 6px; background: white; color: #555; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-page { font-size: 13px; color: #555; }
</style>