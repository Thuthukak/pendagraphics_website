<template>
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
            <button class="btn-secondary" :disabled="deleting" @click="$emit('close')">
            Cancel
            </button>
            <button class="btn penda-btn-danger" :disabled="deleting" @click="handleDelete">
            <span v-if="deleting">⏳ Deleting…</span>
            <span v-else>Delete</span>
            </button>
        </div>
        </div>
    </div>
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
.modal { max-height: 45vh !important; }
</style>