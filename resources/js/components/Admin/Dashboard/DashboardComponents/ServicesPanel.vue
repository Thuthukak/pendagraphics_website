<template>
  <div class="activity-card">
    <div class="activity-card__header">
      <h2>Service catalogue</h2>
      <span class="count-pill">{{ activeCount }} active / {{ services.length }}</span>
    </div>

    <div v-if="loading" class="activity-card__empty">Loading…</div>
    <div v-else-if="!services.length" class="activity-card__empty">
      No services added yet.
    </div>

    <ul v-else class="service-list">
      <li v-for="s in services.slice(0, 6)" :key="s.id">
        <div class="service-list__info">
          <p class="service-list__name">{{ s.name }}</p>
          <p class="service-list__desc">{{ s.description }}</p>
        </div>
        <div class="service-list__meta">
          <span class="mono">{{ formatCurrency(s.base_price) }}</span>
          <span class="state-dot" :class="s.is_active ? 'is-active' : 'is-inactive'"
                :title="s.is_active ? 'Active' : 'Inactive'"></span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatCurrency } from './composables/statusMeta.js'

const props = defineProps({
  services: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const activeCount = computed(() => props.services.filter(s => s.is_active).length)
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

.count-pill {
  font-size: 0.72rem;
  font-weight: 600;
  color: #3B5CA8;
  background: #E8EEFB;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
}

.activity-card__empty {
  padding: 1.5rem 0;
  text-align: center;
  color: #A0AEC0;
  font-size: 0.85rem;
}

.service-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.service-list li {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.6rem 0;
  border-bottom: 1px solid #F5F6F9;
}
.service-list li:last-child { border-bottom: none; }

.service-list__info { min-width: 0; }

.service-list__name {
  margin: 0;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1B2A4A;
}

.service-list__desc {
  margin: 0.1rem 0 0;
  font-size: 0.75rem;
  color: #A0AEC0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 320px;
}

.service-list__meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 0 0 auto;
}

.mono {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 0.83rem;
  color: #1B2A4A;
}

.state-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.state-dot.is-active { background: #2F855A; }
.state-dot.is-inactive { background: #CBD5E0; }
</style>