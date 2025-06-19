<template>
  <!-- Create/Edit Invoice Modal -->
  <div v-if="show" class="modal-overlay" @click="handleOverlayClick">
    <div class="modal" @click.stop>
      <div class="modal-header">
        <h3>{{ isEditing ? 'Edit Invoice' : 'Create Invoice' }}</h3>
        <button @click="handleClose" class="btn-close">×</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="handleSave">
          <div class="form-grid">
            <div class="form-group">
              <label>Client *</label>
              <select v-model="localForm.client_id" required class="form-select">
                <option value="">Select Client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.name }}
                </option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Invoice Date *</label>
              <input v-model="localForm.invoice_date" type="date" required class="form-input" />
            </div>
            
            <div class="form-group">
              <label>Due Date *</label>
              <input v-model="localForm.due_date" type="date" required class="form-input" />
            </div>
            
            <div class="form-group">
              <label>Notes</label>
              <textarea v-model="localForm.notes" class="form-textarea" rows="3"></textarea>
            </div>
          </div>

          <!-- Invoice Items -->
          <div class="items-section">
            <h4>Invoice Items</h4>
            <div v-for="(item, index) in localForm.items" :key="index" class="item-row">
              <div class="item-grid">
                <div class="form-group">
                  <label>Description *</label>
                  <input v-model="item.description" type="text" required class="form-input" />
                </div>
                <div class="form-group">
                  <label>Quantity *</label>
                  <input v-model="item.quantity" type="number" min="1" required class="form-input" />
                </div>
                <div class="form-group">
                  <label>Unit Price *</label>
                  <input v-model="item.unit_price" type="number" step="0.01" min="0" required class="form-input" />
                </div>
                <div class="form-group">
                  <label>Total</label>
                  <input :value="(item.quantity * item.unit_price).toFixed(2)" readonly class="form-input readonly" />
                </div>
                <div class="form-group">
                  <button @click="removeItem(index)" type="button" class="btn btn-danger btn-sm">Remove</button>
                </div>
              </div>
            </div>
            <button @click="addItem" type="button" class="btn btn-secondary">Add Item</button>
          </div>

          <div class="modal-footer">
            <button type="button" @click="handleClose" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              {{ saving ? 'Saving...' : (isEditing ? 'Update Invoice' : 'Create Invoice') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue'


export default {
  name: 'InvoiceCreateEditModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    isEditing: {
      type: Boolean,
      default: false
    },
    invoice: {
      type: Object,
      default: null
    },
    clients: {
      type: Array,
      default: () => []
    },
    saving: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    // Local form state
    const localForm = reactive({
      client_id: '',
      invoice_date: '',
      due_date: '',
      notes: '',
      items: [
        { description: '', quantity: 1, unit_price: 0 }
      ]
    })

    // Methods
    const resetForm = () => {
      Object.assign(localForm, {
        client_id: '',
        invoice_date: new Date().toISOString().split('T')[0],
        due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        notes: '',
        items: [{ description: '', quantity: 1, unit_price: 0 }]
      })
    }

    const populateForm = (invoice) => {
      if (invoice) {
        Object.assign(localForm, {
          client_id: invoice.client_id,
          invoice_date: invoice.invoice_date,
          due_date: invoice.due_date,
          notes: invoice.notes || '',
          items: invoice.items?.length ? invoice.items.map(item => ({
            description: item.description,
            quantity: item.quantity,
            unit_price: item.unit_price
          })) : [{ description: '', quantity: 1, unit_price: 0 }]
        })
      }
    }

    const addItem = () => {
      localForm.items.push({ description: '', quantity: 1, unit_price: 0 })
    }

    const removeItem = (index) => {
      localForm.items.splice(index, 1)
    }

    const handleClose = () => {
      emit('close')
    }

    const handleOverlayClick = () => {
      emit('close')
    }

    const handleSave = () => {
      emit('save', { ...localForm })
    }

    // Watchers
    watch(() => props.show, (newValue) => {
      if (newValue) {
        if (props.isEditing && props.invoice) {
          populateForm(props.invoice)
        } else {
          resetForm()
        }
      }
    })

    watch(() => props.invoice, (newInvoice) => {
      if (props.isEditing && newInvoice) {
        populateForm(newInvoice)
      }
    })

    return {
      localForm,
      addItem,
      removeItem,
      handleClose,
      handleOverlayClick,
      handleSave
    }
  }
}
</script>

<style scoped>
/* Form Elements */
.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.readonly {
  background: #f9fafb;
  color: #6b7280;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #4b5563;
}

.btn-danger {
  background: #dc2626;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #b91c1c;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #6b7280;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  color: #374151;
}

/* Modals */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 20px;
}

.modal {
  background: white;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

/* Form Layouts */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

/* Invoice Items */
.items-section {
  margin-top: 32px;
}

.items-section h4 {
  margin: 0 0 16px 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.item-row {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  background: #f9fafb;
}

.item-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr auto;
  gap: 16px;
  align-items: end;
}

/* Responsive Design */
@media (max-width: 768px) {
  .modal {
    margin: 10px;
    max-width: none;
  }
  
  .item-grid {
    grid-template-columns: 1fr;
  }
}
</style>