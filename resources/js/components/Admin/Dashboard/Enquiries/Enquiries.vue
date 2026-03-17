<template>
  <div class="container-fluid py-4">
    <!-- Header with add button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Manage Enquiries</h2>
      <button class="penda-btn penda-btn-primary" @click="openAddModal">
        <i class="bi bi-plus-circle me-1"></i> Add Enquiry
      </button>
    </div>

<div class="card rounded shadow p-4 mb-5">
    <!-- Search and Filter Controls -->
    <div class="row mb-4">
      <!-- Search bar -->
      <div class="col-md-6 mb-3">
        <div class="input-group">
          <span class="input-group-text bg-light">
            <i class="bi bi-search"></i>
          </span>
          <input 
            type="text" 
            class="form-control" 
            placeholder="Search by name, email, or phone..." 
            v-model="searchQuery"
            @input="debounceSearch"
          >
        </div>
      </div>

      <!-- Status filter -->
      <div class="col-md-3 mb-3">
        <select class="form-select" v-model="statusFilter" @change="fetchEnquiries">
          <option value="">All Status</option>
          <option value="new">New</option>
          <option value="in_progress">In Progress</option>
          <option value="waiting_for_response">Awaiting Response</option>
          <option value="resolved">Resolved</option>
          <option value="spam">Spam</option>
          <option value="closed">Closed</option>
        </select>
      </div>

      <!-- Items per page -->
      <div class="col-md-3 mb-3">
        <select class="form-select" v-model.number="perPage" @change="fetchEnquiries">
          <option :value="10">10 per page</option>
          <option :value="15">15 per page</option>
          <option :value="25">25 per page</option>
          <option :value="50">50 per page</option>
        </select>
      </div>
    </div>

    <!-- Sort Controls -->
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="d-flex align-items-center">
          <label class="form-label me-2 mb-0">Sort by:</label>
          <select class="form-select form-select-sm" v-model="sortBy" @change="fetchEnquiries">
            <option value="created_at">Date Created</option>
            <option value="service">Service</option>
            <option value="updated_at">Last Updated</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="d-flex align-items-center">
          <label class="form-label me-2 mb-0">Order:</label>
          <select class="form-select form-select-sm" v-model="sortOrder" @change="fetchEnquiries">
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </div>
      </div>
      <div class="col-md-5 text-end">
        <span class="text-muted small" v-if="paginationData">
          Showing {{ paginationData.from || 0 }}-{{ paginationData.to || 0 }} of {{ paginationData.total || 0 }} results
        </span>
      </div>
    </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading enquiries...</p>
    </div>

    <!-- No enquiries message -->
    <div v-else-if="enquiries.length === 0" class="text-center my-5">
      <i class="bi bi-inbox display-4 text-muted"></i>
      <p class="lead mt-3">No enquiries found</p>
      <p class="text-muted" v-if="searchQuery || statusFilter">Try changing your search criteria</p>
      <button v-else class="penda-btn penda-btn-outline-secondary mt-2" @click="openAddModal">
        Add your first enquiry
      </button>
    </div>

    <!-- Enquiries table and cards -->
    <div v-else>
      <!-- Desktop table -->
      <div class="d-none d-md-block">
        <div class="table-responsive rounded shadow">
          <table class="table table-hover border table-fixed">
          <thead class="table-light">
            <tr>
              <th style="width: 140px;">Name</th>
              <th style="width: 120px;">Phone</th>
              <th style="width: 180px;">Email</th>
              <th style="width: 140px;">Service</th>
              <th >Status</th>
              <th >Date</th>
              <th class="actions-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="enquiry in enquiries" :key="enquiry.id" class="align-middle">
              <td class="text-truncate" :title="enquiry.name">{{ enquiry.name }}</td>
              <td>
                <button 
                  class="btn btn-link text-decoration-none p-0 text-truncate d-block" 
                  @click="openPhoneModal(enquiry.phone)"
                  type="button"
                  style="max-width: 100%;"
                >
                  {{ enquiry.phone }}
                </button>
              </td>
              <td class="text-truncate" :title="enquiry.email || 'N/A'">{{ enquiry.email || 'N/A' }}</td>
              <td class="" :title="formatService(enquiry.service)">{{ formatService(enquiry.service) }}</td>
              <td>
                <span 
                  class="badge" 
                  :class="statusBadgeClass(enquiry.status)"
                >
                  {{ formatStatus(enquiry.status) }}
                </span>
              </td>
              <td class="text-nowrap small">{{ formatDate(enquiry.created_at) }}</td>
              <td class="actions-column">
                <div class="d-flex gap-1 justify-content-end flex-nowrap">
                  <button 
                    class="btn btn-sm btn-outline-secondary" 
                    @click="openViewModal(enquiry)"
                    type="button"
                    title="View"
                  >
                    <font-awesome-icon :icon="['fas', 'eye']" />
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-secondary" 
                    @click="openEditModal(enquiry)"
                    type="button"
                    title="Edit"
                  >
                    <font-awesome-icon :icon="['fas', 'pencil']" />
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger" 
                    @click="confirmDelete(enquiry.id)"
                    type="button"
                    title="Delete"
                  >
                    <font-awesome-icon :icon="['fas', 'trash']" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>

      <!-- Mobile cards -->
      <div class="d-block d-md-none">
        <div class="card mb-3" v-for="enquiry in enquiries" :key="enquiry.id">
          <div class="card-body">
            <h5 class="card-title">{{ enquiry.name }}</h5>
            <p class="card-text mb-2">
              <strong>Phone:</strong> 
              <button 
                class="btn btn-link text-decoration-none p-0" 
                @click="openPhoneModal(enquiry.phone)"
                type="button"
              >
                {{ enquiry.phone }}
              </button>
            </p>
            <p class="card-text mb-2"><strong>Email:</strong> {{ enquiry.email || 'N/A' }}</p>
            <p class="card-text mb-2"><strong>Service:</strong> {{ formatService(enquiry.service) }}</p>
            <p class="card-text mb-2"><strong>Message:</strong> {{ enquiry.message }}</p>
            <p class="card-text mb-2">
              <strong>Status:</strong>
              <span 
                class="badge ms-2" 
                :class="statusBadgeClass(enquiry.status)"
              >
                {{ formatStatus(enquiry.status) }}
              </span>
            </p>
            <p class="card-text mb-3"><small class="text-muted">{{ formatDate(enquiry.created_at) }}</small></p>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-primary" @click="openViewModal(enquiry)" type="button">
                <i class="bi bi-eye"></i> View
              </button>
              <button class="btn btn-sm btn-outline-secondary" @click="openEditModal(enquiry)" type="button">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(enquiry.id)" type="button">
                <i class="bi bi-trash"></i> Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4" v-if="paginationData && enquiries.length > 0">
      <div class="col-md-7">
        <span class="text-muted small">
          Showing {{ paginationData.from || 0 }}-{{ paginationData.to || 0 }} of {{ paginationData.total || 0 }} results
        </span>
      </div>
      <div class="col-md-5 text-end">
        <button 
          class="penda-btn penda-btn-sm penda-btn-outline-secondary me-2" 
          :disabled="paginationData.current_page === 1"
          @click="goToPage(paginationData.current_page - 1)"
          type="button"
        >
          Previous
        </button>
        <button 
          class="penda-btn penda-btn-sm penda-btn-outline-primary" 
          :disabled="paginationData.current_page === paginationData.last_page"
          @click="goToPage(paginationData.current_page + 1)"
          type="button"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Modals -->
    <PhoneContactModal 
      :show="showPhoneModal"
      :phone-number="selectedPhone"
      @close="closePhoneModal"
    />

    <EnquiryViewModal 
      :show="showViewModal"
      :enquiry="currentViewEnquiry"
      @close="closeViewModal"
      @edit="handleEditFromView"
      @open-phone-modal="openPhoneModal"
    />

    <EnquiryCreateEditModal 
      :show="showEnquiryModal"
      :is-editing="isEditingEnquiry"
      :enquiry-data="currentEnquiryData"
      @close="closeModal"
      @submit="handleEnquirySubmit"
    />

  </div>
</template>

<script>
import EnquiryCreateEditModal from './EnquiryCreateEditModal.vue';
import PhoneContactModal from './PhoneContactModal.vue';
import EnquiryViewModal from './EnquiryViewModal.vue';

export default {
  name: 'EnquiriesManagement',
  components: {
    EnquiryCreateEditModal,
    PhoneContactModal,
    EnquiryViewModal
  },
  data() {
    return {
      enquiries: [],
      loading: false,
      searchQuery: '',
      statusFilter: '',
      perPage: 10,
      sortBy: 'created_at',
      sortOrder: 'desc',
      paginationData: null,
      searchTimeout: null,
      selectedPhone: '',
      showPhoneModal: false,
      showViewModal: false,
      currentViewEnquiry: null,
      showEnquiryModal: false,
      isEditingEnquiry: false,
      currentEnquiryData: null
    };
  },
  mounted() {
    this.fetchEnquiries();
  },
  beforeUnmount() {
    // Clear any pending timeouts
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
  },
  methods: {
    async fetchEnquiries(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({
          page: page.toString(),
          per_page: this.perPage.toString(),
          sort_by: this.sortBy,
          sort_order: this.sortOrder
        });

        if (this.searchQuery) {
          params.append('search', this.searchQuery);
        }
        if (this.statusFilter) {
          params.append('status', this.statusFilter);
        }

        const response = await fetch(`/api/contacts?${params.toString()}`, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        this.enquiries = data.data || [];
        this.paginationData = {
          current_page: data.current_page,
          last_page: data.last_page,
          from: data.from,
          to: data.to,
          total: data.total
        };
      } catch (error) {
        console.error('Error fetching enquiries:', error);
        this.enquiries = [];
        this.paginationData = null;
        alert('Failed to load enquiries. Please check your connection and try again.');
      } finally {
        this.loading = false;
      }
    },
    debounceSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      this.searchTimeout = setTimeout(() => {
        this.fetchEnquiries(1);
      }, 500);
    },
    goToPage(page) {
      if (page >= 1 && this.paginationData && page <= this.paginationData.last_page) {
        this.fetchEnquiries(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    openPhoneModal(phone) {
      this.selectedPhone = phone;
      this.showPhoneModal = true;
    },
    closePhoneModal() {
      this.showPhoneModal = false;
    },
    openViewModal(enquiry) {
      this.currentViewEnquiry = JSON.parse(JSON.stringify(enquiry));
      this.showViewModal = true;
    },
    closeViewModal() {
      this.showViewModal = false;
      setTimeout(() => {
        this.currentViewEnquiry = null;
      }, 300);
    },
    handleEditFromView(enquiry) {
      this.closeViewModal();
      setTimeout(() => {
        this.openEditModal(enquiry);
      }, 300);
    },
    openAddModal() {
      this.isEditingEnquiry = false;
      this.currentEnquiryData = null;
      this.showEnquiryModal = true;
    },
    openEditModal(enquiry) {
      this.isEditingEnquiry = true;
      this.currentEnquiryData = JSON.parse(JSON.stringify(enquiry));
      this.showEnquiryModal = true;
    },
    closeModal() {
      this.showEnquiryModal = false;
      setTimeout(() => {
        this.isEditingEnquiry = false;
        this.currentEnquiryData = null;
      }, 300);
    },
    handleEnquirySubmit(response) {
      const currentPage = this.paginationData?.current_page || 1;
      this.fetchEnquiries(currentPage);
      
      const action = this.isEditingEnquiry ? 'updated' : 'created';
      this.showSuccessMessage(`Enquiry ${action} successfully!`);
    },
    showSuccessMessage(message) {
      alert(message);
    },
    confirmDelete(id) {
      if (confirm('Are you sure you want to delete this enquiry? This action cannot be undone.')) {
        this.deleteEnquiry(id);
      }
    },
    async deleteEnquiry(id) {
      try {
        const response = await fetch(`/api/contacts/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content || '' 
          }
        });
        
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Failed to delete enquiry');
        }
        
        const currentPage = this.paginationData.current_page;
        const itemsOnPage = this.enquiries.length;
        
        if (itemsOnPage === 1 && currentPage > 1) {
          this.fetchEnquiries(currentPage - 1);
        } else {
          this.fetchEnquiries(currentPage);
        }
        
        this.showSuccessMessage('Enquiry deleted successfully!');
      } catch (error) {
        console.error('Error deleting enquiry:', error);
        alert(error.message || 'Failed to delete enquiry. Please try again.');
      }
    },
    formatDate(date) {
      if (!date) return 'N/A';
      try {
        const dateObj = new Date(date);
        const day = dateObj.getDate();
        const month = dateObj.toLocaleDateString('en-ZA', { month: 'short' });
        const year = dateObj.getFullYear().toString().slice(-2);
        const time = dateObj.toLocaleTimeString('en-ZA', { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: false 
        });
        return `${day} ${month} ${year} ${time}`;
      } catch (error) {
        console.error('Error formatting date:', error);
        return 'Invalid date';
      }
    },
    formatStatus(status) {
      if (!status) return 'New';
      return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    },
    formatService(service) {
      if (!service) return 'N/A';
      return service
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },
    statusBadgeClass(status) {
      const classes = {
        'new': 'bg-primary',
        'in_progress': 'bg-warning text-dark',
        'waiting_for_response': 'bg-info text-dark',
        'resolved': 'bg-success',
        'spam': 'bg-danger',
        'closed': 'bg-secondary'
      };
      return classes[status] || 'bg-primary';
    }
  }
};
</script>

<style scoped>
.btn-link {
  color: #0d6efd;
  cursor: pointer;
  font-size: inherit;
}
.btn-link:hover {
  color: #0a58ca;
  text-decoration: underline !important;
}

/* Make table cells truncate text */
.text-truncate {
  max-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Sticky actions column */
.actions-column {
  position: sticky;
  right: 0;
  background-color: white;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05);
  min-width: 130px;
  text-align: right;
  z-index: 1;
}

.table-light .actions-column {
  background-color: #f8f9fa;
}

.table-hover tbody tr:hover .actions-column {
  background-color: #f5f5f5;
}

/* Ensure responsive table allows horizontal scroll */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
}
</style>