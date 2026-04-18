<template>
    <span class="badge" :class="status">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ status: String })
const label = computed(() => ({ draft: 'Draft', sent: 'Sent', paid: 'Paid', overdue: 'Overdue', cancelled: 'Cancelled' }[props.status] ?? props.status))
</script>

<style scoped>
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.badge.draft     { background: #f5f0e8; color: #8a6a1a; }
.badge.sent      { background: #e8f0fb; color: #1e40af; }
.badge.paid      { background: #e8f7ee; color: #166534; }
.badge.overdue   { background: #fee8e8; color: #991b1b; }
.badge.cancelled { background: #f5f5f5; color: #888; }
</style>