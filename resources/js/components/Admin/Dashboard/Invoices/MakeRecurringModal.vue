<template>
    <Teleport to="body">
        <div v-if="show" class="modal-backdrop" @click.self="$emit('close')">
        <div class="modal">

            <div class="modal-hd">
            <div>
                <h2>Make Recurring</h2>
                <p class="modal-sub">{{ invoice?.invoice_number }} · {{ invoice?.client?.name }}</p>
            </div>
            <button class="modal-close" @click="$emit('close')">×</button>
            </div>

            <div class="modal-body">

            <!-- Preview of what will be copied -->
            <div class="source-preview">
                <span class="source-label">Line items that will repeat</span>
                <div class="source-items">
                <div v-for="item in invoice?.items" :key="item.id" class="source-item">
                    <span class="source-desc">{{ item.description }}</span>
                    <span class="source-amt">R {{ fmtAmt(item.quantity * item.unit_price) }}</span>
                </div>
                </div>
                <div class="source-total">
                <span>Total per invoice</span>
                <strong>R {{ invoiceTotal }}</strong>
                </div>
            </div>

            <!-- Schedule name -->
            <div class="form-group">
                <label>Schedule Name *</label>
                <input
                v-model="form.name"
                type="text"
                class="form-input"
                :placeholder="`e.g. Monthly — ${invoice?.client?.name}`"
                />
            </div>

            <!-- Frequency row -->
            <div class="form-row">
                <div class="form-group">
                <label>Frequency *</label>
                <select v-model="form.frequency" class="form-input">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="annually">Annually</option>
                    <option value="custom">Custom</option>
                </select>
                </div>

                <div class="form-group" v-if="form.frequency === 'custom'">
                <label>Every</label>
                <div style="display:flex;gap:8px">
                    <input v-model.number="form.interval" type="number" min="1" class="form-input" style="width:72px" />
                    <select v-model="form.interval_unit" class="form-input">
                    <option value="day">Day(s)</option>
                    <option value="week">Week(s)</option>
                    <option value="month">Month(s)</option>
                    <option value="year">Year(s)</option>
                    </select>
                </div>
                </div>
            </div>

            <!-- Start / end dates -->
            <div class="form-row">
                <div class="form-group">
                <label>First Run Date *</label>
                <input v-model="form.start_date" type="date" class="form-input" />
                </div>
                <div class="form-group">
                <label>End Date <span class="opt">(optional)</span></label>
                <input v-model="form.end_date" type="date" class="form-input" />
                </div>
            </div>

            <!-- Max occurrences -->
            <div class="form-group">
                <label>Max Occurrences <span class="opt">(optional — blank = unlimited)</span></label>
                <input v-model.number="form.occurrences_limit" type="number" min="1" class="form-input" placeholder="e.g. 12" />
            </div>

            <!-- Action on create -->
            <div class="form-group">
                <label>When each invoice is generated…</label>
                <div class="radio-cards">
                <label
                    v-for="opt in actionOptions"
                    :key="opt.value"
                    class="radio-card"
                    :class="{ selected: form.action_on_create === opt.value }"
                >
                    <input type="radio" v-model="form.action_on_create" :value="opt.value" />
                    <div>
                    <span class="radio-title">{{ opt.label }}</span>
                    <span class="radio-desc">{{ opt.desc }}</span>
                    </div>
                </label>
                </div>
            </div>

            <!-- Notify admin toggle -->
            <div class="toggle-row">
                <span>Notify me when an invoice is generated</span>
                <div class="toggle-switch" :class="{ on: form.notify_admin }" @click="form.notify_admin = !form.notify_admin">
                <div class="toggle-thumb"></div>
                </div>
            </div>

            <!-- Preview dates -->
            <div class="preview-dates" v-if="previewDates.length">
                <p class="preview-label">Upcoming scheduled dates:</p>
                <div class="date-pills">
                <span class="date-pill" v-for="d in previewDates" :key="d">{{ fmtDate(d) }}</span>
                </div>
            </div>

            </div>

            <div class="modal-ft">
            <button class="btn-secondary" @click="$emit('close')">Cancel</button>
            <button class="btn-primary" :disabled="saving || !isValid" @click="submit">
                {{ saving ? 'Creating…' : 'Create Schedule' }}
            </button>
            </div>

        </div>
        </div>
    </Teleport>
</template>

<script setup>
import { reactive, computed, watch, ref } from 'vue'

const props = defineProps({
    show:    Boolean,
    invoice: Object,
    saving:  Boolean,
})
const emit = defineEmits(['close', 'save'])

const form = reactive({
    name:              '',
    frequency:         'monthly',
    interval:          1,
    interval_unit:     'month',
    start_date:        '',
    end_date:          '',
    occurrences_limit: null,
    action_on_create:  'draft',
    notify_admin:      true,
})

const actionOptions = [
    { value: 'draft',         label: 'Create as Draft',      desc: 'Held for your review before sending.' },
    { value: 'send',          label: 'Auto-send to Client',  desc: 'Emailed to the client automatically.' },
    { value: 'send_if_valid', label: 'Send if Email Exists', desc: 'Auto-sends if client has an email, otherwise draft.' },
]

// ── Computed ──────────────────────────────────────────────────────────────────

const invoiceTotal = computed(() => {
    const items = props.invoice?.items ?? []
    const t = items.reduce((s, i) => s + parseFloat(i.unit_price ?? 0) * parseFloat(i.quantity ?? 0), 0)
    return fmtAmt(t)
})

const isValid = computed(() =>
    form.name && form.frequency && form.start_date
)

// ── Preview dates (mirrors Model logic client-side) ───────────────────────────

const previewDates = computed(() => {
    if (!form.start_date) return []
    const dates = []
    let d = new Date(form.start_date)
    const max = form.occurrences_limit ? Math.min(form.occurrences_limit, 5) : 5
    for (let i = 0; i < max; i++) {
        if (form.end_date && d > new Date(form.end_date)) break
        dates.push(d.toISOString().split('T')[0])
        d = advance(new Date(d))
    }
    return dates
})

function advance(d) {
    const n = form.interval || 1
    switch (form.frequency) {
        case 'weekly':    d.setDate(d.getDate() + 7 * n); break
        case 'monthly':   d.setMonth(d.getMonth() + n); break
        case 'quarterly': d.setMonth(d.getMonth() + 3 * n); break
        case 'annually':  d.setFullYear(d.getFullYear() + n); break
        case 'custom':
            if      (form.interval_unit === 'day')   d.setDate(d.getDate() + n)
            else if (form.interval_unit === 'week')  d.setDate(d.getDate() + 7 * n)
            else if (form.interval_unit === 'month') d.setMonth(d.getMonth() + n)
            else if (form.interval_unit === 'year')  d.setFullYear(d.getFullYear() + n)
            break
    }
    return d
}

// ── Reset on open ─────────────────────────────────────────────────────────────

watch(() => props.show, (v) => {
    if (!v) return
    // Pre-fill the schedule name from the invoice
    const clientName = props.invoice?.client?.name ?? ''
    form.name              = clientName ? `Monthly — ${clientName}` : ''
    form.frequency         = 'monthly'
    form.interval          = 1
    form.interval_unit     = 'month'
    // Default first run to next month on the same day-of-month as the invoice
    const base             = props.invoice?.invoice_date ? new Date(props.invoice.invoice_date) : new Date()
    base.setMonth(base.getMonth() + 1)
    form.start_date        = base.toISOString().split('T')[0]
    form.end_date          = ''
    form.occurrences_limit = null
    form.action_on_create  = 'draft'
    form.notify_admin      = true
})

// ── Submit ────────────────────────────────────────────────────────────────────

function submit() {
    const payload = {
        name:              form.name,
        frequency:         form.frequency,
        interval:          form.interval,
        interval_unit:     form.frequency === 'custom' ? form.interval_unit : undefined,
        start_date:        form.start_date,
        end_date:          form.end_date   || undefined,
        occurrences_limit: form.occurrences_limit || undefined,
        action_on_create:  form.action_on_create,
        notify_admin:      form.notify_admin,
    }
    emit('save', payload)
}

// ── Formatters ────────────────────────────────────────────────────────────────

function fmtDate(d) {
    return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'
}
function fmtAmt(v) {
    return (parseFloat(v) || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>

<style scoped>

.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 9100; padding: 20px; }
.modal { background: white; border-radius: 16px; width: 100%; max-width: 600px; max-height: 100vh; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
.modal-hd { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f0e8; }
.modal-hd h2 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0; font-family: 'Playfair Display', Georgia, serif; }
.modal-close { background: none; border: none; font-size: 22px; color: #aaa; cursor: pointer; }
.modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; overflow-y: auto; }
.modal-ft { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 24px; border-top: 1px solid #f0f0e8; background: #fafaf7; border-radius: 0 0 16px 16px; }
.modal-sub { font-size: 13px; color: #888; margin: 0; }

/* Source invoice preview */
.source-preview { background: #fafaf7; border: 1px solid #f0f0e8; border-radius: 10px; padding: 14px 16px; }
.source-label { font-size: 11px; font-weight: 600; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
.source-items { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
.source-item { display: flex; justify-content: space-between; font-size: 13px; color: #555; }
.source-desc { color: #333; }
.source-amt { font-weight: 500; font-variant-numeric: tabular-nums; }
.source-total { display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e8e8e0; font-size: 14px; color: #555; }
.source-total strong { color: #1a1a1a; }

/* Form */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 500; color: #555; }
.opt { font-weight: 400; color: #aaa; }
.form-input { padding: 9px 12px; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; color: #1a1a1a; outline: none; background: #fff; width: 100%; box-sizing: border-box; }
.form-input:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }

/* Radio cards */
.radio-cards { display: flex; flex-direction: column; gap: 8px; margin-top: 6px; }
.radio-card { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border: 1px solid #e8e8e0; border-radius: 10px; cursor: pointer; transition: all 0.15s; }
.radio-card:hover { border-color: #d4a853; }
.radio-card.selected { border-color: #d4a853; background: #fdf9f0; }
.radio-card input { margin-top: 3px; accent-color: #d4a853; flex-shrink: 0; }
.radio-title { display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; }
.radio-desc  { display: block; font-size: 12px; color: #888; margin-top: 1px; }

/* Toggle */
.toggle-row { display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #333; }
.toggle-switch { width: 40px; height: 22px; background: #e0e0d8; border-radius: 11px; position: relative; transition: background 0.2s; cursor: pointer; flex-shrink: 0; }
.toggle-switch.on { background: #d4a853; }
.toggle-thumb { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 3px; left: 3px; transition: left 0.2s; }
.toggle-switch.on .toggle-thumb { left: 21px; }

/* Preview dates */
.preview-dates { }
.preview-label { font-size: 12px; color: #888; margin: 0 0 8px; }
.date-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.date-pill { padding: 4px 10px; background: #fdf6e8; border: 1px solid #f5d98e; border-radius: 20px; font-size: 12px; color: #8a6a1a; }

/* Footer — never scrolls, always pinned to bottom of modal */
.modal-ft { flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; padding: 16px 28px; border-top: 1px solid #f0f0e8; background: #fafaf7; }
.btn-primary { padding: 10px 22px; background: #1a1a1a; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 10px 18px; background: white; color: #555; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; cursor: pointer; }
.btn-secondary:hover { border-color: #999; color: #1a1a1a; }
</style>