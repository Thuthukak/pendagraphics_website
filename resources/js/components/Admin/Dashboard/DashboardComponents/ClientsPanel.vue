<template>
  <div class="activity-card">
    <div class="activity-card__header">
      <h2>Clients</h2>
      <span class="count-pill">{{ clients.length }} active</span>
    </div>

    <input
      v-model="search"
      type="text"
      class="form-control form-control-sm client-search"
      placeholder="Search clients…"
    />

    <div v-if="loading" class="activity-card__empty">Loading…</div>
    <div v-else-if="!filtered.length" class="activity-card__empty">
      No clients match "{{ search }}".
    </div>

    <ul v-else class="client-list">
      <li v-for="c in filtered.slice(0, 6)" :key="c.id">
        <div class="avatar">{{ initials(c.name) }}</div>
        <div class="client-list__info">
          <p class="client-list__name">{{ c.name }}</p>
          <p class="client-list__email">{{ c.email || c.phone || 'No contact on file' }}</p>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  clients: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const search = ref('')

const filtered = computed(() => {
  if (!search.value.trim()) return props.clients
  const term = search.value.toLowerCase()
  return props.clients.filter(c =>
    c.name?.toLowerCase().includes(term) || c.email?.toLowerCase().includes(term)
  )
})

function initials(name = '') {
  return name.split(' ').filter(Boolean).slice(0, 2).map(w => w[0]?.toUpperCase()).join('') || '—'
}
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
  margin-bottom: 0.7rem;
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
  color: #2F855A;
  background: #E6F4EC;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
}

.client-search {
  margin-bottom: 0.75rem;
  border-color: #E7EAF1;
}
.client-search:focus {
  border-color: #3B5CA8;
  box-shadow: 0 0 0 0.15rem rgba(59, 92, 168, 0.15);
}

.activity-card__empty {
  padding: 1.5rem 0;
  text-align: center;
  color: #A0AEC0;
  font-size: 0.85rem;
}

.client-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.client-list li {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #F5F6F9;
}
.client-list li:last-child { border-bottom: none; }

.avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #1B2A4A;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  flex: 0 0 auto;
}

.client-list__info { min-width: 0; }

.client-list__name {
  margin: 0;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1B2A4A;
}

.client-list__email {
  margin: 0.05rem 0 0;
  font-size: 0.75rem;
  color: #A0AEC0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>