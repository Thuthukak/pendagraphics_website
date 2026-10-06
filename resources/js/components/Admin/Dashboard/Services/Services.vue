<template>
  <div class="svc-root">
    <main class="svc-main">
      <section class="svc-section">
        <!-- Top bar -->
        <div class="svc-topbar">
          <div>
            <h1 class="svc-title">Manage Services</h1>
            <p class="svc-subtitle">{{ paginationData ? paginationData.total : services.length }} services total</p>
          </div>
          <button class="penda-btn penda-btn-primary" @click="openAddModal">
            <font-awesome-icon :icon="['fas', 'plus']" /> Add Service
          </button>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
          <div class="search-wrap">
            <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
            <input
              type="text"
              class="filter-search"
              placeholder="Search services..."
              v-model="searchQuery"
              @input="debounceSearch"
            >
          </div>

          <select class="filter-select" v-model="statusFilter" @change="fetchServices">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>

          <select class="filter-select-sm" v-model="sortBy" @change="fetchServices">
            <option value="created_at">Date Created</option>
            <option value="name">Service</option>
            <option value="base_price">Price</option>
            <option value="updated_at">Last Updated</option>
          </select>

          <select class="filter-select-sm" v-model="sortOrder" @change="fetchServices">
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>

          <select class="filter-select-sm" v-model="perPage" @change="fetchServices">
            <option value="10">10 / page</option>
            <option value="15">15 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
          </select>

          <button class="btn-reset" @click="resetFilters">Reset</button>
        </div>

        <!-- Services table (desktop) -->
        <div class="svc-table-wrap d-none d-md-block">
          <table class="svc-table">
            <thead>
              <tr>
                <th @click="sort('name')" class="sortable">Service</th>
                <th>Description</th>
                <th @click="sort('base_price')" class="sortable text-right">Price</th>
                <th @click="sort('is_active')" class="sortable">Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="5" class="table-state">Loading services…</td></tr>
              <tr v-else-if="services.length === 0"><td colspan="5" class="table-state">No services found</td></tr>
              <tr v-else v-for="service in services" :key="service.id" class="svc-row">
                <td class="svc-num">{{ service.name }}</td>
                <td>
                  <span :title="service.description">
                    {{ truncateText(service.description, 100) }}
                  </span>
                </td>
                <td class="text-right font-med">R{{ service.base_price }}</td>
                <td>
                  <span class="status-badge" :class="service.is_active ? 'status-active' : 'status-inactive'">
                    {{ service.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="row-actions" @click.stop>
                  <button class="icon-btn" @click="openEditModal(service)" title="Edit service">
                    <font-awesome-icon :icon="['fas', 'pencil']" />
                  </button>
                  <button class="icon-btn" @click="openDeleteModal(service)" title="Delete service">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile card layout -->
        <div class="d-block d-md-none">
          <div v-if="loading" class="table-state">Loading services…</div>
          <div v-else-if="services.length === 0" class="table-state">No services found</div>
          <div class="svc-card" v-for="service in services" :key="service.id" v-else>
            <div class="svc-card-head">
              <h5 class="svc-card-title">{{ service.name }}</h5>
              <div class="svc-card-actions">
                <button class="icon-btn" @click="openEditModal(service)" title="Edit service">
                  <font-awesome-icon :icon="['fas', 'pencil']" />
                </button>
                <button class="icon-btn" @click="openDeleteModal(service)" title="Delete service">
                  <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
              </div>
            </div>
            <p class="svc-card-desc" :title="service.description">
              {{ truncateText(service.description, 120) }}
            </p>
            <div class="svc-card-meta">
              <div>
                <small>Price</small>
                <strong>R{{ service.base_price }}</strong>
              </div>
              <div>
                <small>Status</small>
                <span class="status-badge" :class="service.is_active ? 'status-active' : 'status-inactive'">
                  {{ service.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="paginationData && paginationData.last_page > 1">
          <span class="pag-info">{{ paginationData.from || 0 }}–{{ paginationData.to || 0 }} of {{ paginationData.total || 0 }}</span>
          <div class="pag-controls">
            <button
              class="pag-btn"
              :disabled="paginationData.current_page === 1"
              @click="goToPage(paginationData.current_page - 1)"
            >‹</button>
            <span class="pag-page">{{ paginationData.current_page }} / {{ paginationData.last_page }}</span>
            <button
              class="pag-btn"
              :disabled="paginationData.current_page === paginationData.last_page"
              @click="goToPage(paginationData.current_page + 1)"
            >›</button>
          </div>
        </div>
      </section>
    </main>

    <!-- Service Modal -->
    <ServiceCreateEditModal
      :show="showServiceModal"
      :service="currentService"
      :is-submitting="isSubmitting"
      @close="closeModal"
      @submit="handleServiceSubmit"
      ref="serviceModal"
    />

    <!-- Delete Confirmation Modal -->
    <ServiceDeleteConfirmationModal
      :show="showDeleteModal"
      :service="serviceToDelete"
      :is-deleting="isDeleting"
      @close="closeDeleteConfirmModal"
      @confirm="deleteService"
    />

    <!-- Toasts -->
    <div class="toast-container">
      <div v-if="showToast" class="toast" :class="toastType">
        {{ toastMessage }}
        <button type="button" class="toast-close" @click="hideToast">&times;</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ServiceCreateEditModal from "./ServiceCreateEditModal.vue";
import ServiceDeleteConfirmationModal from "./ServiceDeleteConfirmationModal.vue";

export default {
  components: {
    ServiceCreateEditModal,
    ServiceDeleteConfirmationModal
  },

  data() {
    return {
      services: [],
      paginationData: null,
      searchQuery: "",
      statusFilter: "",
      sortBy: "created_at",
      sortOrder: "desc",
      perPage: 15,
      currentPage: 1,
      loading: true,
      isSubmitting: false,
      isDeleting: false,
      currentService: null,
      serviceToDelete: null,
      showServiceModal: false,
      showDeleteModal: false,
      showToast: false,
      toastMessage: "",
      toastType: "success",
      searchTimeout: null,
    };
  },

  mounted() {
    this.fetchServices();
  },

  methods: {
    async fetchServices() {
      this.loading = true;
      try {
        const params = {
          page: this.currentPage,
          per_page: this.perPage,
          sort_by: this.sortBy,
          sort_order: this.sortOrder,
        };

        if (this.searchQuery.trim()) {
          params.search = this.searchQuery.trim();
        }

        if (this.statusFilter) {
          params.status = this.statusFilter;
        }

        const response = await axios.get("/api/services", { params });

        if (response.data.data) {
          this.services = response.data.data;
          this.paginationData = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to
          };
        } else {
          this.services = response.data;
          this.paginationData = null;
        }
      } catch (error) {
        this.displayToast("Error fetching services. Please try again.", "error");
        console.error("Error fetching services:", error);
      } finally {
        this.loading = false;
      }
    },

    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.currentPage = 1;
        this.fetchServices();
      }, 500);
    },

    sort(field) {
      if (this.sortBy === field) {
        this.sortOrder = this.sortOrder === "asc" ? "desc" : "asc";
      } else {
        this.sortBy = field;
        this.sortOrder = "asc";
      }
      this.fetchServices();
    },

    goToPage(page) {
      if (page >= 1 && page <= this.paginationData.last_page && page !== this.currentPage) {
        this.currentPage = page;
        this.fetchServices();
      }
    },

    resetFilters() {
      this.searchQuery = "";
      this.statusFilter = "";
      this.sortBy = "created_at";
      this.sortOrder = "desc";
      this.currentPage = 1;
      this.fetchServices();
    },

    truncateText(text, maxLength) {
      if (!text) return '';
      return text.length > maxLength
        ? text.substring(0, maxLength) + '...'
        : text;
    },

    openAddModal() {
      this.currentService = null;
      this.showServiceModal = true;
    },

    openEditModal(service) {
      this.currentService = service;
      this.showServiceModal = true;
    },

    openDeleteModal(service) {
      this.serviceToDelete = service;
      this.showDeleteModal = true;
    },

    closeModal() {
      this.showServiceModal = false;
      this.currentService = null;
    },

    closeDeleteConfirmModal() {
      this.showDeleteModal = false;
      this.serviceToDelete = null;
    },

    async handleServiceSubmit(formData) {
      this.isSubmitting = true;

      try {
        if (this.currentService) {
          await this.updateService(formData);
        } else {
          await this.createService(formData);
        }
      } catch (error) {
        console.error("Form submission error:", error);

        if (error.response && error.response.data && error.response.data.errors) {
          const serverErrors = error.response.data.errors;
          this.$refs.serviceModal.setErrors(serverErrors);
        } else {
          this.displayToast(
            `Error ${this.currentService ? 'updating' : 'creating'} service. Please try again.`,
            "error"
          );
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    async createService(formData) {
      const data = new FormData();
      data.append('name', formData.name);
      data.append('description', formData.description);
      data.append('base_price', formData.base_price);
      data.append('is_active', formData.is_active);

      await axios.post("/api/services", data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      this.fetchServices();
      this.closeModal();
      this.displayToast("Service added successfully!", "success");
    },

    async updateService(formData) {
      const data = new FormData();
      data.append('name', formData.name);
      data.append('description', formData.description);
      data.append('base_price', formData.base_price);
      data.append('is_active', formData.is_active);
      data.append('_method', 'PUT');

      await axios.post(`/api/services/${this.currentService.id}`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      this.fetchServices();
      this.closeModal();
      this.displayToast("Service updated successfully!", "success");
    },

    async deleteService() {
      if (!this.serviceToDelete) return;

      this.isDeleting = true;

      try {
        await axios.delete(`/api/services/${this.serviceToDelete.id}`);

        this.fetchServices();
        this.closeDeleteConfirmModal();
        this.displayToast("Service deleted successfully!", "success");
      } catch (error) {
        console.error("Error deleting service:", error);
        this.displayToast("Error deleting service. Please try again.", "error");
      } finally {
        this.isDeleting = false;
      }
    },

    displayToast(message, type = "success") {
      this.toastMessage = message;
      this.toastType = type;
      this.showToast = true;

      setTimeout(() => {
        this.hideToast();
      }, 5000);
    },

    hideToast() {
      this.showToast = false;
    }
  }
};
</script>

<style scoped>
/* ── Root layout ─────────────────────────────────────────────────────────── */
.svc-root { display: block; min-height: 100vh; background: #f8f7f4}
.svc-main { flex: 1; overflow: auto; }
.svc-section { padding: 36px 40px; max-width: 1280px; }

/* ── Top bar ─────────────────────────────────────────────────────────────── */
.svc-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
.svc-title { font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; margin: 0 0 4px}
.svc-subtitle { font-size: 13px; color: #888; margin: 0; }

.btn-create {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 20px; background: #1a1a1a; color: #f0efe7;
  border: none; border-radius: 8px; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: background 0.15s;
}
.btn-create:hover { background: #333; }

/* ── Filter bar ──────────────────────────────────────────────────────────── */
.filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #aaa; }
.filter-search {
  width: 100%; padding: 8px 12px 8px 32px;
  border: 1px solid #e0e0d8; border-radius: 8px; background: white;
  font-size: 14px; color: #1a1a1a; outline: none; box-sizing: border-box;
}
.filter-search:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
.filter-select {
  padding: 8px 10px; border: 1px solid #e0e0d8; border-radius: 8px;
  background: white; font-size: 13px; color: #333; outline: none; cursor: pointer;
}
.filter-select:focus { border-color: #d4a853; }
.filter-select-sm { padding: 5px 8px; border: 1px solid #e0e0d8; border-radius: 6px; font-size: 13px; background: white; color: #333; cursor: pointer; }
.btn-reset { padding: 8px 14px; border: 1px solid #e0e0d8; background: white; border-radius: 8px; font-size: 13px; color: #555; cursor: pointer; }
.btn-reset:hover { border-color: #d4a853; color: #333; }

/* ── Table ───────────────────────────────────────────────────────────────── */
.svc-table-wrap { background: white; border-radius: 12px; border: 1px solid #e8e8e0; overflow: hidden; margin-bottom: 16px; }
.svc-table { width: 100%; border-collapse: collapse; }
.svc-table thead th {
  padding: 12px 16px; font-size: 12px; font-weight: 600; color: #888;
  text-transform: uppercase; letter-spacing: 0.5px;
  border-bottom: 1px solid #f0f0e8; background: #fafaf7; text-align: left; white-space: nowrap;
}
.svc-table thead th.sortable { cursor: pointer; user-select: none; }
.svc-table thead th.sortable:hover { color: #444; }
.svc-table thead th.text-right { text-align: right; }
.svc-table tbody td { padding: 13px 16px; font-size: 14px; color: #333; border-bottom: 1px solid #f4f4ee; vertical-align: middle; }
.svc-row { transition: background 0.1s; }
.svc-row:hover { background: #fafaf7; }
.svc-row:last-child td { border-bottom: none; }
.svc-num { font-weight: 600; color: #1a1a1a; }
.text-right { text-align: right; }
.font-med { font-weight: 500; }
.table-state { text-align: center; padding: 48px; color: #aaa; font-style: italic; }

/* ── Status badge ────────────────────────────────────────────────────────── */
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-active { background: #e6f7ed; color: #1e8449; }
.status-inactive { background: #f0f0e8; color: #888; }

/* ── Row actions ─────────────────────────────────────────────────────────── */
.row-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px; border: none; background: none; border-radius: 6px;
  color: #aaa; cursor: pointer; transition: all 0.15s;
}
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn svg { width: 15px; height: 15px; }

/* ── Mobile cards ────────────────────────────────────────────────────────── */
.svc-card { background: white; border: 1px solid #e8e8e0; border-radius: 12px; padding: 16px; margin-bottom: 12px; transition: all 0.15s; }
.svc-card:hover { border-color: #d4a853; }
.svc-card-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
.svc-card-title { font-size: 16px; font-weight: 600; color: #1a1a1a; margin: 0; font-family: 'Playfair Display', Georgia, serif; }
.svc-card-actions { display: flex; gap: 2px; }
.svc-card-desc { font-size: 13px; color: #888; margin: 0 0 12px; }
.svc-card-meta { display: flex; gap: 24px; }
.svc-card-meta small { display: block; color: #aaa; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
.svc-card-meta strong { color: #1a1a1a; font-size: 14px; }

/* ── Pagination ──────────────────────────────────────────────────────────── */
.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn {
  width: 30px; height: 30px; border: 1px solid #e0e0d8; border-radius: 6px;
  background: white; color: #555; cursor: pointer; font-size: 16px;
  display: flex; align-items: center; justify-content: center; transition: all 0.15s;
}
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-btn:hover:not(:disabled) { border-color: #d4a853; color: #d4a853; }
.pag-page { font-size: 13px; color: #555; }

/* ── Toasts ──────────────────────────────────────────────────────────────── */
.toast-container { position: fixed; top: 20px; right: 20px; z-index: 2000000; }
.toast {
  background: white; padding: 15px 20px; border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 10px;
  border-left: 4px solid #4299e1; max-width: 300px;
  display: flex; align-items: center; justify-content: space-between; gap: 12px; font-size: 14px;
}
.toast.success { border-left-color: #48bb78; }
.toast.error { border-left-color: #f56565; }
.toast-close { background: none; border: none; font-size: 16px; color: #aaa; cursor: pointer; line-height: 1; }

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 900px) {
  .svc-section { padding: 20px; }
}
</style>