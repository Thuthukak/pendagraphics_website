<template>
  <div class="activity-card">
    <div class="activity-card__header">
      <h2>Recent delivery notes</h2>
      <a href="/admin/delivery-notes" class="view-all">View all <i class="bi bi-arrow-right"></i></a>
    </div>

    <div v-if="loading" class="activity-card__empty">Loading…</div>
    <div v-else-if="!notes.length" class="activity-card__empty">
      Nothing dispatched yet — delivery notes appear here once created.
    </div>

    <table v-else class="activity-table">
      <thead>
        <tr>
          <th>Note</th>
          <th>Client</th>
          <th>Delivery date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="note in notes" :key="note.id">
          <td class="mono">{{ note.delivery_number }}</td>
          <td>{{ note.client?.name ?? '—' }}</td>
          <td>{{ formatDate(note.delivery_date) }}</td>
          <td><StatusBadge :status="note.status" /></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import StatusBadge from './StatusBadge.vue'
import { formatDate } from './composables/statusMeta.js'

defineProps({
  notes: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})
</script>

<style scoped>
.activity-card {
  background: #fff;
  border: 1px solid #E7EAF1;
  border-radius: 12px;
  padding: 1.1rem 1.2rem;
  height: 100%;
}

.activity-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.activity-card__header h2 {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1B2A4A;
  margin: 0;
}

.view-all {
  font-size: 0.78rem;
  color: #3B5CA8;
  text-decoration: none;
  font-weight: 600;
}
.view-all:hover { text-decoration: underline; }

.activity-card__empty {
  padding: 1.5rem 0;
  text-align: center;
  color: #A0AEC0;
  font-size: 0.85rem;
}

.activity-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.83rem;
}

.activity-table th {
  text-align: left;
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94A3B8;
  font-weight: 600;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #EEF1F6;
}

.activity-table td {
  padding: 0.55rem 0;
  border-bottom: 1px solid #F5F6F9;
  color: #4A5568;
  vertical-align: middle;
}

.activity-table tr:last-child td { border-bottom: none; }

.mono {
  font-family: 'IBM Plex Mono', monospace;
  color: #1B2A4A;
}
</style>