<template>
  <span class="status-badge" :class="status">
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true },
})

const label = computed(() => props.status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()))
</script>

<style scoped>
.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  white-space: nowrap;
}
.status-badge.pending       { background: #fffaf0; color: #c05621; border: 1px solid #f6ad55; }
.status-badge.emailed       { background: #ebf8ff; color: #2b6cb0; border: 1px solid #4299e1; }
.status-badge.email_failed  { background: #fff5f5; color: #c53030; border: 1px solid #fc8181; }
.status-badge.completed     { background: #f0fff4; color: #22543d; border: 1px solid #68d391; }
.status-badge.converted     { background: #e6fffa; color: #234e52; border: 1px solid #2f9e8f; }
.status-badge.cancelled     { background: #f7fafc; color: #4a5568; border: 1px solid #a0aec0; }
</style>