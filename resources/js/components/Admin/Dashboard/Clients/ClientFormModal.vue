<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { useNotify } from '@/composables/useToast.js'

const notify = useNotify()

const props = defineProps({
    show: Boolean,
    client: Object,
    isEditing: Boolean
})

const emit = defineEmits([
    'close',
    'saved'
])

const saving = ref(false)
const errors = ref({})

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
    tax_number: ''
})

watch(() => props.client, (client) => {
    form.value = {
        name: client?.name ?? '',
        email: client?.email ?? '',
        phone: client?.phone ?? '',
        address: client?.address ?? '',
        city: client?.city ?? '',
        state: client?.state ?? '',
        postal_code: client?.postal_code ?? '',
        country: client?.country ?? '',
        tax_number: client?.tax_number ?? ''
    }
    errors.value = {}
}, { immediate: true })

const save = async () => {
    if (saving.value) return

    saving.value = true
    errors.value = {}

    try {
        if (props.isEditing) {
            await axios.put(`/api/clients/${props.client.id}`, form.value)
        } else {
            await axios.post('/api/clients', form.value)
        }

        notify?.success?.(props.isEditing ? 'Client updated successfully' : 'Client created successfully')
        emit('saved')
        emit('close')
    } catch (e) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
            notify?.error?.('Please fix the errors below')
        } else {
            notify?.error?.('Failed to save client')
        }
    } finally {
        saving.value = false
    }
}

const close = () => {
    if (saving.value) return
    errors.value = {}
    emit('close')
}
</script>

<template>
    <teleport to="body">
        <div v-if="props.show" class="modal-overlay" @click.self="close">
            <div class="modal-content">
                <h2>{{ props.isEditing ? 'Edit Customer' : 'Add Customer' }}</h2>
                <form @submit.prevent="save">
                    <div class="form-field">
                        <label for="name">Name</label>
                        <input v-model="form.name" id="name" required :class="{ 'has-error': errors.name }" />
                        <span v-if="errors.name" class="field-error">{{ errors.name[0] }}</span>
                    </div>

                    <div class="form-field">
                        <label for="email">Email</label>
                        <input v-model="form.email" id="email" type="email" :class="{ 'has-error': errors.email }" />
                        <span v-if="errors.email" class="field-error">{{ errors.email[0] }}</span>
                    </div>

                    <div class="form-field">
                        <label for="phone">Phone</label>
                        <input v-model="form.phone" id="phone" :class="{ 'has-error': errors.phone }" />
                        <span v-if="errors.phone" class="field-error">{{ errors.phone[0] }}</span>
                    </div>

                    <div class="form-field">
                        <label for="address">Address</label>
                        <input v-model="form.address" id="address" :class="{ 'has-error': errors.address }" />
                        <span v-if="errors.address" class="field-error">{{ errors.address[0] }}</span>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="city">City</label>
                            <input v-model="form.city" id="city" :class="{ 'has-error': errors.city }" />
                            <span v-if="errors.city" class="field-error">{{ errors.city[0] }}</span>
                        </div>

                        <div class="form-field">
                            <label for="state">State</label>
                            <input v-model="form.state" id="state" :class="{ 'has-error': errors.state }" />
                            <span v-if="errors.state" class="field-error">{{ errors.state[0] }}</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="postal_code">Postal Code</label>
                            <input v-model="form.postal_code" id="postal_code" :class="{ 'has-error': errors.postal_code }" />
                            <span v-if="errors.postal_code" class="field-error">{{ errors.postal_code[0] }}</span>
                        </div>

                        <div class="form-field">
                            <label for="country">Country</label>
                            <input v-model="form.country" id="country" :class="{ 'has-error': errors.country }" />
                            <span v-if="errors.country" class="field-error">{{ errors.country[0] }}</span>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="tax_number">Tax Number</label>
                        <input v-model="form.tax_number" id="tax_number" :class="{ 'has-error': errors.tax_number }" />
                        <span v-if="errors.tax_number" class="field-error">{{ errors.tax_number[0] }}</span>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" :disabled="saving" @click="close">Cancel</button>
                        <button type="submit" class="btn-primary" :disabled="saving">
                            {{ saving ? 'Saving…' : (props.isEditing ? 'Update' : 'Create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </teleport>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 26, 26, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.modal-content h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -0.3px;
    margin: 0 0 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 14px;
}

.form-field label {
    font-size: 12px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-field input {
    padding: 9px 12px;
    border: 1px solid #e0e0d8;
    border-radius: 8px;
    background: #fafaf7;
    font-size: 14px;
    color: #1a1a1a;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.form-field input:focus {
    border-color: #d4a853;
    box-shadow: 0 0 0 3px rgba(212, 168, 83, 0.1);
    background: #fff;
}

.form-field input.has-error {
    border-color: #c0392b;
}

.field-error {
    font-size: 12px;
    color: #c0392b;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn-primary,
.btn-secondary {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: background 0.15s, opacity 0.15s;
}

.btn-primary {
    background: #1a1a1a;
    color: #f0efe7;
}
.btn-primary:hover:not(:disabled) { background: #333; }

.btn-secondary {
    background: #f0f0e8;
    color: #555;
}
.btn-secondary:hover:not(:disabled) { background: #e6e6dc; }

.btn-primary:disabled,
.btn-secondary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>