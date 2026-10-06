// composables/useDashboardData.js
// Pulls together everything the dashboard needs from the existing
// Invoice / Estimate / DeliveryNote / Service / Client endpoints and
// exposes it as one reactive bundle, with a single `refresh()` you can
// call whenever the date filter changes.

import { ref, reactive, computed } from 'vue'
import axios from 'axios'

export function useDashboardData() {
  const loading = ref(true)
  const error = ref(null)

  const dateRange = reactive({ from: null, to: null })

  const invoiceStats = ref(null)
  const estimateStats = ref(null)
  const deliveryStats = ref(null)

  const recentEstimates = ref([])
  const services = ref([])
  const clients = ref([])

  function withRange(extra = {}) {
    const params = { ...extra }
    if (dateRange.from) params.date_from = dateRange.from
    if (dateRange.to) params.date_to = dateRange.to
    return { params }
  }

  async function refresh() {
    loading.value = true
    error.value = null

    try {
      const [invRes, estRes, dnRes, estListRes, servicesRes, clientsRes] = await Promise.all([
        axios.get('/api/invoices/statistics', withRange()),
        axios.get('/api/estimates/statistics', withRange()),
        axios.get('/api/delivery-notes/statistics', withRange()),
        axios.get('/api/estimates', withRange({ per_page: 5, sort_by: 'created_at', sort_order: 'desc' })),
        axios.get('/api/services', { params: { per_page: 100 } }),
        axios.get('/api/clients'),
      ])

      invoiceStats.value = invRes.data.statistics
      estimateStats.value = estRes.data.statistics
      deliveryStats.value = dnRes.data.statistics
      recentEstimates.value = estListRes.data?.data ?? estListRes.data ?? []
      services.value = servicesRes.data?.data ?? servicesRes.data ?? []
      clients.value = clientsRes.data ?? []
    } catch (e) {
      error.value = e?.response?.data?.message || e.message || 'Failed to load dashboard data'
    } finally {
      loading.value = false
    }
  }

  const recentInvoices = computed(() => invoiceStats.value?.recent_invoices?.data ?? invoiceStats.value?.recent_invoices ?? [])
  const recentDeliveryNotes = computed(() => deliveryStats.value?.recent_delivery_notes?.data ?? deliveryStats.value?.recent_delivery_notes ?? [])

  const activeClientsCount = computed(() => clients.value.length)
  const activeServicesCount = computed(() => services.value.filter(s => s.is_active).length)

  return {
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
    activeServicesCount,
    refresh,
  }
}