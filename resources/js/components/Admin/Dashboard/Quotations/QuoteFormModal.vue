<template>
  <div v-if="show" class="modal-overlay" @click="close">
    <div class="modal" @click.stop>
      <div class="modal-header">
        <h2>{{ isEditing ? 'Edit Quotation' : 'New Quotation' }}</h2>
        <button class="close-btn" @click="close">×</button>
      </div>

      <div class="modal-body">
        <div class="form-grid">
          <label>
            Name
            <input v-model="form.name" type="text" placeholder="Client name" />
          </label>
          <label>
            Email
            <input v-model="form.email" type="email" placeholder="client@example.com" />
          </label>
          <label>
            Expiry date
            <input v-model="form.expiry_date" type="date" />
          </label>
        </div>

        <label class="full">
          Services
          <div class="services-picker">
            <div v-for="service in services" :key="service.id" class="service-row">
              <label class="checkbox">
                <input
                  type="checkbox"
                  :checked="isSelected(service.id)"
                  @change="toggleService(service, $event.target.checked)"
                />
                {{ service.name }}
              </label>
              <input
                v-if="isSelected(service.id)"
                type="number"
                min="0"
                step="0.01"
                class="price-input"
                :value="selectedPrice(service.id)"
                @input="updatePrice(service.id, $event.target.value)"
              />
            </div>
            <p v-if="!services.length" class="empty-hint">No services available.</p>
          </div>
        </label>

        <label class="full">
          Additional details / notes
          <textarea v-model="form.notes" rows="3"></textarea>
        </label>

        <label class="full">
          Terms
          <textarea v-model="form.terms" rows="2"></textarea>
        </label>

        <div class="total-row">
          <span>Total</span>
          <strong>R{{ total.toFixed(2) }}</strong>
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>
      </div>

      <div class="modal-footer">
        <button class="penda-btn penda-btn-gray" @click="close" :disabled="saving">Cancel</button>
        <button class="penda-btn penda-btn-primary" @click="submit" :disabled="saving">
          {{ saving ? 'Saving…' : (isEditing ? 'Save Changes' : 'Create Quotation') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  isEditing: { type: Boolean, default: false },
  estimate: { type: Object, default: null },
  services: { type: Array, default: () => [] },
  saving: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'save'])

const error = ref('')
const form = reactive({
  name: '',
  email: '',
  expiry_date: '',
  notes: '',
  terms: '',
})
const selected = ref([]) // [{ id, name, price }]

function isSelected(id) {
  return selected.value.some(s => s.id === id)
}
function selectedPrice(id) {
  return selected.value.find(s => s.id === id)?.price ?? 0
}
function toggleService(service, checked) {
  if (checked) {
    selected.value.push({ id: service.id, name: service.name, price: service.price ?? 0 })
  } else {
    selected.value = selected.value.filter(s => s.id !== service.id)
  }
}
function updatePrice(id, value) {
  const item = selected.value.find(s => s.id === id)
  if (item) item.price = parseFloat(value) || 0
}

const total = computed(() => selected.value.reduce((sum, s) => sum + (parseFloat(s.price) || 0), 0))

watch(() => props.show, (val) => {
  if (!val) return
  error.value = ''

  if (props.isEditing && props.estimate) {
    form.name = props.estimate.name || ''
    form.email = props.estimate.email || ''
    form.expiry_date = props.estimate.expiry_date || ''
    form.notes = props.estimate.notes || ''
    form.terms = props.estimate.terms || ''
    selected.value = (props.estimate.services || []).map(s => ({ id: s.id, name: s.name, price: s.price }))
  } else {
    form.name = ''
    form.email = ''
    form.expiry_date = ''
    form.notes = ''
    form.terms = ''
    selected.value = []
  }
})

function close() {
  emit('close')
}

function submit() {
  if (!form.name || !form.email) {
    error.value = 'Name and email are required.'
    return
  }
  if (!selected.value.length) {
    error.value = 'Please select at least one service.'
    return
  }

  emit('save', {
    name: form.name,
    email: form.email,
    expiry_date: form.expiry_date || null,
    notes: form.notes,
    terms: form.terms,
    selectedServices: selected.value.map(s => ({ id: s.id, price: s.price })),
    totalEstimate: total.value,
  })
}
</script>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(31,31,31,0.75); display: flex; align-items: center; justify-content: center; z-index: 999999; }
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
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 20px; border-bottom: 1px solid #e8e8e0; }
.modal-header h2 { margin: 0; font-size: 18px; color: #1a1a1a; }
.close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; }
.modal-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
label { display: flex; flex-direction: column; gap: 5px; font-size: 12px; font-weight: 600; color: #555; }
label.full { grid-column: 1 / -1; }
input, textarea { padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 14px; font-weight: 400; color: #1a1a1a; }
textarea { resize: vertical; font-family: inherit; }

.services-picker { border: 1px solid #e0e0d8; border-radius: 8px; padding: 8px; display: flex; flex-direction: column; gap: 4px; max-height: 220px; overflow-y: auto; }
.service-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 6px 8px; border-radius: 6px; }
.service-row:hover { background: #fafaf7; }
.checkbox { flex-direction: row; align-items: center; gap: 8px; font-weight: 400; font-size: 13px; color: #333; }
.checkbox input { width: auto; }
.price-input { width: 90px; padding: 5px 8px; }
.empty-hint { font-size: 13px; color: #999; margin: 4px; }

.total-row { display: flex; justify-content: space-between; align-items: center; font-size: 15px; padding-top: 6px; border-top: 1px solid #f0f0e8; }
.error-msg { color: #c0392b; font-size: 13px; margin: 0; }

.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid #e8e8e0; }
.btn-primary, .btn-secondary { padding: 9px 18px; border-radius: 7px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
.btn-primary { background: #1a1a1a; color: #f0efe7; }
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-secondary { background: #edf2f7; color: #4a5568; }
.btn-primary:disabled, .btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
</style>