<template>
  <div class="dashboard container-fluid mt-4 px-4 pb-5">

    <!-- Header -->
    <div class="dashboard__header">
      <div>
        <h1 class="dashboard__title">Dashboard</h1>
        <p class="dashboard__subtitle">Ledger, quotations and delivery overview</p>
      </div>

      <div class="dashboard__filters">
        <input type="date" v-model="dateRange.from" class="form-control form-control-sm" />
        <span class="range-sep">to</span>
        <input type="date" v-model="dateRange.to" class="form-control form-control-sm" />
        <button class="btn btn-sm btn-apply" @click="refresh">
          <i class="bi bi-arrow-repeat" :class="{ spin: loading }"></i>
          Apply
        </button>
      </div>
    </div>

    <div v-if="error" class="alert alert-danger dashboard__alert">{{ error }}</div>

    <!-- KPI cards -->
    <div class="row g-3 dashboard__stats">
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Revenue collected"
          :value="invoiceStats?.total_revenue ?? 0"
          currency
          icon="bi bi-cash-coin"
          accent="green"
          :loading="loading"
        />
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Outstanding"
          :value="invoiceStats?.pending_revenue ?? 0"
          currency
          icon="bi bi-hourglass-split"
          accent="amber"
          :loading="loading"
        />
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Overdue invoices"
          :value="invoiceStats?.overdue_invoices ?? 0"
          icon="bi bi-exclamation-triangle"
          accent="red"
          :loading="loading"
        />
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Open quotations"
          :value="openEstimates"
          icon="bi bi-file-earmark-text"
          accent="blue"
          :loading="loading"
        />
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Pending deliveries"
          :value="deliveryStats?.pending_delivery_notes ?? 0"
          icon="bi bi-truck"
          accent="navy"
          :loading="loading"
        />
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <StatCard
          label="Active clients"
          :value="activeClientsCount"
          icon="bi bi-people"
          accent="navy"
          :loading="loading"
        />
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 dashboard__charts">
      <div class="col-12 col-xl-4">
        <div class="chart-card">
          <h2 class="chart-card__title">Invoice status</h2>
          <StatusDoughnut
            v-if="!loading && invoiceStats"
            :breakdown="{
              draft: invoiceStats.draft_invoices,
              sent: invoiceStats.sent_invoices,
              paid: invoiceStats.paid_invoices,
              overdue: invoiceStats.overdue_invoices,
            }"
            center-label="Invoices"
          />
          <div v-else class="chart-card__placeholder"></div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="chart-card">
          <h2 class="chart-card__title">Quotation status</h2>
          <StatusDoughnut
            v-if="!loading && estimateStats"
            :breakdown="{
              pending: estimateStats.pending,
              email_failed: estimateStats.email_failed,
              completed: estimateStats.completed,
              converted: estimateStats.converted,
              cancelled: estimateStats.cancelled,
            }"
            center-label="Quotes"
          />
          <div v-else class="chart-card__placeholder"></div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="chart-card">
          <h2 class="chart-card__title">Delivery status</h2>
          <StatusDoughnut
            v-if="!loading && deliveryStats"
            :breakdown="{
              draft: deliveryStats.draft_delivery_notes,
              pending: deliveryStats.pending_delivery_notes,
              delivered: deliveryStats.delivered_delivery_notes,
              cancelled: deliveryStats.cancelled_delivery_notes,
            }"
            center-label="Deliveries"
          />
          <div v-else class="chart-card__placeholder"></div>
        </div>
      </div>

      <div class="col-12">
        <div class="chart-card">
          <div class="chart-card__header-row">
            <h2 class="chart-card__title">Revenue breakdown</h2>
            <p class="chart-card__caption">
              Average invoice: <strong class="mono">{{ formatCurrency(invoiceStats?.average_invoice_value ?? 0) }}</strong>
            </p>
          </div>
          <RevenueBar
            v-if="!loading && invoiceStats"
            :collected="Number(invoiceStats.total_revenue ?? 0)"
            :outstanding="Number(invoiceStats.pending_revenue ?? 0)"
            :quoted="Number(estimateStats?.total_value ?? 0)"
          />
          <div v-else class="chart-card__placeholder chart-card__placeholder--wide"></div>
        </div>
      </div>
    </div>

    <!-- Activity -->
    <div class="row g-3 dashboard__activity">
      <div class="col-12 col-xl-6">
        <RecentInvoicesTable :invoices="recentInvoices" :loading="loading" />
      </div>
      <div class="col-12 col-xl-6">
        <RecentEstimatesTable :estimates="recentEstimates" :loading="loading" />
      </div>
      <div class="col-12 col-xl-6">
        <RecentDeliveryNotesTable :notes="recentDeliveryNotes" :loading="loading" />
      </div>
      <div class="col-12 col-xl-6">
        <div class="row g-3 h-100">
          <div class="col-12">
            <ServicesPanel :services="services" :loading="loading" />
          </div>
          <div class="col-12">
            <ClientsPanel :clients="clients" :loading="loading" />
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useDashboardData } from './DashboardComponents/composables/useDashboardData.js'
import { formatCurrency } from './DashboardComponents/composables/statusMeta.js'

import StatCard from './DashboardComponents/StatCard.vue'
import StatusDoughnut from './DashboardComponents/StatusDoughnut.vue'
import RevenueBar from './DashboardComponents/RevenueBar.vue'
import RecentInvoicesTable from './DashboardComponents/RecentInvoicesTable.vue'
import RecentEstimatesTable from './DashboardComponents/RecentEstimatesTable.vue'
import RecentDeliveryNotesTable from './DashboardComponents/RecentDeliveryNotesTable.vue'
import ServicesPanel from './DashboardComponents/ServicesPanel.vue'
import ClientsPanel from './DashboardComponents/ClientsPanel.vue'
import 'bootstrap-icons/font/bootstrap-icons.css'

const {
  loading,
  error,
  dateRange,
  invoiceStats,
  estimateStats,
  deliveryStats,
  recentInvoices,
  recentEstimates,
  recentDeliveryNotes,
  services,
  clients,
  activeClientsCount,
  refresh,
} = useDashboardData()

const openEstimates = computed(() => {
  if (!estimateStats.value) return 0
  return (estimateStats.value.pending ?? 0) + (estimateStats.value.email_failed ?? 0)
})

onMounted(refresh)
</script>

<style scoped>
.dashboard {
  --ink: #1B2A4A;
  --muted: #94A3B8;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.dashboard__header {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.dashboard__title {
  font-weight: 800;
  color: var(--ink);
  margin: 0;
  letter-spacing: -0.02em;
}

.dashboard__subtitle {
  margin: 0.15rem 0 0;
  color: var(--muted);
  font-size: 0.9rem;
}

.dashboard__filters {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dashboard__filters .form-control {
  border-color: #E7EAF1;
  font-size: 0.82rem;
}

.range-sep {
  font-size: 0.78rem;
  color: var(--muted);
}

.btn-apply {
  background: var(--ink);
  color: #fff;
  border: none;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.35rem 0.85rem;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  border-radius: 6px;
}
.btn-apply:hover { background: #142038; color: #fff; }

.spin { animation: spin 0.9s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.dashboard__alert {
  border-radius: 10px;
  font-size: 0.88rem;
}

.dashboard__stats { margin-bottom: 0.5rem; }
.dashboard__charts { margin-top: 0.75rem; }
.dashboard__activity { margin-top: 0.75rem; }

.chart-card {
  background: #fff;
  border: 1px solid #E7EAF1;
  border-radius: 12px;
  padding: 1.1rem 1.2rem;
  height: 100%;
}

.chart-card__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--ink);
  margin: 0 0 0.9rem;
}

.chart-card__header-row {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.chart-card__caption {
  margin: 0 0 0.9rem;
  font-size: 0.8rem;
  color: var(--muted);
}

.chart-card__caption .mono {
  font-family: 'IBM Plex Mono', monospace;
  color: var(--ink);
}

.chart-card__placeholder {
  height: 128px;
  border-radius: 10px;
  background: linear-gradient(90deg, #F5F6F9 25%, #fbfbfd 50%, #F5F6F9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.3s infinite;
}
.chart-card__placeholder--wide { height: 170px; }

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>