<template>
  <div class="stat-card" :class="`accent-${accent}`">
    <span class="stat-label">{{ label }}</span>
    <span class="stat-value">{{ value }}</span>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  accent: { type: String, default: 'gray' }, // teal | amber | red | green | gray | blue
})
</script>

<style scoped>
.stat-card {
  background: white;
  border: 1px solid #e8e8e0;
  border-radius: 12px;
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  border-left: 3px solid #d0d0c6;
}
.stat-label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #999;
}
.stat-value {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  font-variant-numeric: tabular-nums;
}
.accent-teal   { border-left-color: #2f9e8f; }
.accent-amber  { border-left-color: #d4a853; }
.accent-red    { border-left-color: #c0392b; }
.accent-green  { border-left-color: #4a9d5f; }
.accent-blue   { border-left-color: #4299e1; }
.accent-gray   { border-left-color: #a0aec0; }
</style>