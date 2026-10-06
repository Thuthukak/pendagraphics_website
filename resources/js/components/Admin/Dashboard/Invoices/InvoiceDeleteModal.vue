<template>
    <Teleport to="body">
    <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
        <div class="modal modal-medium" @click.stop>
        <div class="modal-header bg-danger-500">
            <h3 class="text-white">Delete Invoice {{ invoice?.invoice_number }}</h3>
            <button class="btn-close text-white" @click="$emit('close')"></button>
        </div>
        <div class="modal-body text-center">
            <p class="fw-bold mb-4">Invoice #{{ invoice?.invoice_number }}</p>
            <p>Are you sure you want to delete this invoice?</p>
        </div>
        <div class="modal-footer">
            <button class="penda-btn penda-btn-gray" :disabled="deleting" @click="$emit('close')">
            Cancel
            </button>
            <button class="penda-btn penda-btn-danger" :disabled="deleting" @click="handleDelete">
            <span v-if="deleting">⏳ Deleting…</span>
            <span v-else>Delete</span>
            </button>
        </div>
        </div>
    </div>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps(['show', 'invoice'])
const emit  = defineEmits(['close', 'deleted'])

const deleting = ref(false)

async function handleDelete() {
    if (!props.invoice) return
    deleting.value = true
    try {
        const res = await fetch(`/api/invoices/${props.invoice.id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
        })
        if (!res.ok) throw await res.json()
        emit('deleted')
        emit('close')
    } catch (err) {
        alert(err?.message ?? 'Failed to delete invoice.')
    } finally {
        deleting.value = false
    }
}
</script>
<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  z-index: 50;
  padding: 20px;
  overflow-y: auto;
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #ebe6e5;
  position: sticky;
  top: 0;
  background: rgb(145, 1, 1);
  z-index: 10;
  border-radius: 12px 12px 0 0;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.modal-body {
  padding: 24px;
  flex: 1;
  overflow-y: visible;
}
</style>