<template>
  <div v-if="show && deliveryNote" class="dn-overlay" @click.self="$emit('close')">
    <div class="dn-modal-lg">
      <div class="dn-modal-header">
        <div>
          <h2>{{ deliveryNote.delivery_number }}</h2>
          <StatusBadge :status="deliveryNote.status" />
        </div>
        <button class="icon-btn" @click="$emit('close')"><font-awesome-icon :icon="['fas', 'xmark']" /></button>
      </div>

      <div class="dn-modal-body">
        <div class="dn-grid-2">
          <div>
            <span class="detail-label">Client</span>
            <p class="detail-value">{{ deliveryNote.client?.name }}</p>
          </div>
          <div>
            <span class="detail-label">Delivery date</span>
            <p class="detail-value">{{ fmtDate(deliveryNote.delivery_date) }}</p>
          </div>
        </div>

        <div v-if="deliveryNote.source" class="source-note">
          Created from {{ deliveryNote.source.type }} <strong>{{ deliveryNote.source.number }}</strong>
        </div>

        <div v-if="deliveryNote.delivery_address">
          <span class="detail-label">Delivery address</span>
          <p class="detail-value">{{ deliveryNote.delivery_address }}</p>
        </div>

        <div class="items-table-wrap">
          <table class="items-table">
            <thead>
              <tr><th>Description</th><th>Qty</th><th>Unit</th><th class="text-right">Price</th></tr>
            </thead>
            <tbody>
              <tr v-for="item in deliveryNote.items" :key="item.id">
                <td>{{ item.description }}</td>
                <td>{{ item.formatted_quantity }}</td>
                <td>{{ item.unit || '—' }}</td>
                <td class="text-right">{{ item.formatted_unit_price || '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="deliveryNote.status === 'delivered'" class="delivery-proof">
          <span class="detail-label">Received by</span>
          <p class="detail-value">{{ deliveryNote.received_by || '—' }} on {{ fmtDate(deliveryNote.received_date) }}</p>
        </div>

        <div v-if="deliveryNote.notes">
          <span class="detail-label">Notes</span>
          <p class="detail-value">{{ deliveryNote.notes }}</p>
        </div>
      </div>

      <div class="dn-modal-footer">
        <button class="penda-btn" @click="$emit('close')">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import StatusBadge       from '../Invoices/partials/StatusBadge.vue'

defineProps({
  show: { type: Boolean, default: false },
  deliveryNote: { type: Object, default: null },
})
defineEmits(['close'])

function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' }) : '—' }
</script>

<style scoped>
.dn-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.dn-modal-lg { background: white; border-radius: 12px; width: 620px; max-width: 100%; max-height: 90vh; display: flex; flex-direction: column; }
.dn-modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 24px; border-bottom: 1px solid #f0f0e8; }
.dn-modal-header h2 { margin: 0 0 6px; font-size: 18px; color: #1a1a1a; }
.dn-modal-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
.dn-modal-footer { display: flex; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid #f0f0e8; }

.dn-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
.detail-label { font-size: 11px; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 0.5px; }
.detail-value { margin: 4px 0 0; font-size: 14px; color: #1a1a1a; }

.source-note { font-size: 13px; color: #666; background: #fafaf7; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px; }

.items-table-wrap { margin: 16px 0; border: 1px solid #e8e8e0; border-radius: 10px; overflow: hidden; }
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead th { padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #888; background: #fafaf7; border-bottom: 1px solid #f0f0e8; text-align: left; }
.items-table tbody td { padding: 10px 12px; font-size: 13px; color: #333; border-bottom: 1px solid #f4f4ee; }
.items-table tbody tr:last-child td { border-bottom: none; }
.text-right { text-align: right; }

.delivery-proof { margin-top: 8px; padding: 10px 12px; background: #f2f9f2; border-radius: 8px; }

.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: none; background: none; border-radius: 6px; color: #aaa; cursor: pointer; }
.icon-btn:hover { background: #f0f0e8; color: #333; }
</style>