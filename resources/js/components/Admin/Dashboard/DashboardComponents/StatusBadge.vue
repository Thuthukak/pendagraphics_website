<template>
  <span class="status-badge" :style="{ background: meta.bg, color: meta.fg }">
    <span class="dot" :style="{ background: meta.dot }"></span>
    {{ meta.label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { statusMeta } from './composables/statusMeta'

const props = defineProps({ status: { type: String, default: '' } })
const meta = computed(() => statusMeta(props.status))
</script>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.22rem 0.6rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: capitalize;
  white-space: nowrap;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
</style>