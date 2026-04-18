<template>
    <Teleport to="body">
        <div v-if="show && schedule" class="modal-backdrop" @click.self="$emit('close')">
        <div class="modal">
            <!-- Header -->
            <div class="modal-hd">
            <div>
                <h2>Run History</h2>
                <p class="modal-subtitle">{{ schedule.name }}</p>
            </div>
            <button class="modal-close" @click="$emit('close')">×</button>
            </div>

            <!-- Schedule summary strip -->
            <div class="sched-strip">
            <div class="strip-item">
                <span class="strip-label">Frequency</span>
                <span class="strip-value">{{ schedule.schedule_description }}</span>
            </div>
            <div class="strip-item">
                <span class="strip-label">Next run</span>
                <span class="strip-value" :class="{ danger: isPast(schedule.next_run_date) && schedule.is_active }">
                {{ fmtDate(schedule.next_run_date) }}
                </span>
            </div>
            <div class="strip-item">
                <span class="strip-label">Total runs</span>
                <span class="strip-value">{{ schedule.occurrences_count ?? 0 }}</span>
            </div>
            <div class="strip-item">
                <span class="strip-label">Status</span>
                <span class="strip-value">
                <span class="active-dot" :class="schedule.is_active ? 'active' : 'paused'"></span>
                {{ schedule.is_active ? 'Active' : 'Paused' }}
                </span>
            </div>
            </div>

            <!-- Body -->
            <div class="modal-body">
            <!-- Loading -->
            <div v-if="loading" class="state-msg">Loading logs…</div>

            <!-- Empty -->
            <div v-else-if="!logs.length" class="state-msg">
                <div class="empty-icon">📋</div>
                <p>No runs yet. This schedule will generate its first invoice on {{ fmtDate(schedule.next_run_date) }}.</p>
            </div>

            <!-- Log list -->
            <div v-else class="log-list">
                <div
                v-for="log in logs"
                :key="log.id"
                class="log-entry"
                :class="log.status"
                >
                <!-- Status indicator -->
                <div class="log-status-col">
                    <span class="log-dot" :class="log.status"></span>
                </div>

                <!-- Main content -->
                <div class="log-content">
                    <div class="log-top">
                    <span class="log-ran-at">{{ fmtDateTime(log.ran_at) }}</span>
                    <span class="log-badge" :class="log.status">{{ statusLabel(log.status) }}</span>
                    </div>

                    <div class="log-message">{{ log.message }}</div>

                    <!-- Invoice link if one was created -->
                    <div v-if="log.invoice" class="log-invoice">
                    <span class="log-inv-label">Invoice</span>
                    <span class="log-inv-num">{{ log.invoice.invoice_number }}</span>
                    <span class="log-inv-sep">·</span>
                    <span class="log-inv-total">R {{ fmt(log.invoice.total) }}</span>
                    <span class="log-inv-sep">·</span>
                    <span class="log-inv-status" :class="log.invoice.status">{{ log.invoice.status }}</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="log-pagination" v-if="pagination.last_page > 1">
                <button class="pag-btn" :disabled="pagination.current_page <= 1" @click="loadLogs(pagination.current_page - 1)">‹</button>
                <span class="pag-page">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
                <button class="pag-btn" :disabled="pagination.current_page >= pagination.last_page" @click="loadLogs(pagination.current_page + 1)">›</button>
            </div>
            </div>

            <!-- Footer -->
            <div class="modal-ft">
            <span class="log-count" v-if="pagination.total > 0">{{ pagination.total }} total run{{ pagination.total !== 1 ? 's' : '' }}</span>
            <button class="btn-secondary" @click="$emit('close')">Close</button>
            </div>
        </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  show:     { type: Boolean, default: false },
  schedule: { type: Object,  default: null  },
})
defineEmits(['close'])

const logs    = ref([])
const loading = ref(false)

const pagination = reactive({
  current_page: 1,
  last_page:    1,
  total:        0,
  per_page:     20,
})

async function loadLogs(page = 1) {
  if (!props.schedule) return
  loading.value = true
  try {
    const res = await fetch(
      `/api/recurring-invoices/${props.schedule.id}/logs?page=${page}&per_page=${pagination.per_page}`,
      { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    )
    if (!res.ok) throw new Error('Failed to load logs')
    const data = await res.json()
    logs.value = data.data
    Object.assign(pagination, {
      current_page: data.current_page,
      last_page:    data.last_page,
      total:        data.total,
    })
  } catch (e) {
    console.error('Error loading logs:', e)
    logs.value = []
  } finally {
    loading.value = false
  }
}

watch(
  () => props.show,
  (v) => {
    if (v) {
      pagination.current_page = 1
      loadLogs(1)
    } else {
      logs.value = []
    }
  }
)

// ── Helpers ───────────────────────────────────────────────────────────────────
function statusLabel(s) {
  return { created: 'Created', sent: 'Sent', failed: 'Failed' }[s] ?? s
}

function fmt(v) {
  return (parseFloat(v) || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function fmtDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' })
}

function fmtDateTime(d) {
  if (!d) return '—'
  return new Date(d).toLocaleString('en-ZA', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function isPast(d) {
  return d && new Date(d) < new Date()
}
</script>

<style scoped>
/* ── Backdrop & shell ────────────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: flex-end;
  z-index: 9200; padding: 0;
}

.modal {
  background: white;
  width: 100%; max-width: 520px; height: 100vh;
  display: flex; flex-direction: column;
  box-shadow: -8px 0 40px rgba(0,0,0,0.12);
  animation: slide-in 0.22s ease;
}

@keyframes slide-in {
  from { transform: translateX(40px); opacity: 0; }
  to   { transform: translateX(0);    opacity: 1; }
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.modal-hd {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: 24px 24px 16px;
  border-bottom: 1px solid #f0f0e8;
  flex-shrink: 0;
}
.modal-hd h2 {
  font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 3px;
  font-family: 'Playfair Display', Georgia, serif;
}
.modal-subtitle { font-size: 13px; color: #888; margin: 0; }
.modal-close {
  background: none; border: none; font-size: 22px;
  color: #aaa; cursor: pointer; line-height: 1; flex-shrink: 0;
}
.modal-close:hover { color: #333; }

/* ── Schedule strip ──────────────────────────────────────────────────────── */
.sched-strip {
  display: flex; gap: 0;
  background: #fafaf7; border-bottom: 1px solid #f0f0e8;
  flex-shrink: 0;
}
.strip-item {
  flex: 1; display: flex; flex-direction: column; gap: 3px;
  padding: 12px 16px;
  border-right: 1px solid #f0f0e8;
}
.strip-item:last-child { border-right: none; }
.strip-label { font-size: 10px; color: #bbb; text-transform: uppercase; letter-spacing: 0.6px; }
.strip-value {
  font-size: 13px; font-weight: 500; color: #333;
  display: flex; align-items: center; gap: 5px;
}
.strip-value.danger { color: #c0392b; }

.active-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.active-dot.active { background: #27ae60; }
.active-dot.paused { background: #bbb; }

/* ── Body ────────────────────────────────────────────────────────────────── */
.modal-body { flex: 1; overflow-y: auto; padding: 20px 24px; }

.state-msg {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; text-align: center;
  padding: 48px 24px; color: #aaa; font-size: 14px;
  gap: 12px;
}
.empty-icon { font-size: 32px; }
.state-msg p { margin: 0; line-height: 1.6; }

/* ── Log list ────────────────────────────────────────────────────────────── */
.log-list { display: flex; flex-direction: column; }

.log-entry {
  display: flex; gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid #f4f4ee;
  position: relative;
}
.log-entry:last-child { border-bottom: none; }

/* Vertical timeline line */
.log-entry:not(:last-child) .log-status-col::after {
  content: '';
  position: absolute;
  left: 7px; top: 30px; bottom: -14px;
  width: 1px; background: #f0f0e8;
}

.log-status-col { position: relative; flex-shrink: 0; padding-top: 2px; }

.log-dot {
  display: block; width: 14px; height: 14px;
  border-radius: 50%; border: 2px solid white;
  box-shadow: 0 0 0 1.5px currentColor;
}
.log-dot.created { color: #27ae60; background: #27ae60; }
.log-dot.sent    { color: #2980b9; background: #2980b9; }
.log-dot.failed  { color: #c0392b; background: #c0392b; }

.log-content { flex: 1; min-width: 0; }

.log-top {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 5px;
}
.log-ran-at { font-size: 13px; color: #555; font-weight: 500; }

.log-badge {
  font-size: 11px; font-weight: 600;
  padding: 2px 8px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: 0.4px;
}
.log-badge.created { background: #e8f7ee; color: #166534; }
.log-badge.sent    { background: #e8f0fb; color: #1e40af; }
.log-badge.failed  { background: #fee8e8; color: #991b1b; }

.log-message { font-size: 13px; color: #777; margin-bottom: 6px; }

.log-invoice {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; background: #fafaf7;
  padding: 5px 10px; border-radius: 6px;
  border: 1px solid #f0f0e8;
  width: fit-content;
}
.log-inv-label { color: #aaa; }
.log-inv-num   { font-weight: 600; color: #1a1a1a; }
.log-inv-total { color: #555; font-weight: 500; }
.log-inv-sep   { color: #ccc; }

.log-inv-status {
  font-size: 11px; font-weight: 600; padding: 1px 7px;
  border-radius: 10px; text-transform: uppercase; letter-spacing: 0.3px;
}
.log-inv-status.draft     { background: #f5f0e8; color: #8a6a1a; }
.log-inv-status.sent      { background: #e8f0fb; color: #1e40af; }
.log-inv-status.paid      { background: #e8f7ee; color: #166534; }
.log-inv-status.overdue   { background: #fee8e8; color: #991b1b; }
.log-inv-status.cancelled { background: #f5f5f5; color: #888; }

/* ── Pagination ──────────────────────────────────────────────────────────── */
.log-pagination {
  display: flex; align-items: center; justify-content: center;
  gap: 12px; padding-top: 20px;
}
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

/* ── Footer ──────────────────────────────────────────────────────────────── */
.modal-ft {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 24px; border-top: 1px solid #f0f0e8;
  background: #fafaf7; flex-shrink: 0;
}
.log-count { font-size: 13px; color: #aaa; }
.btn-secondary {
  padding: 9px 20px; background: white; color: #555;
  border: 1px solid #e0e0d8; border-radius: 8px;
  font-size: 14px; cursor: pointer; transition: all 0.15s;
}
.btn-secondary:hover { border-color: #999; color: #1a1a1a; }
</style>