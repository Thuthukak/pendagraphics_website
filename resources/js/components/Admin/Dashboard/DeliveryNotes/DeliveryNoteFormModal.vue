<template>
  <div v-if="show" class="dn-overlay" @click.self="$emit('close')">
    <div class="dn-modal-lg">
      <div class="dn-modal-header">
        <h2>{{ isEditing ? 'Edit Delivery Note' : 'New Delivery Note' }}</h2>
        <button class="icon-btn" @click="$emit('close')"><font-awesome-icon :icon="['fas', 'xmark']" /></button>
      </div>

      <div class="dn-modal-body">
        <!-- Source picker (create only) -->
        <div v-if="!isEditing" class="source-picker">
          <span class="source-label">Create from:</span>
          <div class="source-options">
            <button class="source-btn" :class="{ active: sourceType === 'standalone' }" @click="setSource('standalone')">Standalone</button>
            <button class="source-btn" :class="{ active: sourceType === 'invoice' }" @click="setSource('invoice')">Invoice</button>
            <button class="source-btn" :class="{ active: sourceType === 'estimate' }" @click="setSource('estimate')">Estimate</button>
          </div>

          <div v-if="sourceType !== 'standalone'" class="source-lookup">
            <input
              v-model="sourceSearch"
              class="dn-input"
              :placeholder="sourceType === 'invoice' ? 'Search invoice #…' : 'Search estimate #…'"
              @input="debouncedSourceSearch"
            />
            <div v-if="sourceResults.length" class="source-results">
              <button
                v-for="doc in sourceResults"
                :key="doc.id"
                class="source-result"
                @click="loadPrefill(doc)"
              >
                {{ doc.invoice_number || doc.estimate_number }} — {{ doc.client?.name }}
              </button>
            </div>
            <p v-if="prefillLoaded" class="source-confirm">
              <font-awesome-icon :icon="['fas', 'check']" /> Loaded client and items from {{ sourceType }}. You can still edit them below.
            </p>
          </div>
        </div>

        <!-- Client + date -->
        <div class="dn-grid-2">
          <label class="dn-field">
            <span>Client *</span>
            <select v-model="form.client_id" class="dn-input">
              <option value="" disabled>Select a client…</option>
              <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </label>
          <label class="dn-field">
            <span>Delivery date *</span>
            <input v-model="form.delivery_date" type="date" class="dn-input" />
          </label>
        </div>

        <label class="dn-field">
          <span>Delivery address</span>
          <textarea v-model="form.delivery_address" class="dn-input" rows="2" placeholder="Where the goods are being delivered…"></textarea>
        </label>

        <!-- Items -->
        <div class="items-block">
          <div class="items-header">
            <span>Items</span>
            <button class="link-btn" @click="addItem">+ Add item</button>
          </div>
          <div v-for="(item, idx) in form.items" :key="idx" class="item-row">
            <input v-model="item.description" class="dn-input item-desc" placeholder="Description" />
            <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="dn-input item-qty" placeholder="Qty" />
            <input v-model="item.unit" class="dn-input item-unit" placeholder="Unit" />
            <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="dn-input item-price" placeholder="Price (optional)" />
            <button class="icon-btn" @click="removeItem(idx)"><font-awesome-icon :icon="['fas', 'trash']" /></button>
          </div>
          <p v-if="!form.items.length" class="items-empty">No items yet — add at least one.</p>
        </div>

        <label class="dn-field">
          <span>Notes</span>
          <textarea v-model="form.notes" class="dn-input" rows="2"></textarea>
        </label>
      </div>

      <div class="dn-modal-footer">
        <button class="penda-btn penda-btn-gray" @click="$emit('close')">Cancel</button>
        <button class="penda-btn penda-btn-primary" :disabled="saving || !canSave" @click="save">
          {{ saving ? 'Saving…' : (isEditing ? 'Save Changes' : 'Create Delivery Note') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'

const props = defineProps({
  show:         { type: Boolean, default: false },
  isEditing:    { type: Boolean, default: false },
  deliveryNote: { type: Object, default: null },
  clients:      { type: Array, default: () => [] },
  services:     { type: Array, default: () => [] },
  saving:       { type: Boolean, default: false },
})
const emit = defineEmits(['close', 'save'])

const sourceType    = ref('standalone') // standalone | invoice | estimate
const sourceSearch   = ref('')
const sourceResults  = ref([])
const prefillLoaded  = ref(false)

const form = reactive(emptyForm())

function emptyForm() {
  return {
    client_id: '',
    invoice_id: null,
    estimate_id: null,
    delivery_date: new Date().toISOString().slice(0, 10),
    delivery_address: '',
    notes: '',
    items: [],
  }
}

function setSource(type) {
  sourceType.value = type
  sourceSearch.value = ''
  sourceResults.value = []
  prefillLoaded.value = false
  form.invoice_id = null
  form.estimate_id = null
}

function addItem()          { form.items.push({ service_id: null, description: '', quantity: 1, unit: '', unit_price: null }) }
function removeItem(idx)    { form.items.splice(idx, 1) }

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}
async function api(method, path) {
  const res = await fetch('/api' + path, {
    method,
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrf() },
  })
  if (!res.ok) throw await res.json()
  return res.json()
}

const debouncedSourceSearch = (() => {
  let t
  return () => {
    clearTimeout(t)
    t = setTimeout(async () => {
      if (!sourceSearch.value) { sourceResults.value = []; return }
      const path = sourceType.value === 'invoice'
        ? `/invoices?search=${encodeURIComponent(sourceSearch.value)}&per_page=5`
        : `/estimates?search=${encodeURIComponent(sourceSearch.value)}&per_page=5`
      try {
        const res = await api('GET', path)
        sourceResults.value = res.data ?? []
      } catch { sourceResults.value = [] }
    }, 300)
  }
})()

async function loadPrefill(doc) {
  try {
    const path = sourceType.value === 'invoice'
      ? `/delivery-notes/from-invoice/${doc.id}`
      : `/delivery-notes/from-estimate/${doc.id}`
    const res = await api('GET', path)
    const prefill = res.prefill

    form.client_id    = prefill.client_id
    form.invoice_id    = prefill.invoice_id ?? null
    form.estimate_id  = prefill.estimate_id ?? null
    form.items         = prefill.items.map(i => ({ ...i, unit: i.unit ?? '' }))

    sourceResults.value = []
    sourceSearch.value  = doc.invoice_number || doc.estimate_number
    prefillLoaded.value = true
  } catch (err) {
    console.error('Failed to load prefill', err)
  }
}

const canSave = computed(() => form.client_id && form.delivery_date && form.items.length > 0)

function save() {
  emit('save', { ...form })
}

watch(() => props.show, (val) => {
  if (!val) return
  if (props.isEditing && props.deliveryNote) {
    Object.assign(form, {
      client_id: props.deliveryNote.client?.id ?? props.deliveryNote.client_id,
      invoice_id: props.deliveryNote.invoice_id ?? null,
      estimate_id: props.deliveryNote.estimate_id ?? null,
      delivery_date: props.deliveryNote.delivery_date,
      delivery_address: props.deliveryNote.delivery_address ?? '',
      notes: props.deliveryNote.notes ?? '',
      items: (props.deliveryNote.items ?? []).map(i => ({
        service_id: i.service_id ?? null,
        description: i.description,
        quantity: Number(i.quantity),
        unit: i.unit ?? '',
        unit_price: i.unit_price !== null ? Number(i.unit_price) : null,
      })),
    })
  } else {
    Object.assign(form, emptyForm())
    setSource('standalone')
    if (!form.items.length) addItem()
  }
})
</script>

<style scoped>
.dn-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.dn-modal-lg { background: white; border-radius: 12px; width: 800px; max-width: 100%; max-height: 90vh; display: flex; flex-direction: column; }
.dn-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f0e8; }
.dn-modal-header h2 { margin: 0; font-size: 18px; color: #1a1a1a; }
.dn-modal-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
.dn-modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0e8; }

.source-picker { margin-bottom: 20px; padding: 14px; background: #fafaf7; border-radius: 10px; border: 1px solid #f0f0e8; }
.source-label { font-size: 12px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
.source-options { display: flex; gap: 8px; margin-top: 8px; }
.source-btn { padding: 6px 14px; border-radius: 7px; border: 1px solid #e0e0d8; background: white; font-size: 13px; color: #555; cursor: pointer; }
.source-btn.active { background: #1a1a1a; color: #f0efe7; border-color: #1a1a1a; }
.source-lookup { margin-top: 10px; position: relative; }
.source-results { margin-top: 6px; border: 1px solid #e0e0d8; border-radius: 8px; overflow: hidden; }
.source-result { display: block; width: 100%; text-align: left; padding: 8px 12px; background: white; border: none; border-bottom: 1px solid #f4f4ee; font-size: 13px; cursor: pointer; }
.source-result:hover { background: #fafaf7; }
.source-confirm { margin: 8px 0 0; font-size: 12px; color: #2f9e44; }

.dn-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.dn-field { display: flex; flex-direction: column; gap: 5px; font-size: 13px; color: #555; margin-bottom: 14px; }
.dn-input { padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; }
.dn-input:focus { border-color: #d4a853; }

.items-block { margin-bottom: 14px; }
.items-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; }
.link-btn { background: none; border: none; color: #d4a853; font-size: 13px; cursor: pointer; }
.item-row { display: grid; grid-template-columns: 2.4fr 0.7fr 0.7fr 1fr auto; gap: 8px; margin-bottom: 8px; align-items: center; }
.item-desc, .item-qty, .item-unit, .item-price { padding: 7px 9px; font-size: 13px; }
.items-empty { font-size: 13px; color: #aaa; font-style: italic; }

.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: none; background: none; border-radius: 6px; color: #aaa; cursor: pointer; }
.icon-btn:hover { background: #f0f0e8; color: #333; }
</style>