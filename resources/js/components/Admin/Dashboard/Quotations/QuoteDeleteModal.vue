<template>
  <div v-if="show && estimate" class="modal-overlay" @click="$emit('close')">
    <div class="modal" @click.stop>
      <div class="modal-header">
        <h2>Delete Quotation</h2>
        <button class="close-btn" @click="$emit('close')">×</button>
      </div>
      <div class="modal-body">
        <p>
          Are you sure you want to delete quote
          <strong>{{ estimate.quote_number || '#' + estimate.id }}</strong>
          for <strong>{{ estimate.name }}</strong>? This action cannot be undone.
        </p>
        <p v-if="error" class="error-msg">{{ error }}</p>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" @click="$emit('close')" :disabled="deleting">Cancel</button>
        <button class="btn-danger" @click="confirmDelete" :disabled="deleting">
          {{ deleting ? 'Deleting…' : 'Delete Quotation' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  estimate: { type: Object, default: null },
})

const emit = defineEmits(['close', 'deleted'])

const deleting = ref(false)
const error = ref('')

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function confirmDelete() {
  if (!props.estimate) return
  deleting.value = true
  error.value = ''

  try {
    const res = await fetch(`/api/estimates/${props.estimate.id}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
      },
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Failed to delete quotation.')

    emit('deleted', props.estimate)
  } catch (e) {
    error.value = e.message || 'Failed to delete quotation.'
  } finally {
    deleting.value = false
  }
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
.modal-header h2 { margin: 0; font-size: 17px; color: #1a1a1a; }
.close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; }
.modal-body { padding: 20px; font-size: 14px; color: #444; line-height: 1.5; }
.error-msg { color: #c0392b; font-size: 13px; margin-top: 10px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid #e8e8e0; }
.btn-secondary, .btn-danger { padding: 9px 18px; border-radius: 7px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
.btn-secondary { background: #edf2f7; color: #4a5568; }
.btn-danger { background: #e53e3e; color: white; }
.btn-danger:hover:not(:disabled) { background: #c53030; }
.btn-secondary:disabled, .btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }
</style>