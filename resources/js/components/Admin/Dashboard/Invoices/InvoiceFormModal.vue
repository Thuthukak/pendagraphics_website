<template>
    <Teleport to="body">
        <div v-if="show" class="modal-backdrop" @click.self="$emit('close')">
        <div class="modal">
            <!-- Header -->
            <div class="modal-hd">
            <h2>{{ isEditing ? 'Edit Invoice' : 'New Invoice' }}</h2>
            <button class="modal-close" @click="$emit('close')">×</button>
            </div>

            <div class="modal-body">
            <!-- Client & Dates -->
            <div class="section">
                <div class="section-title">Invoice Details</div>
                <div class="form-row">
                <div class="form-group">
                    <label>Client *</label>
                    <select v-model="form.client_id" class="form-input" required @change="onClientChange">
                    <option value="">Select client…</option>
                    <option v-for="c in allClients" :key="c.id" :value="c.id">{{ c.name }}</option>
                    <option value="__new__">+ Add new client</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Invoice Date *</label>
                    <input v-model="form.invoice_date" type="date" class="form-input" required />
                </div>

                <div class="form-group">
                    <label>Due Date *</label>
                    <input v-model="form.due_date" type="date" class="form-input" required />
                </div>

                <div class="form-group">
                    <label>Quick terms</label>
                    <select v-model="form.net_terms" class="form-input" @change="applyNetTerms">
                    <option value="">Select…</option>
                    <option v-for="m in netTerms" :key="m.key" :value="m.key">{{ m.label }}</option>
                    </select>
                </div>
                </div>

                <!-- Inline new client form -->
                <div v-if="showNewClient" class="new-client-panel">
                <div class="new-client-title">New Client</div>
                <div class="form-row">
                    <div class="form-group"><label>Name *</label><input v-model="newClient.name" type="text" class="form-input" placeholder="Company or person name" /></div>
                    <div class="form-group"><label>Email</label><input v-model="newClient.email" type="email" class="form-input" placeholder="billing@example.com" /></div>
                    <div class="form-group"><label>Phone</label><input v-model="newClient.phone" type="tel" class="form-input" /></div>
                    <div class="form-group"><label>Tax Number</label><input v-model="newClient.tax_number" type="text" class="form-input" /></div>
                    <div class="form-group full"><label>Address</label><input v-model="newClient.address" type="text" class="form-input" /></div>
                    <div class="form-group"><label>City</label><input v-model="newClient.city" type="text" class="form-input" /></div>
                    <div class="form-group"><label>Province</label><input v-model="newClient.state" type="text" class="form-input" /></div>
                    <div class="form-group"><label>Postal Code</label><input v-model="newClient.postal_code" type="text" class="form-input" /></div>
                    <div class="form-group"><label>Country</label><input v-model="newClient.country" type="text" class="form-input" placeholder="South Africa" /></div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:12px">
                  <button type="button" class="btn-cancel-client" @click="cancelNewClient">Cancel</button>
                  <button type="button" class="btn-save-client" :disabled="savingClient" @click="saveNewClient">
                    {{ savingClient ? 'Saving…' : 'Save Client' }}
                  </button>
                </div>
                <p v-if="clientError" class="client-error">{{ clientError }}</p>
                </div>
            </div>

            <!-- Line Items -->
            <div class="section">
                <div class="section-header">
                <div class="section-title">Line Items</div>
                <div class="items-actions">
                    <button type="button" class="btn-text" @click="addItem">+ Add item</button>
                    <button type="button" class="btn-text" @click="showServicePicker = true">+ From service</button>
                </div>
                </div>

                <!-- Service picker inline -->
                <div v-if="showServicePicker" class="service-picker">
                <div class="service-picker-title">Pick a service</div>
                <div class="service-grid">
                    <button
                    v-for="svc in services"
                    :key="svc.id"
                    type="button"
                    class="service-chip"
                    @click="addServiceItem(svc)"
                    >
                    <span class="svc-name">{{ svc.name }}</span>
                    <span class="svc-price">R {{ fmtAmt(svc.price ?? svc.base_price) }}</span>
                    </button>
                </div>
                <button type="button" class="btn-cancel-client" @click="showServicePicker = false">Close</button>
                </div>

                <!-- Items table -->
                <div class="items-table-wrap">
                <table class="items-table">
                    <thead>
                    <tr>
                        <th class="col-desc">Description</th>
                        <th class="col-qty">Qty</th>
                        <th class="col-price">Unit Price</th>
                        <th class="col-total">Total</th>
                        <th class="col-del"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, idx) in form.items" :key="idx" class="item-row">
                        <td class="col-desc">
                        <input
                            v-model="item.description"
                            type="text"
                            class="cell-input"
                            placeholder="Item description"
                            required
                        />
                        </td>
                        <td class="col-qty">
                        <input
                            v-model.number="item.quantity"
                            type="number"
                            min="0.01"
                            step="0.01"
                            class="cell-input text-right"
                        />
                        </td>
                        <td class="col-price">
                        <div class="price-cell">
                            <span class="price-prefix">R</span>
                            <input
                            v-model.number="item.unit_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="cell-input"
                            />
                        </div>
                        </td>
                        <td class="col-total text-right">
                        R {{ fmtAmt((item.quantity || 0) * (item.unit_price || 0)) }}
                        </td>
                        <td class="col-del">
                        <button
                            type="button"
                            class="del-btn"
                            :disabled="form.items.length === 1"
                            @click="removeItem(idx)"
                        >×</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>

                <!-- Totals block -->
                <div class="totals-block">
                <div class="totals-inner">
                    <div class="totals-row">
                    <span>Subtotal</span>
                    <span>R {{ fmtAmt(subtotal) }}</span>
                    </div>

                    <div class="totals-row adjust">
                    <span>Discount (%)</span>
                    <div class="adjust-input-wrap">
                        <input v-model.number="form.discount_rate" type="number" min="0" max="100" step="0.01" class="adjust-input" />
                        <span class="adjust-preview" v-if="form.discount_rate > 0">− R {{ fmtAmt(discountAmount) }}</span>
                    </div>
                    </div>

                    <div class="totals-row adjust">
                    <span>Tax (%)</span>
                    <div class="adjust-input-wrap">
                        <input v-model.number="form.tax_rate" type="number" min="0" max="100" step="0.01" class="adjust-input" />
                        <span class="adjust-preview" v-if="form.tax_rate > 0">+ R {{ fmtAmt(taxAmount) }}</span>
                    </div>
                    </div>

                    <div class="totals-row total-final">
                    <span>Total</span>
                    <strong>R {{ fmtAmt(total) }}</strong>
                    </div>
                </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="section">
                <div class="form-row">
                <div class="form-group">
                    <label>Notes <span class="opt">(visible on invoice)</span></label>
                    <textarea v-model="form.notes" class="form-input" rows="3" placeholder="Payment instructions, bank details…" />
                </div>
                <div class="form-group">
                    <label>Terms <span class="opt">(optional)</span></label>
                    <textarea v-model="form.terms" class="form-input" rows="3" placeholder="Late payment penalties, warranty terms…" />
                </div>
                </div>
            </div>
            </div>

            <!-- Footer -->
            <div class="modal-ft">
            <button type="button" class="btn-secondary" @click="$emit('close')">Cancel</button>
            <div style="display:flex;gap:8px">
                <button type="button" class="btn-secondary" @click="saveDraft" :disabled="saving || !isValid">
                Save as Draft
                </button>
                <button type="button" class="btn-primary" @click="saveAndSend" :disabled="saving || !isValid">
                {{ saving ? 'Saving…' : (isEditing ? 'Update Invoice' : 'Create Invoice') }}
                </button>
            </div>
            </div>
        </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'

const props = defineProps({
  show:      { type: Boolean, default: false },
  isEditing: { type: Boolean, default: false },
  invoice:   { type: Object,  default: null  },
  clients:   { type: Array,   default: () => [] },
  services:  { type: Array,   default: () => [] },
  saving:    { type: Boolean, default: false },
})
const emit = defineEmits(['close', 'save', 'client-created'])

// ── Local client list (seeded from prop, extended when a new one is saved) ───
const localClients  = ref([])
const allClients    = computed(() => [...props.clients, ...localClients.value])

// ── Form state ────────────────────────────────────────────────────────────────
const form = reactive({
  client_id:     '',
  invoice_date:  today(),
  due_date:      daysFromNow(30),
  notes:         '',
  terms:         '',
  tax_rate:      0,
  discount_rate: 0,
  items: [{ service_id: null, description: '', quantity: 1, unit_price: 0 }],
})

const showNewClient     = ref(false)
const showServicePicker = ref(false)
const savingClient      = ref(false)
const clientError       = ref('')
const netTerms          = ref([])

const newClient = reactive({
  name: '', email: '', phone: '', address: '',
  city: '', state: '', postal_code: '', country: 'South Africa', tax_number: ''
})

// ── Computed totals ───────────────────────────────────────────────────────────
const subtotal       = computed(() =>
  form.items.reduce((s, i) => s + (parseFloat(i.quantity) || 0) * (parseFloat(i.unit_price) || 0), 0)
)
const discountAmount = computed(() => subtotal.value * (form.discount_rate / 100))
const taxableAmount  = computed(() => subtotal.value - discountAmount.value)
const taxAmount      = computed(() => taxableAmount.value * (form.tax_rate / 100))
const total          = computed(() => taxableAmount.value + taxAmount.value)

const isValid = computed(() =>
  form.client_id && form.client_id !== '__new__' &&
  form.invoice_date && form.due_date &&
  form.items.length > 0 &&
  form.items.every(i => i.description && parseFloat(i.unit_price) >= 0)
)

// ── CSRF helper ───────────────────────────────────────────────────────────────
function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function apiPost(path, data) {
  const res = await fetch('/api' + path, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': getCsrf(),
    },
    body: JSON.stringify(data),
  })
  const json = await res.json()
  if (!res.ok) throw json
  return json
}

// ── Actions ───────────────────────────────────────────────────────────────────
function onClientChange() {
  if (form.client_id === '__new__') {
    showNewClient.value = true
    form.client_id = ''
  }
}

function cancelNewClient() {
  showNewClient.value = false
  clientError.value = ''
  Object.assign(newClient, {
    name: '', email: '', phone: '', address: '',
    city: '', state: '', postal_code: '', country: 'South Africa', tax_number: ''
  })
}

/**
 * POST to /api/clients, get back a real ID, add to local list,
 * select it in the dropdown, then close the panel.
 */
async function saveNewClient() {
  clientError.value = ''
  if (!newClient.name.trim()) { clientError.value = 'Client name is required.'; return }

  savingClient.value = true
  try {
    const { client } = await apiPost('/clients', { ...newClient })
    // Add to local list so it appears in the dropdown immediately
    localClients.value.push(client)
    // Select it
    form.client_id = client.id
    // Notify parent so it can refresh its clients array too
    emit('client-created', client)
    cancelNewClient()
  } catch (err) {
    clientError.value = err?.message ?? 'Failed to save client. Please try again.'
  } finally {
    savingClient.value = false
  }
}

async function loadMethods() {
  try {
    const res = await fetch('/api/invoice-configs/net_terms')
    netTerms.value = await res.json()
  } catch {}
}

function applyNetTerms(e) {
  const days = parseInt(e.target.value)
  if (isNaN(days)) return
  const base = form.invoice_date ? new Date(form.invoice_date) : new Date()
  base.setDate(base.getDate() + days)
  form.due_date = base.toISOString().split('T')[0]
}

function addItem() {
  form.items.push({ service_id: null, description: '', quantity: 1, unit_price: 0 })
}

function removeItem(idx) {
  if (form.items.length > 1) form.items.splice(idx, 1)
}

function addServiceItem(svc) {
  form.items.push({
    service_id:  svc.id,
    description: svc.name,
    quantity:    1,
    unit_price:  parseFloat(svc.price ?? svc.base_price ?? 0),
  })
  showServicePicker.value = false
}

function buildPayload(status = null) {
  const payload = {
    client_id:     form.client_id,
    invoice_date:  form.invoice_date,
    due_date:      form.due_date,
    notes:         form.notes,
    terms:         form.terms,
    tax_rate:      form.tax_rate,
    discount_rate: form.discount_rate,
    items: form.items.map(i => ({
      service_id:  i.service_id || null,
      description: i.description,
      quantity:    parseFloat(i.quantity),
      unit_price:  parseFloat(i.unit_price),
    })),
  }
  if (status) payload.status = status
  return payload
}

function saveDraft()   { emit('save', buildPayload('draft')) }
function saveAndSend() { emit('save', buildPayload()) }

// ── Reset / populate ──────────────────────────────────────────────────────────
function reset() {
  Object.assign(form, {
    client_id: '', invoice_date: today(), due_date: daysFromNow(30),
    notes: '', terms: '', tax_rate: 0, discount_rate: 0,
    items: [{ service_id: null, description: '', quantity: 1, unit_price: 0 }],
  })
  showNewClient.value     = false
  showServicePicker.value = false
  clientError.value       = ''
  cancelNewClient()
}

function populate(inv) {
  Object.assign(form, {
    client_id:     inv.client_id,
    invoice_date:  inv.invoice_date,
    due_date:      inv.due_date,
    notes:         inv.notes ?? '',
    terms:         inv.terms ?? '',
    tax_rate:      parseFloat(inv.tax_rate ?? 0),
    discount_rate: parseFloat(inv.discount_rate ?? 0),
    items: inv.items?.length
      ? inv.items.map(i => ({
          service_id:  i.service_id ?? null,
          description: i.description,
          quantity:    parseFloat(i.quantity),
          unit_price:  parseFloat(i.unit_price),
        }))
      : [{ service_id: null, description: '', quantity: 1, unit_price: 0 }],
  })
}

watch(() => props.show, (v) => {
  if (!v) return
  localClients.value = []
  if (props.isEditing && props.invoice) populate(props.invoice)
  else reset()
})

// ── Utilities ─────────────────────────────────────────────────────────────────
function today()        { return new Date().toISOString().split('T')[0] }
function daysFromNow(n) { const d = new Date(); d.setDate(d.getDate() + n); return d.toISOString().split('T')[0] }
function fmtAmt(v)      { return (parseFloat(v) || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }

onMounted(loadMethods)
</script>

<style scoped>
/* ── Backdrop & modal shell ───────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex; align-items: flex-start; justify-content: center;
  z-index: 9000; padding: 32px 20px; overflow-y: auto;
}

.modal {
  background: #fff;
  border-radius: 16px;
  width: 100%; max-width: 820px;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 64px rgba(0,0,0,0.18);
  margin-bottom: 32px;
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.modal-hd {
  display: flex; justify-content: space-between; align-items: center;
  padding: 22px 28px; border-bottom: 1px solid #f0f0e8;
  position: sticky; top: 0; background: white; z-index: 10;
  border-radius: 16px 16px 0 0;
}
.modal-hd h2 {
  font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0;
  font-family: 'Playfair Display', Georgia, serif;
}
.modal-close { background: none; border: none; font-size: 22px; color: #aaa; cursor: pointer; line-height: 1; }
.modal-close:hover { color: #333; }

/* ── Body ────────────────────────────────────────────────────────────────── */
.modal-body { padding: 0; overflow: visible; }

/* ── Sections ────────────────────────────────────────────────────────────── */
.section { padding: 24px 28px; border-bottom: 1px solid #f4f4ee; }
.section:last-child { border-bottom: none; }

.section-title {
  font-size: 12px; font-weight: 600; color: #aaa;
  text-transform: uppercase; letter-spacing: 0.8px;
  margin-bottom: 16px;
}

.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 16px;
}

/* ── Form layout ─────────────────────────────────────────────────────────── */
.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 12px; font-weight: 500; color: #666; }
.opt { font-weight: 400; color: #bbb; }

.form-input {
  padding: 8px 11px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  font-size: 14px; color: #1a1a1a;
  outline: none; background: white;
  width: 100%; box-sizing: border-box;
  transition: border-color 0.15s;
}
.form-input:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
textarea.form-input { resize: vertical; }

/* ── New client panel ────────────────────────────────────────────────────── */
.new-client-panel {
  margin-top: 14px; padding: 16px;
  background: #fafaf7; border: 1px solid #e8e8e0; border-radius: 10px;
}
.new-client-title { font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 12px; }
.client-error { color: #c0392b; font-size: 12px; margin-top: 8px; margin-bottom: 0; }
.btn-cancel-client { background: none; border: none; font-size: 13px; color: #999; cursor: pointer; }
.btn-cancel-client:hover { color: #555; }
.btn-save-client {
  background: #1a1a1a; color: white; border: none;
  border-radius: 7px; padding: 7px 16px;
  font-size: 13px; cursor: pointer;
}
.btn-save-client:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-save-client:hover:not(:disabled) { background: #333; }

/* ── Service picker ──────────────────────────────────────────────────────── */
.items-actions { display: flex; gap: 12px; }
.btn-text { background: none; border: none; font-size: 13px; color: #d4a853; font-weight: 600; cursor: pointer; padding: 0; }
.btn-text:hover { color: #b8902e; }

.service-picker {
  background: #fafaf7; border: 1px solid #e8e8e0; border-radius: 10px;
  padding: 14px 16px; margin-bottom: 14px;
}
.service-picker-title { font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 10px; }
.service-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
.service-chip {
  display: flex; flex-direction: column; gap: 2px;
  padding: 8px 14px; background: white;
  border: 1px solid #e0e0d8; border-radius: 8px;
  cursor: pointer; transition: all 0.15s; text-align: left;
}
.service-chip:hover { border-color: #d4a853; background: #fdf9f0; }
.svc-name { font-size: 13px; font-weight: 500; color: #1a1a1a; }
.svc-price { font-size: 11px; color: #888; }

/* ── Items table ─────────────────────────────────────────────────────────── */
.items-table-wrap { overflow-x: auto; border: 1px solid #e8e8e0; border-radius: 8px; margin-bottom: 0; }
.items-table { width: 100%; border-collapse: collapse; }

.items-table thead th {
  padding: 10px 12px;
  font-size: 11px; font-weight: 600; color: #aaa;
  text-transform: uppercase; letter-spacing: 0.5px;
  background: #fafaf7; border-bottom: 1px solid #e8e8e0;
  text-align: left;
}
.items-table thead th.text-right { text-align: right; }

.col-desc  { width: auto; min-width: 220px; }
.col-qty   { width: 80px; }
.col-price { width: 130px; }
.col-total { width: 110px; }
.col-del   { width: 36px; }

.item-row td {
  padding: 8px 12px; border-bottom: 1px solid #f4f4ee;
  vertical-align: middle;
}
.item-row:last-child td { border-bottom: none; }

.cell-input {
  width: 100%; padding: 6px 8px;
  border: 1px solid transparent; border-radius: 6px;
  font-size: 14px; color: #1a1a1a; background: transparent;
  outline: none; box-sizing: border-box;
}
.cell-input:hover { border-color: #e0e0d8; }
.cell-input:focus { border-color: #d4a853; background: white; box-shadow: 0 0 0 3px rgba(212,168,83,0.08); }
.cell-input.text-right { text-align: right; }

.price-cell { display: flex; align-items: center; gap: 4px; }
.price-prefix { font-size: 13px; color: #aaa; }

.text-right { text-align: right; font-size: 14px; font-weight: 500; color: #1a1a1a; white-space: nowrap; }

.del-btn {
  width: 24px; height: 24px;
  background: none; border: none; border-radius: 5px;
  color: #ccc; font-size: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.del-btn:hover:not(:disabled) { background: #fee8e8; color: #c0392b; }
.del-btn:disabled { opacity: 0.25; cursor: default; }

/* ── Totals block ────────────────────────────────────────────────────────── */
.totals-block { display: flex; justify-content: flex-end; padding-top: 16px; }
.totals-inner { width: 100%; max-width: 320px; display: flex; flex-direction: column; gap: 8px; }

.totals-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 13px; color: #555;
}
.totals-row.total-final {
  font-size: 16px; font-weight: 600; color: #1a1a1a;
  padding-top: 10px; border-top: 1px solid #e8e8e0; margin-top: 4px;
}

.adjust-input-wrap { display: flex; align-items: center; gap: 8px; }
.adjust-input {
  width: 64px; padding: 4px 8px;
  border: 1px solid #e0e0d8; border-radius: 6px;
  font-size: 13px; text-align: right; outline: none;
}
.adjust-input:focus { border-color: #d4a853; }
.adjust-preview { font-size: 12px; color: #888; min-width: 80px; text-align: right; }

/* ── Footer ──────────────────────────────────────────────────────────────── */
.modal-ft {
  display: flex; justify-content: space-between; align-items: center;
  padding: 16px 28px; border-top: 1px solid #f0f0e8;
  background: #fafaf7; border-radius: 0 0 16px 16px;
  position: sticky; bottom: 0; z-index: 10;
}

.btn-primary {
  padding: 10px 22px; background: #1a1a1a; color: white;
  border: none; border-radius: 8px; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: background 0.15s;
}
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  padding: 10px 18px; background: white; color: #555;
  border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px;
  cursor: pointer; transition: all 0.15s;
}
.btn-secondary:hover:not(:disabled) { border-color: #999; color: #1a1a1a; }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 640px) {
  .modal-backdrop { padding: 0; align-items: flex-end; }
  .modal { border-radius: 16px 16px 0 0; max-height: 95vh; overflow-y: auto; margin-bottom: 0; }
  .form-row { grid-template-columns: 1fr; }
  .totals-inner { max-width: none; }
}
</style>