<template>
  <Teleport to="body">
    <div v-if="show && invoice" class="modal-backdrop" @click.self="$emit('close')">
      <div class="modal">
        <div class="modal-hd">
          <h2>Record Payment</h2>
          <button class="modal-close" @click="$emit('close')">×</button>
        </div>
        <div class="modal-body">
          <div class="inv-summary">
            <div class="inv-sum-row"><span>Invoice</span><strong>{{ invoice.invoice_number }}</strong></div>
            <div class="inv-sum-row"><span>Total</span><strong>R {{ fmt(invoice.total) }}</strong></div>
            <div class="inv-sum-row"><span>Paid</span><strong>R {{ fmt(invoice.paid_amount) }}</strong></div>
            <div class="inv-sum-row balance"><span>Balance Due</span><strong>R {{ fmt(invoice.balance) }}</strong></div>
          </div>

          <div class="form-group">
            <label>Amount *</label>
            <input v-model.number="form.amount" type="number" step="0.01" :max="parseFloat(invoice.balance)" min="0.01" class="form-input" />
            <small>Maximum: R {{ fmt(invoice.balance) }}</small>
          </div>

          <div class="form-group">
            <label>Payment Method</label>
            <select v-model="form.payment_method" class="form-input">
              <option value="">Select…</option>
              <option v-for="m in paymentMethods" :key="m.key" :value="m.key">{{ m.label }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Notes</label>
            <textarea v-model="form.payment_notes" class="form-input" rows="2" placeholder="Reference number, transaction ID…" />
          </div>
        </div>
        <div class="modal-ft">
          <button class="btn-secondary" @click="$emit('close')">Cancel</button>
          <button class="btn-primary" :disabled="saving || !form.amount" @click="submit">
            {{ saving ? 'Saving…' : 'Record Payment' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'

const props = defineProps({ show: Boolean, invoice: Object })
const emit  = defineEmits(['close', 'saved'])

const saving = ref(false)
const paymentMethods = ref([])
const form = reactive({ amount: 0, payment_method: '', payment_notes: '' })

watch(() => props.invoice, (inv) => {
  if (inv) form.amount = parseFloat(inv.balance)
})

async function loadMethods() {
  try {
    const res = await fetch('/api/invoice-configs/payment_methods')
    paymentMethods.value = await res.json()
  } catch {}
}

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}
async function submit() {
  saving.value = true
  try {
    const res = await fetch(`/api/invoices/${props.invoice.id}/record-payment`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': getCsrf()
    },
      body: JSON.stringify(form)
    })
    if (!res.ok) throw await res.json()
    form.amount = 0; form.payment_method = ''; form.payment_notes = ''
    emit('saved')
  } finally { saving.value = false }
}

function fmt(v) { return parseFloat(v || 0).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }

onMounted(loadMethods)
</script>

<style scoped>
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 9100; padding: 20px; }
.modal { background: white; border-radius: 16px; width: 100%; max-width: 440px; max-height: 100vh; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
.modal-hd { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f0e8; }
.modal-hd h2 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0; font-family: 'Playfair Display', Georgia, serif; }
.modal-close { background: none; border: none; font-size: 22px; color: #aaa; cursor: pointer; }
.modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; }
.modal-ft { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 24px; border-top: 1px solid #f0f0e8; background: #fafaf7; border-radius: 0 0 16px 16px; }

.inv-summary { background: #fafaf7; border: 1px solid #f0f0e8; border-radius: 8px; padding: 12px 16px; }
.inv-sum-row { display: flex; justify-content: space-between; font-size: 13px; color: #555; padding: 3px 0; }
.inv-sum-row.balance { margin-top: 6px; padding-top: 8px; border-top: 1px solid #e8e8e0; font-size: 15px; color: #1a1a1a; }

.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 13px; font-weight: 500; color: #555; }
.form-group small { font-size: 11px; color: #aaa; }
.form-input { padding: 9px 12px; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; color: #1a1a1a; outline: none; background: #fff; }
.form-input:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
textarea.form-input { resize: vertical; }

.btn-primary { padding: 10px 22px; background: #1a1a1a; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 10px 18px; background: white; color: #555; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; cursor: pointer; }
.btn-secondary:hover { border-color: #999; }
</style>