<template>
  <div class="stat-card" :class="`stat-card--${accent}`">
    <div class="stat-card__icon" aria-hidden="true">
      <i :class="icon"></i>
    </div>
    <div class="stat-card__body">
      <p class="stat-card__label">{{ label }}</p>
      <p class="stat-card__value">
        <span v-if="loading" class="stat-card__skeleton"></span>
        <template v-else>{{ displayValue }}</template>
      </p>
      <p v-if="hint" class="stat-card__hint">{{ hint }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatCurrency } from './composables/statusMeta'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, default: 'bi bi-clipboard-data' },
  accent: { type: String, default: 'navy' }, // navy | amber | green | red | blue
  currency: { type: Boolean, default: false },
  hint: { type: String, default: '' },
  loading: { type: Boolean, default: false },
})

const displayValue = computed(() => {
  if (props.currency) return formatCurrency(props.value)
  return typeof props.value === 'number' ? props.value.toLocaleString() : props.value
})
</script>

<style scoped>
.stat-card {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  background: #fff;
  border: 1px solid #E7EAF1;
  border-radius: 12px;
  padding: 1.1rem 1.2rem;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  inset: 0 auto 0 0;
  width: 4px;
  background: var(--accent-color, #1B2A4A);
}

.stat-card--navy  { --accent-color: #1B2A4A; }
.stat-card--amber { --accent-color: #E8A33D; }
.stat-card--green { --accent-color: #2F855A; }
.stat-card--red   { --accent-color: #C53030; }
.stat-card--blue  { --accent-color: #3B5CA8; }

.stat-card__icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--accent-color) 12%, white);
  color: var(--accent-color);
  font-size: 1.05rem;
  flex: 0 0 auto;
}

.stat-card__label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94A3B8;
  font-weight: 600;
}

.stat-card__value {
  margin: 0.15rem 0 0;
  font-family: 'IBM Plex Mono', 'SFMono-Regular', Menlo, monospace;
  font-size: 1.45rem;
  font-weight: 600;
  color: #1B2A4A;
  line-height: 1.1;
}

.stat-card__hint {
  margin: 0.15rem 0 0;
  font-size: 0.76rem;
  color: #A0AEC0;
}

.stat-card__skeleton {
  display: inline-block;
  width: 70px;
  height: 1.1rem;
  border-radius: 4px;
  background: linear-gradient(90deg, #EEF1F6 25%, #f7f8fa 50%, #EEF1F6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.3s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>