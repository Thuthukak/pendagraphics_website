<template>
  <div class="revenue-bar">
    <canvas ref="canvasRef"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  collected: { type: Number, default: 0 },
  outstanding: { type: Number, default: 0 },
  quoted: { type: Number, default: 0 },
})

const canvasRef = ref(null)
let chartInstance = null

function buildChart() {
  const ctx = canvasRef.value.getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Collected', 'Outstanding', 'Quoted (open)'],
      datasets: [{
        data: [props.collected, props.outstanding, props.quoted],
        backgroundColor: ['#2F855A', '#E8A33D', '#3B5CA8'],
        borderRadius: 6,
        maxBarThickness: 46,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1B2A4A',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: (item) => new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(item.raw),
          },
        },
      },
      scales: {
        x: {
          grid: { color: '#EEF1F6' },
          ticks: {
            font: { family: "'IBM Plex Mono', monospace", size: 11 },
            callback: (v) => 'R' + Number(v).toLocaleString(),
          },
        },
        y: {
          grid: { display: false },
          ticks: { font: { size: 12 }, color: '#4A5568' },
        },
      },
    },
  })
}

function updateChart() {
  if (!chartInstance) return
  chartInstance.data.datasets[0].data = [props.collected, props.outstanding, props.quoted]
  chartInstance.update()
}

onMounted(buildChart)
onBeforeUnmount(() => chartInstance?.destroy())
watch(() => [props.collected, props.outstanding, props.quoted], updateChart)
</script>

<style scoped>
.revenue-bar {
  position: relative;
  height: 170px;
}
</style>