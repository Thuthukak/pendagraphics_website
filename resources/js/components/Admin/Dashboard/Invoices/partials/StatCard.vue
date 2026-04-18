<template>
    <div class="stat-card" :class="accent">
        <div class="stat-label">{{ label }}</div>
        <div class="stat-value">{{ value }}</div>
    </div>
</template>

<script setup>
defineProps({ label: String, value: [String, Number], accent: String })
</script>

<style scoped>
.stat-card {
  background: white;
  border: 1px solid #e8e8e0;
  border-radius: 12px;
  padding: 18px 20px;
  border-left: 4px solid #e8e8e0;
}
.stat-card.teal  { border-left-color: #1abc9c; }
.stat-card.amber { border-left-color: #d4a853; }
.stat-card.red   { border-left-color: #c0392b; }
.stat-card.gray  { border-left-color: #aaa; }
.stat-label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.stat-value { font-size: 24px; font-weight: 700; color: #1a1a1a; font-variant-numeric: tabular-nums; }
</style>