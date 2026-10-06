<template>
  <div class="status-doughnut">
    <div class="status-doughnut__chart">
      <canvas ref="canvasRef"></canvas>
      <div class="status-doughnut__center">
        <div class="status-doughnut__total">{{ total }}</div>
        <div class="status-doughnut__label">{{ centerLabel }}</div>
      </div>
    </div>

    <ul class="status-doughnut__legend">
      <li v-for="item in items" :key="item.status">
        <span class="dot" :style="{ background: item.color }"></span>
        <span class="legend-label">{{ item.label }}</span>
        <span class="legend-value">{{ item.value }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import Chart from 'chart.js/auto'
import { statusMeta } from './composables/statusMeta'

const props = defineProps({
  // { status: number } e.g. { draft: 4, sent: 10, paid: 21 }
  breakdown: { type: Object, required: true },
  centerLabel: { type: String, default: 'Total' },
})

const canvasRef = ref(null)
let chartInstance = null

const items = computed(() =>
  Object.entries(props.breakdown)
    .filter(([, value]) => value !== undefined && value !== null)
    .map(([status, value]) => ({
      status,
      value,
      label: statusMeta(status).label,
      color: statusMeta(status).dot,
    }))
)

const total = computed(() => items.value.reduce((sum, i) => sum + Number(i.value || 0), 0))

function buildChart() {
  if (!canvasRef.value) return
  const ctx = canvasRef.value.getContext('2d')

  chartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: items.value.map(i => i.label),
      datasets: [{
        data: items.value.map(i => i.value),
        backgroundColor: items.value.map(i => i.color),
        borderWidth: 2,
        borderColor: '#ffffff',
        hoverOffset: 6,
      }],
    },
    options: {
      cutout: '72%',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1B2A4A',
          padding: 10,
          cornerRadius: 8,
          titleFont: { family: "'IBM Plex Mono', monospace", size: 12 },
          bodyFont: { family: "'IBM Plex Mono', monospace", size: 12 },
        },
      },
    },
  })
}

function updateChart() {
  if (!chartInstance) return
  chartInstance.data.labels = items.value.map(i => i.label)
  chartInstance.data.datasets[0].data = items.value.map(i => i.value)
  chartInstance.data.datasets[0].backgroundColor = items.value.map(i => i.color)
  chartInstance.update()
}

onMounted(buildChart)
onBeforeUnmount(() => chartInstance?.destroy())
watch(() => props.breakdown, updateChart, { deep: true })
</script>

<style scoped>
.status-doughnut {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.status-doughnut__chart {
  position: relative;
  width: 128px;
  height: 128px;
  flex: 0 0 auto;
}

.status-doughnut__center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.status-doughnut__total {
  font-family: 'IBM Plex Mono', 'SFMono-Regular', Menlo, monospace;
  font-size: 1.35rem;
  font-weight: 600;
  color: #1B2A4A;
  line-height: 1;
}

.status-doughnut__label {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94A3B8;
  margin-top: 0.2rem;
}

.status-doughnut__legend {
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1 1 auto;
  min-width: 0;
}

.status-doughnut__legend li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.2rem 0;
  font-size: 0.83rem;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex: 0 0 auto;
}

.legend-label {
  color: #4A5568;
  flex: 1 1 auto;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.legend-value {
  font-family: 'IBM Plex Mono', monospace;
  font-weight: 600;
  color: #1B2A4A;
}
</style>