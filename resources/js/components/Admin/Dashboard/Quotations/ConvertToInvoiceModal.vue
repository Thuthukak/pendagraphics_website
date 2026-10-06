<template>
  <div v-if="show" class="modal-overlay" @click="$emit('close')">
    <div class="modal" @click.stop>
      <div class="modal-header">
        <h2>Convert to Invoice</h2>
        <button class="close-btn" @click="$emit('close')">×</button>
      </div>

      <div class="modal-body" v-if="estimate">
        <p class="lead">
          Quote <strong>{{ estimate.quote_number }}</strong> for <strong>{{ estimate.name }}</strong>
          (R{{ estimate.total_amount }}) will become a new invoice. The quotation will be locked
          and marked <em>converted</em>.
        </p>

        <div class="form-grid">
          <label>
            Invoice date
            <input type="date" v-model="form.invoice_date" />
          </label>
          <label>
            Due date
            <input type="date" v-model="form.due_date" />
          </label>
          <label>
            Tax rate (%)
            <input type="number" min="0" max="100" step="0.01" v-model.number="form.tax_rate" />
          </label>
          <label>
            Discount rate (%)
            <input type="number" min="0" max="100" step="0.01" v-model.number="form.discount_rate" />
          </label>
        </div>

        <label class="full">
          Notes (optional override)
          <textarea v-model="form.notes" rows="3" :placeholder="estimate.notes || 'Carried over from the quotation notes'"></textarea>
        </label>

        <div class="action-choice">
          <label class="radio">
            <input type="radio" value="draft" v-model="form.action" />
            <div>
              <strong>Save as draft</strong>
              <span>Create the invoice but don't email it yet</span>
            </div>
          </label>
          <label class="radio">
            <input type="radio" value="send" v-model="form.action" />
            <div>
              <strong>Send immediately</strong>
              <span>Create and email the invoice to {{ estimate.email }}</span>
            </div>
          </label>
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" @click="$emit('close')" :disabled="submitting">Cancel</button>
        <button class="btn-primary" @click="submit" :disabled="submitting">
          {{ submitting ? 'Converting…' : 'Convert to Invoice' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  estimate: { type: Object, default: null },
})

const emit = defineEmits(['close', 'converted'])

const submitting = ref(false)
const error = ref('')

const form = reactive({
  invoice_date: new Date().toISOString().slice(0, 10),
  due_date: '',
  tax_rate: 0,
  discount_rate: 0,
  notes: '',
  action: 'draft',
})

function defaultDueDate() {
  const d = new Date()
  d.setDate(d.getDate() + 30)
  return d.toISOString().slice(0, 10)
}

watch(() => props.show, (val) => {
  if (val) {
    error.value = ''
    form.invoice_date = new Date().toISOString().slice(0, 10)
    form.due_date = defaultDueDate()
    form.tax_rate = 0
    form.discount_rate = 0
    form.notes = ''
    form.action = 'draft'
  }
})

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function submit() {
  if (!props.estimate) return
  submitting.value = true
  error.value = ''

  try {
    const res = await fetch(`/api/estimates/${props.estimate.id}/convert-to-invoice`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
      },
      body: JSON.stringify(form),
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message || 'Failed to convert quotation.')
    }

    emit('converted', data)
  } catch (e) {
    error.value = e.message || 'Failed to convert quotation.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(31,31,31,0.75);
  display: flex; align-items: center; justify-content: center; z-index: 999999;
}
.modal {
  position: relative;
  top: auto;
  left: auto;
  height: auto;
  background: #fff;
  border-radius: 16px;
  width: 100%; max-width: 820px;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 64px rgba(0,0,0,0.18);
  margin-bottom: 32px;
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 20px; border-bottom: 1px solid #e8e8e0;
}
.modal-header h2 { margin: 0; font-size: 18px; color: #1a1a1a; }
.close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; }
.modal-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }
.lead { font-size: 14px; color: #444; line-height: 1.5; margin: 0; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
label { display: flex; flex-direction: column; gap: 5px; font-size: 12px; font-weight: 600; color: #555; }
label.full { grid-column: 1 / -1; }
input, textarea {
  padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 14px; font-weight: 400; color: #1a1a1a;
}
textarea { resize: vertical; font-family: inherit; }
.action-choice { display: flex; flex-direction: column; gap: 8px; }
.radio {
  flex-direction: row; align-items: flex-start; gap: 10px; font-weight: 400;
  border: 1px solid #e0e0d8; border-radius: 8px; padding: 10px 12px; cursor: pointer;
}
.radio input { width: auto; margin-top: 3px; }
.radio div { display: flex; flex-direction: column; gap: 2px; }
.radio strong { font-size: 13px; color: #1a1a1a; }
.radio span { font-size: 12px; color: #888; }
.error-msg { color: #c0392b; font-size: 13px; margin: 0; }
.modal-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  padding: 16px 20px; border-top: 1px solid #e8e8e0;
}
.btn-primary, .btn-secondary {
  padding: 9px 18px; border-radius: 7px; font-size: 14px; font-weight: 600; cursor: pointer; border: none;
}
.btn-primary { background: #2f9e8f; color: white; }
.btn-primary:hover:not(:disabled) { background: #278174; }
.btn-secondary { background: #edf2f7; color: #4a5568; }
.btn-primary:disabled, .btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
</style>