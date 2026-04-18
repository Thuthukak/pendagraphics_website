<template>
    <Teleport to="body">
        <div v-if="show" class="modal-backdrop" @click.self="$emit('close')">
        <div class="modal">
            <!-- Header -->
            <div class="modal-hd">
            <h2>{{ isEditing ? 'Edit Schedule' : 'New Recurring Invoice' }}</h2>
            <button class="modal-close" @click="$emit('close')">×</button>
            </div>

            <div class="modal-body">
            <!-- Step indicator -->
            <div class="steps">
                <button
                v-for="(step, i) in steps"
                :key="i"
                class="step"
                :class="{ active: currentStep === i, done: currentStep > i }"
                @click="goToStep(i)"
                >
                <span class="step-num">{{ i + 1 }}</span>
                <span class="step-label">{{ step }}</span>
                </button>
            </div>

            <!-- Step 1: Basic info -->
            <div v-show="currentStep === 0" class="step-content">
                <div class="form-row">
                <div class="form-group full">
                    <label>Schedule Name *</label>
                    <input v-model="form.name" type="text" class="form-input" placeholder="e.g. Monthly Hosting — Acme Corp" />
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>Client *</label>
                    <select v-model="form.client_id" class="form-input">
                    <option value="">Select client…</option>
                    <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Days Until Due</label>
                    <select v-model="form.days_until_due" class="form-input">
                    <option :value="0">Due on Receipt</option>
                    <option :value="7">Net 7</option>
                    <option :value="14">Net 14</option>
                    <option :value="30">Net 30</option>
                    <option :value="60">Net 60</option>
                    <option :value="90">Net 90</option>
                    </select>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group full">
                    <label>Notes <span class="opt">(appears on each generated invoice)</span></label>
                    <textarea v-model="form.notes" class="form-input" rows="3" placeholder="Payment terms, bank details…" />
                </div>
                </div>
            </div>

            <!-- Step 2: Schedule -->
            <div v-show="currentStep === 1" class="step-content">
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
                    <input v-model.number="form.interval" type="number" min="1" class="form-input" style="width:80px" />
                    <select v-model="form.interval_unit" class="form-input">
                        <option value="day">Day(s)</option>
                        <option value="week">Week(s)</option>
                        <option value="month">Month(s)</option>
                        <option value="year">Year(s)</option>
                    </select>
                    </div>
                </div>
                </div>

                <div class="form-row">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input v-model="form.start_date" type="date" class="form-input" />
                </div>
                <div class="form-group">
                    <label>End Date <span class="opt">(optional)</span></label>
                    <input v-model="form.end_date" type="date" class="form-input" />
                </div>
                </div>

                <div class="form-row">
                <div class="form-group">
                    <label>Max Occurrences <span class="opt">(optional — blank = unlimited)</span></label>
                    <input v-model.number="form.occurrences_limit" type="number" min="1" class="form-input" placeholder="e.g. 12" />
                </div>
                </div>

                <!-- Preview dates — computed client-side -->
                <div class="preview-dates" v-if="previewDates.length">
                <p class="preview-label">Next scheduled dates:</p>
                <div class="date-pills">
                    <span class="date-pill" v-for="d in previewDates" :key="d">{{ fmtDate(d) }}</span>
                </div>
                </div>
            </div>

            <!-- Step 3: Automation -->
            <div v-show="currentStep === 2" class="step-content">
                <div class="form-group full">
                <label>When a new invoice is generated…</label>
                <div class="radio-cards">
                    <label
                    v-for="opt in actionOptions"
                    :key="opt.value"
                    class="radio-card"
                    :class="{ selected: form.action_on_create === opt.value }"
                    >
                    <input type="radio" v-model="form.action_on_create" :value="opt.value" />
                    <div class="radio-card-content">
                        <span class="radio-card-title">{{ opt.label }}</span>
                        <span class="radio-card-desc">{{ opt.desc }}</span>
                    </div>
                    </label>
                </div>
                </div>

                <div class="toggle-row">
                <label class="toggle-label">
                    <span>Notify me when an invoice is generated</span>
                    <div class="toggle-switch" :class="{ on: form.notify_admin }" @click="form.notify_admin = !form.notify_admin">
                    <div class="toggle-thumb"></div>
                    </div>
                </label>
                </div>
            </div>

            <!-- Step 4: Line items -->
            <div v-show="currentStep === 3" class="step-content">
                <div class="items-header">
                <span>Line Items *</span>
                <button type="button" class="btn-add-item" @click="addItem">+ Add item</button>
                </div>

                <div v-for="(item, idx) in form.items" :key="idx" class="item-row">
                <div class="item-service">
                    <label>Service</label>
                    <select v-model="item.service_id" class="form-input" @change="onServiceChange(idx)">
                    <option value="">Custom item</option>
                    <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div class="item-desc">
                    <label>Description *</label>
                    <input v-model="item.description" type="text" class="form-input" placeholder="Item description" />
                </div>
                <div class="item-qty">
                    <label>Qty</label>
                    <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="form-input" />
                </div>
                <div class="item-price">
                    <label>Unit Price</label>
                    <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="form-input" />
                </div>
                <div class="item-total">
                    <label>Total</label>
                    <div class="item-total-val">R {{ fmtAmt(item.quantity * item.unit_price) }}</div>
                </div>
                <button type="button" class="item-remove" @click="removeItem(idx)" :disabled="form.items.length === 1">×</button>
                </div>

                <div class="items-subtotal">
                <span>Total per invoice</span>
                <strong>R {{ fmtAmt(invoiceTotal) }}</strong>
                </div>
            </div>
            </div>

            <!-- Footer nav -->
            <div class="modal-ft">
            <button class="btn-secondary" @click="$emit('close')">Cancel</button>
            <div style="display:flex;gap:8px">
                <button v-if="currentStep > 0" class="btn-secondary" @click="currentStep--">← Back</button>
                <button v-if="currentStep < steps.length - 1" class="btn-primary" @click="nextStep">Next →</button>
                <button v-else class="btn-primary" :disabled="saving || !isValid" @click="submit">
                {{ saving ? 'Saving…' : (isEditing ? 'Update Schedule' : 'Create Schedule') }}
                </button>
            </div>
            </div>
        </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
    show:       Boolean,
    isEditing:  Boolean,
    schedule:   Object,
    clients:    Array,
    services:   Array,
    saving:     Boolean,
})
const emit = defineEmits(['close', 'save'])

const steps       = ['Details', 'Schedule', 'Automation', 'Line Items']
const currentStep = ref(0)

// ── Default / blank form ──────────────────────────────────────────────────────

function blankForm() {
    return {
        name:              '',
        client_id:         '',
        frequency:         'monthly',
        interval:          1,
        interval_unit:     'month',
        start_date:        new Date().toISOString().split('T')[0],
        end_date:          '',
        occurrences_limit: null,
        days_until_due:    30,
        notes:             '',
        terms:             '',
        tax_rate:          0,
        discount_rate:     0,
        action_on_create:  'draft',
        notify_admin:      true,
        items: [{ service_id: '', description: '', quantity: 1, unit_price: 0 }],
    }
}

const form = reactive(blankForm())

const previewDates = ref([])

const actionOptions = [
    { value: 'draft',         label: 'Create as Draft',       desc: 'Invoice is created but held for your review before sending.' },
    { value: 'send',          label: 'Auto-send to Client',   desc: 'Invoice is created and emailed to the client automatically.' },
    { value: 'send_if_valid', label: 'Send if Email Exists',  desc: 'Auto-sends if the client has an email address, otherwise creates a draft.' },
]

// ── Computed ──────────────────────────────────────────────────────────────────

const invoiceTotal = computed(() =>
    form.items.reduce(
        (sum, i) => sum + (parseFloat(i.quantity) || 0) * (parseFloat(i.unit_price) || 0),
        0
    )
)

// All required fields must be filled; every item needs description + non-negative price
const isValid = computed(() =>
    form.name &&
    form.client_id &&
    form.start_date &&
    form.frequency &&
    form.items.length > 0 &&
    form.items.every(i => i.description && parseFloat(i.unit_price) >= 0 && parseFloat(i.quantity) > 0)
)

// ── Line-item helpers ─────────────────────────────────────────────────────────

function addItem() {
    form.items.push({ service_id: '', description: '', quantity: 1, unit_price: 0 })
}

function removeItem(idx) {
    if (form.items.length > 1) form.items.splice(idx, 1)
}

// Populate description & price from the selected service catalogue entry
function onServiceChange(idx) {
    const svc = (props.services ?? []).find(s => s.id == form.items[idx].service_id)
    if (svc) {
        form.items[idx].description = svc.name
        form.items[idx].unit_price  = parseFloat(svc.price ?? svc.base_price ?? 0)
    }
}

// ── Step navigation ───────────────────────────────────────────────────────────

function goToStep(i) {
    // Allow clicking already-visited steps freely
    if (i <= currentStep.value) {
        currentStep.value = i
        return
    }
    // Only advance if all prior steps are implicitly valid
    currentStep.value = i
}

function nextStep() {
    if (currentStep.value === 1) computePreviewDates()
    if (currentStep.value < steps.length - 1) currentStep.value++
}

// ── Preview dates (client-side, mirrors Model.calculateNextRunDate logic) ─────

function computePreviewDates() {
    if (!form.start_date) { previewDates.value = []; return }

    const dates = []
    let d = new Date(form.start_date)
    const max = form.occurrences_limit ? Math.min(form.occurrences_limit, 6) : 6

    for (let i = 0; i < max; i++) {
        if (form.end_date && d > new Date(form.end_date)) break
        dates.push(d.toISOString().split('T')[0])
        d = advance(new Date(d))
    }

    previewDates.value = dates
}

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
        default: d.setMonth(d.getMonth() + 1)
    }
    return d
}

// ── Submit ────────────────────────────────────────────────────────────────────

function submit() {
    // Build a clean payload — strip empty optional fields so they don't fail server validation
    const payload = {
        ...form,
        items: form.items.map(i => ({
            service_id:  i.service_id || null, // Convert '' → null for nullable FK
            description: i.description,
            quantity:    i.quantity,
            unit_price:  i.unit_price,
        })),
    }

    if (!payload.end_date)          delete payload.end_date
    if (!payload.occurrences_limit) delete payload.occurrences_limit
    if (!payload.notes)             delete payload.notes
    if (!payload.terms)             delete payload.terms

    // interval_unit is only required when frequency === 'custom'
    if (payload.frequency !== 'custom') delete payload.interval_unit

    emit('save', payload)
}

// ── Reset / populate on open ──────────────────────────────────────────────────

function reset() {
    Object.assign(form, blankForm())
    currentStep.value  = 0
    previewDates.value = []
}

function populate(s) {
    Object.assign(form, {
        name:              s.name,
        client_id:         s.client_id,
        frequency:         s.frequency,
        interval:          s.interval          ?? 1,
        interval_unit:     s.interval_unit     ?? 'month',
        start_date:        s.start_date,
        end_date:          s.end_date          ?? '',
        occurrences_limit: s.occurrences_limit ?? null,
        days_until_due:    s.days_until_due    ?? 30,
        notes:             s.notes             ?? '',
        terms:             s.terms             ?? '',
        tax_rate:          s.tax_rate          ?? 0,
        discount_rate:     s.discount_rate     ?? 0,
        action_on_create:  s.action_on_create  ?? 'draft',
        notify_admin:      s.notify_admin      ?? true,
        items: (s.items ?? [{ service_id: '', description: '', quantity: 1, unit_price: 0 }])
            .map(i => ({
                service_id:  i.service_id  ?? '',
                description: i.description,
                quantity:    i.quantity,
                unit_price:  i.unit_price,
            })),
    })
    currentStep.value  = 0
    previewDates.value = []
}

watch(
    () => props.show,
    (visible) => {
        if (!visible) return
        if (props.isEditing && props.schedule) {
            populate(props.schedule)
        } else {
            reset()
        }
    }
)

// ── Formatting helpers ────────────────────────────────────────────────────────

function fmtDate(d) {
    return d
        ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' })
        : '—'
}

function fmtAmt(v) {
    return (parseFloat(v) || 0).toLocaleString('en-ZA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}
</script>

<style scoped>
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 9000; padding: 20px; }
.modal { background: #fff; border-radius: 16px; width: 100%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }

.modal-hd { display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid #f0f0e8; }
.modal-hd h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0; font-family: 'Playfair Display', Georgia, serif; }
.modal-close { background: none; border: none; font-size: 22px; color: #aaa; cursor: pointer; line-height: 1; }
.modal-close:hover { color: #333; }

/* Steps */
.steps { display: flex; gap: 0; padding: 20px 28px 0; }
.step { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border: none; background: none; cursor: pointer; font-size: 13px; color: #aaa; position: relative; border-bottom: 2px solid transparent; transition: all 0.15s; }
.step:hover { color: #555; }
.step.active { color: #1a1a1a; border-bottom-color: #d4a853; }
.step.done { color: #27ae60; }
.step-num { width: 20px; height: 20px; border-radius: 50%; background: #f0f0e8; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.step.active .step-num { background: #d4a853; color: white; }
.step.done .step-num { background: #27ae60; color: white; }

.modal-body { flex: 1; overflow-y: auto; padding: 24px 28px; }
.step-content { display: flex; flex-direction: column; gap: 16px; }

/* Form elements */
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 13px; font-weight: 500; color: #555; }
.opt { font-weight: 400; color: #aaa; }
.form-input { padding: 9px 12px; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; color: #1a1a1a; outline: none; background: #fff; width: 100%; box-sizing: border-box; }
.form-input:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
textarea.form-input { resize: vertical; }

/* Preview dates */
.preview-dates { margin-top: 8px; }
.preview-label { font-size: 12px; color: #888; margin: 0 0 8px; }
.date-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.date-pill { padding: 4px 10px; background: #fdf6e8; border: 1px solid #f5d98e; border-radius: 20px; font-size: 12px; color: #8a6a1a; }

/* Radio cards */
.radio-cards { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }
.radio-card { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid #e8e8e0; border-radius: 10px; cursor: pointer; transition: all 0.15s; }
.radio-card:hover { border-color: #d4a853; }
.radio-card.selected { border-color: #d4a853; background: #fdf9f0; }
.radio-card input[type="radio"] { margin-top: 2px; accent-color: #d4a853; }
.radio-card-content { display: flex; flex-direction: column; gap: 2px; }
.radio-card-title { font-size: 14px; font-weight: 600; color: #1a1a1a; }
.radio-card-desc { font-size: 12px; color: #888; }

/* Toggle */
.toggle-row { margin-top: 8px; }
.toggle-label { display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-size: 14px; color: #333; }
.toggle-switch { width: 40px; height: 22px; background: #e0e0d8; border-radius: 11px; position: relative; transition: background 0.2s; flex-shrink: 0; }
.toggle-switch.on { background: #d4a853; }
.toggle-thumb { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 3px; left: 3px; transition: left 0.2s; }
.toggle-switch.on .toggle-thumb { left: 21px; }

/* Line items */
.items-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 4px; }
.btn-add-item { background: none; border: none; color: #d4a853; font-size: 13px; font-weight: 600; cursor: pointer; }

.item-row { display: grid; grid-template-columns: 1.5fr 2fr 80px 100px 90px 28px; gap: 8px; align-items: end; padding: 12px; background: #fafaf7; border: 1px solid #f0f0e8; border-radius: 8px; margin-bottom: 8px; }
.item-row label { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.4px; }
.item-service, .item-desc, .item-qty, .item-price, .item-total { display: flex; flex-direction: column; gap: 4px; }
.item-total-val { font-size: 14px; font-weight: 600; color: #1a1a1a; padding: 9px 0; }
.item-remove { background: none; border: none; color: #ccc; font-size: 18px; cursor: pointer; padding: 0; align-self: flex-end; margin-bottom: 2px; }
.item-remove:hover:not(:disabled) { color: #c0392b; }
.item-remove:disabled { opacity: 0.3; cursor: default; }

.items-subtotal { display: flex; justify-content: space-between; padding: 12px 0 0; border-top: 1px solid #e8e8e0; font-size: 14px; color: #555; }
.items-subtotal strong { color: #1a1a1a; }

/* Modal footer */
.modal-ft { display: flex; justify-content: space-between; align-items: center; padding: 16px 28px; border-top: 1px solid #f0f0e8; background: #fafaf7; }
.btn-primary { padding: 10px 22px; background: #1a1a1a; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 10px 18px; background: white; color: #555; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; cursor: pointer; transition: all 0.15s; }
.btn-secondary:hover { border-color: #999; color: #1a1a1a; }

@media (max-width: 640px) {
  .item-row { grid-template-columns: 1fr 1fr; }
  .steps { overflow-x: auto; }
}
</style>