<template>
  <div class="enq-root">
    <main class="enq-main">
      <section class="enq-section">
        <!-- Top bar -->
        <div class="enq-topbar">
          <div>
            <h1 class="enq-title">Enquiries</h1>
            <p class="enq-subtitle">{{ paginationData ? paginationData.total : 0 }} enquiries total</p>
          </div>
          <button class="btn-create" @click="openAddModal">
            <font-awesome-icon :icon="['fas', 'plus']" /> New Enquiry
          </button>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
          <div class="search-wrap">
            <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
            <input
              v-model="searchQuery"
              class="filter-search"
              placeholder="Search by name, email, or phone…"
              @input="debounceSearch"
            />
          </div>

          <select v-model="statusFilter" class="filter-select" @change="fetchEnquiries()">
            <option value="">All statuses</option>
            <option value="new">New</option>
            <option value="in_progress">In Progress</option>
            <option value="waiting_for_response">Awaiting Response</option>
            <option value="resolved">Resolved</option>
            <option value="spam">Spam</option>
            <option value="closed">Closed</option>
          </select>

          <select v-model.number="perPage" class="filter-select" @change="fetchEnquiries()">
            <option :value="10">10 per page</option>
            <option :value="15">15 per page</option>
            <option :value="25">25 per page</option>
            <option :value="50">50 per page</option>
          </select>
        </div>

        <!-- Table -->
        <div class="enq-table-wrap">
          <table class="enq-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th @click="sort('service')" class="sortable">
                  Service
                  <span class="sort-caret" v-if="sortBy === 'service'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                </th>
                <th>Status</th>
                <th @click="sort('created_at')" class="sortable">
                  Date
                  <span class="sort-caret" v-if="sortBy === 'created_at'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="7" class="table-state">Loading…</td></tr>
              <tr v-else-if="!enquiries.length">
                <td colspan="7" class="table-state">
                  No enquiries found
                  <div class="table-state-sub" v-if="searchQuery || statusFilter">Try adjusting your search or filters</div>
                </td>
              </tr>
              <tr
                v-else
                v-for="enquiry in enquiries"
                :key="enquiry.id"
                class="enq-row"
                @click="openViewModal(enquiry)"
              >
                <td class="enq-name" :title="enquiry.name">{{ enquiry.name }}</td>
                <td @click.stop>
                  <button class="phone-link" @click="openPhoneModal(enquiry.phone)" type="button">
                    {{ enquiry.phone }}
                  </button>
                </td>
                <td class="ellipsis" :title="enquiry.email || 'N/A'">{{ enquiry.email || 'N/A' }}</td>
                <td class="ellipsis" :title="formatService(enquiry.service)">{{ formatService(enquiry.service) }}</td>
                <td>
                  <span class="status-badge" :class="statusBadgeClass(enquiry.status)">
                    {{ formatStatus(enquiry.status) }}
                  </span>
                </td>
                <td class="date-cell">{{ formatDate(enquiry.created_at) }}</td>
                <td class="row-actions" @click.stop>
                  <button class="icon-btn" title="View" @click="openViewModal(enquiry)">
                    <font-awesome-icon :icon="['fas', 'eye']" />
                  </button>
                  <button class="icon-btn" title="Edit" @click="openEditModal(enquiry)">
                    <font-awesome-icon :icon="['fas', 'pencil']" />
                  </button>
                  <button class="icon-btn icon-btn-danger" title="Delete" @click="confirmDelete(enquiry.id)">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="paginationData && enquiries.length > 0">
          <span class="pag-info">
            {{ paginationData.from || 0 }}–{{ paginationData.to || 0 }} of {{ paginationData.total || 0 }}
          </span>
          <div class="pag-controls">
            <button
              class="pag-btn"
              :disabled="paginationData.current_page === 1"
              @click="goToPage(paginationData.current_page - 1)"
              type="button"
            >‹</button>
            <span class="pag-page">{{ paginationData.current_page }} / {{ paginationData.last_page }}</span>
            <button
              class="pag-btn"
              :disabled="paginationData.current_page === paginationData.last_page"
              @click="goToPage(paginationData.current_page + 1)"
              type="button"
            >›</button>
          </div>
        </div>
      </section>
    </main>

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
    sort(field) {
      if (this.sortBy === field) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortBy = field;
        this.sortOrder = 'asc';
      }
      this.fetchEnquiries();
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
        'new': 'status-new',
        'in_progress': 'status-in_progress',
        'waiting_for_response': 'status-waiting_for_response',
        'resolved': 'status-resolved',
        'spam': 'status-spam',
        'closed': 'status-closed'
      };
      return classes[status] || 'status-new';
    }
  }
};
</script>

<style scoped>
/* ── Root layout ─────────────────────────────────────────────────────────── */
.enq-root {
  display: block;
  min-height: 100vh;
  background: #f8f7f4;
}

.enq-main { flex: 1; overflow: auto; }
.enq-section { padding: 36px 40px; max-width: 1280px; }

/* ── Top bar ─────────────────────────────────────────────────────────────── */
.enq-topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 28px;
}
.enq-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  letter-spacing: -0.5px;
  margin: 0 0 4px;
}
.enq-subtitle { font-size: 13px; color: #888; margin: 0; }

.btn-create {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #1a1a1a;
  color: #f0efe7;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-create:hover { background: #333; }
.btn-create svg { width: 16px; height: 16px; }

/* ── Filter bar ──────────────────────────────────────────────────────────── */
.filter-bar {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.search-wrap { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #aaa; }
.filter-search {
  width: 100%;
  padding: 8px 12px 8px 32px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  color: #1a1a1a;
  outline: none;
  box-sizing: border-box;
}
.filter-search:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
.filter-select {
  padding: 8px 10px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  background: white;
  font-size: 13px;
  color: #333;
  outline: none;
  cursor: pointer;
}
.filter-select:focus { border-color: #d4a853; }

/* ── Table ───────────────────────────────────────────────────────────────── */
.enq-table-wrap {
  background: white;
  border-radius: 12px;
  border: 1px solid #e8e8e0;
  overflow-x: auto;
  margin-bottom: 16px;
}
.enq-table { width: 100%; border-collapse: collapse; min-width: 720px; }
.enq-table thead th {
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #f0f0e8;
  background: #fafaf7;
  text-align: left;
  white-space: nowrap;
}
.enq-table thead th.sortable { cursor: pointer; user-select: none; }
.enq-table thead th.sortable:hover { color: #444; }
.sort-caret { font-size: 9px; margin-left: 4px; color: #d4a853; }
.enq-table tbody td {
  padding: 13px 16px;
  font-size: 14px;
  color: #333;
  border-bottom: 1px solid #f4f4ee;
  vertical-align: middle;
  max-width: 220px;
}
.enq-row { cursor: pointer; transition: background 0.1s; }
.enq-row:hover { background: #fafaf7; }
.enq-row:last-child td { border-bottom: none; }
.enq-name { font-weight: 600; color: #1a1a1a; }
.ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.date-cell { white-space: nowrap; font-variant-numeric: tabular-nums; color: #555; }
.table-state { text-align: center; padding: 48px; color: #aaa; font-style: italic; }
.table-state-sub { font-style: normal; font-size: 12px; margin-top: 6px; color: #bbb; }

/* ── Phone link ──────────────────────────────────────────────────────────── */
.phone-link {
  background: none;
  border: none;
  padding: 0;
  color: #a9770c;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
}
.phone-link:hover { text-decoration: underline; }

/* ── Status badge ────────────────────────────────────────────────────────── */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  white-space: nowrap;
}
.status-new { background: #eaf1fb; color: #3a6ea5; }
.status-in_progress { background: #fdf3d9; color: #a9770c; }
.status-waiting_for_response { background: #f3e9fb; color: #7c4fa0; }
.status-resolved { background: #e8f5ec; color: #2f7d4f; }
.status-spam { background: #fbe9e7; color: #c0392b; }
.status-closed { background: #eeeeee; color: #666666; }

/* ── Row actions ─────────────────────────────────────────────────────────── */
.row-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px;
  border: none; background: none; border-radius: 6px;
  color: #aaa; cursor: pointer; transition: all 0.15s;
}
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn-danger:hover { background: #fbe9e7; color: #c0392b; }
.icon-btn svg { width: 15px; height: 15px; }

/* ── Pagination ──────────────────────────────────────────────────────────── */
.pagination { display: flex; justify-content: space-between; align-items: center; }
.pag-info { font-size: 13px; color: #888; }
.pag-controls { display: flex; align-items: center; gap: 8px; }
.pag-btn {
  width: 30px; height: 30px;
  border: 1px solid #e0e0d8; border-radius: 6px;
  background: white; color: #555; cursor: pointer; font-size: 16px;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
}
.pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pag-btn:hover:not(:disabled) { border-color: #d4a853; color: #d4a853; }
.pag-page { font-size: 13px; color: #555; }

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 900px) {
  .enq-section { padding: 20px; }
  .enq-topbar { flex-direction: column; align-items: stretch; gap: 14px; }
  .btn-create { justify-content: center; }
}
</style>