<template>
  <Teleport to="body">
    <div v-if="show && estimate" class="modal-overlay" @click="$emit('close')">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Quote {{ estimate.quote_number || '#' + estimate.id }}</h2>
          <button class="close-btn" @click="$emit('close')">×</button>
        </div>

        <div class="modal-body">
          <div v-if="estimate.status === 'converted'" class="converted-banner">
            <font-awesome-icon :icon="['fas', 'circle-check']" />
            Converted to invoice
            <strong>{{ estimate.invoice?.invoice_number || ('#' + estimate.invoice_id) }}</strong>
            on {{ fmtDate(estimate.converted_at) }}
          </div>

          <div class="detail-grid">
            <div class="detail-item">
              <label>Name</label>
              <span>{{ estimate.name }}</span>
            </div>
            <div class="detail-item">
              <label>Email</label>
              <span>{{ estimate.email }}</span>
            </div>
            <div class="detail-item">
              <label>Total</label>
              <span>R{{ estimate.total_amount }}</span>
            </div>
            <div class="detail-item">
              <label>Status</label>
              <StatusBadge :status="estimate.status" />
            </div>
            <div class="detail-item">
              <label>Expiry</label>
              <span :class="{ 'text-danger': estimate.is_expired }">
                {{ estimate.expiry_date ? fmtDate(estimate.expiry_date) : '—' }}
              </span>
            </div>
            <div class="detail-item">
              <label>Created</label>
              <span>{{ fmtDate(estimate.created_at) }}</span>
            </div>
          </div>

          <div v-if="estimate.notes" class="notes-section">
            <h3>Additional Details</h3>
            <p>{{ estimate.notes }}</p>
          </div>

          <div v-if="estimate.terms" class="notes-section">
            <h3>Terms</h3>
            <p>{{ estimate.terms }}</p>
          </div>

          <div class="services-section">
            <h3>Selected Services</h3>
            <div class="services-list">
              <div v-for="service in estimate.services" :key="service.id" class="service-item">
                <span class="service-name">{{ service.name }}</span>
                <span class="service-price">R{{ service.price }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button
            v-if="estimate.status !== 'converted'"
            class="btn-convert"
            @click="$emit('convert', estimate)"
            :disabled="estimate.status === 'cancelled'"
          >
            <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" /> Convert to Invoice
          </button>
          <button
            class="btn-primary"
            @click="$emit('resend', estimate)"
            :disabled="estimate.status === 'converted'"
          >
            Resend Email
          </button>
          <button class="btn-secondary" @click="$emit('download', estimate)">Download PDF</button>
          <button class="btn-secondary" @click="$emit('close')">Close</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import StatusBadge from './StatusBadge.vue'

defineProps({
  show: { type: Boolean, default: false },
  estimate: { type: Object, default: null },
})

defineEmits(['close', 'resend', 'download', 'convert'])

function fmtDate(d) {
  return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'
}
</script>

<style scoped>
.modal-overlay {
  position: fixed !important; inset: 0 !important; background: rgba(31,31,31,0.8) !important;
  display: flex !important; align-items: center !important; justify-content: center !important; z-index: 999999 !important;
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
  padding: 20px; border-bottom: 1px solid #e2e8f0;
}
.modal-header h2 { margin: 0; color: #2d3748; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #718096; }
.modal-body { padding: 20px; }

.converted-banner {
  display: flex; align-items: center; gap: 8px;
  background: #e6fffa; border: 1px solid #2f9e8f; color: #234e52;
  padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;
}

.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px; }
.detail-item { display: flex; flex-direction: column; gap: 5px; }
.detail-item label { font-weight: 600; color: #4a5568; font-size: 12px; text-transform: uppercase; }
.detail-item span { color: #2d3748; }
.text-danger { color: #c0392b; font-weight: 600; }

.notes-section { margin-bottom: 20px; }
.notes-section h3 { margin: 0 0 10px 0; color: #2d3748; font-size: 15px; }
.notes-section p { background: #f7fafc; padding: 12px 15px; border-radius: 6px; margin: 0; color: #4a5568; line-height: 1.5; }

.services-section h3 { margin: 0 0 12px 0; color: #2d3748; font-size: 15px; }
.services-list { border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
.service-item { display: flex; justify-content: space-between; padding: 10px 15px; border-bottom: 1px solid #e2e8f0; }
.service-item:last-child { border-bottom: none; }
.service-name { color: #2d3748; font-weight: 500; }
.service-price { color: #4a5568; font-weight: 600; }

.modal-footer { padding: 18px 20px; border-top: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
.btn-primary, .btn-secondary, .btn-convert {
  padding: 8px 16px; border-radius: 6px; border: none; font-size: 14px; font-weight: 500; cursor: pointer;
}
.btn-primary { background: #4299e1; color: white; }
.btn-secondary { background: #edf2f7; color: #4a5568; border: 1px solid #e2e8f0; }
.btn-convert { background: #2f9e8f; color: white; display: flex; align-items: center; gap: 8px; }
.btn-convert:hover:not(:disabled) { background: #278174; }
.btn-primary:disabled, .btn-convert:disabled { opacity: 0.5; cursor: not-allowed; }
</style>