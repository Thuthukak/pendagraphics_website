<template>
  <div class="container py-4">
    <!-- Header with add button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage Services</h2>
      <button class="btn btn-primary" @click="openAddModal">
        <i class="bi bi-plus-circle me-1"></i> Add Service
      </button>
    </div>

    <!-- Search bar -->
    <div class="mb-4">
      <div class="input-group">
        <span class="input-group-text bg-light">
          <i class="bi bi-search"></i>
        </span>
        <input 
          type="text" 
          class="form-control" 
          placeholder="Search services..." 
          v-model="searchQuery"
        >
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading services...</p>
    </div>

    <!-- No services message -->
    <div v-else-if="filteredServices.length === 0" class="text-center my-5">
      <i class="bi bi-inbox display-4 text-muted"></i>
      <p class="lead mt-3">No services found</p>
      <p class="text-muted" v-if="searchQuery">Try changing your search query</p>
      <button v-else class="btn btn-outline-primary mt-2" @click="openAddModal">
        Add your first service
      </button>
    </div>

    <!-- Services table for desktop -->
    <div v-else class="d-none d-md-block">
      <div class="table-responsive rounded">
        <table class="table table-hover border">
          <thead class="table-light">
            <tr>
              <th>Service</th>
              <th>Description</th>
              <th>Price</th>
              <th>Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in filteredServices" :key="service.id" class="align-middle">
              <td class="fw-medium">{{ service.name }}</td>
              <td>
                <span :title="service.description">
                  {{ truncateText(service.description, 100) }}
                </span>
              </td>
              <td>R{{ service.base_price }}</td>
              <td>{{ service.is_active }}</td>
              <td class="text-center">
                <div class="btn-group">
                  <button 
                    class="btn btn-outline-primary btn-sm" 
                    @click="openEditModal(service)"
                    title="Edit service"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button 
                    class="btn btn-outline-danger btn-sm" 
                    @click="openDeleteModal(service)"
                    title="Delete service"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile card layout -->
    <div class="d-block d-md-none">
      <div class="row g-3">
        <div class="col-12" v-for="service in filteredServices" :key="service.id">
          <div class="card service-card">
            <div class="card-body">
              <div class="d-flex mb-3">
                <!-- Service Info -->
                <div class="flex-grow-1 min-width-0">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0 text-truncate me-2">{{ service.name }}</h5>
                    <div class="btn-group flex-shrink-0">
                      <button 
                        class="btn btn-outline-primary btn-sm" 
                        @click="openEditModal(service)"
                        title="Edit service"
                      >
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button 
                        class="btn btn-outline-danger btn-sm" 
                        @click="openDeleteModal(service)"
                        title="Delete service"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                  
                  <p class="card-text text-muted mb-3" :title="service.description">
                    {{ truncateText(service.description, 120) }}
                  </p>
                </div>
              </div>
              
              <div class="row g-2">
                <div class="col-6">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-currency-dollar text-success me-2"></i>
                    <div>
                      <small class="text-muted d-block">Price</small>
                      <strong>R{{ service.base_price }}</strong>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-clock text-primary me-2"></i>
                    <div>
                      <small class="text-muted d-block">Status</small>
                      <strong>{{ service.is_active }}</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade " id="serviceModal" tabindex="-1" ref="serviceModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Service' : 'Add New Service' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <!-- Photo Upload Section -->
              <div class="mb-4">
                <label class="form-label">Service Photo</label>
                <div class="photo-upload-section">
                  <!-- Current Photo Preview -->
                  <div class="current-photo mb-3" v-if="photoPreview || (isEditing && formData.photo)">
                    <div class="photo-preview-container">
                      <img 
                        :src="photoPreview || formData.photo" 
                        alt="Service photo preview"
                        class="rounded"
                      >
                      <button 
                        type="button" 
                        class="btn btn-danger btn-sm remove-photo-btn"
                        @click="removePhoto"
                        title="Remove photo"
                      >
                        <i class="bi bi-x"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- File Upload -->
                  <div class="photo-upload-area" v-if="!photoPreview && !(isEditing && formData.photo)">
                    <input 
                      type="file" 
                      ref="photoInput"
                      @change="handlePhotoUpload"
                      accept="image/*"
                      class="d-none"
                    >
                    <div 
                      class="upload-placeholder rounded border-2 border-dashed p-4 text-center"
                      @click="$refs.photoInput.click()"
                      @dragover.prevent
                      @drop.prevent="handlePhotoDrop"
                    >
                      <i class="bi bi-cloud-upload display-6 text-muted mb-2"></i>
                      <p class="mb-2">Click to upload or drag and drop</p>
                      <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                    </div>
                  </div>
                  
                  <!-- Change Photo Button -->
                  <div v-if="photoPreview || (isEditing && formData.photo)" class="mt-2">
                    <button 
                      type="button" 
                      class="btn btn-outline-secondary btn-sm"
                      @click="$refs.photoInput.click()"
                    >
                      <i class="bi bi-camera me-1"></i> Change Photo
                    </button>
                    <input 
                      type="file" 
                      ref="photoInput"
                      @change="handlePhotoUpload"
                      accept="image/*"
                      class="d-none"
                    >
                  </div>
                  
                  <div v-if="errors.photo" class="text-danger mt-2">{{ errors.photo }}</div>
                </div>
              </div>

              <div class="mb-3">
                <label for="serviceName" class="form-label">Service Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="serviceName"
                  v-model="formData.name"
                  required
                >
                <div v-if="errors.name" class="text-danger mt-1">{{ errors.name }}</div>
              </div>
              
              <div class="mb-3">
                <label for="serviceDescription" class="form-label">Description</label>
                <textarea
                  class="form-control"
                  id="serviceDescription"
                  rows="3"
                  v-model="formData.description"
                  required
                ></textarea>
                <div v-if="errors.description" class="text-danger mt-1">{{ errors.description }}</div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="servicePrice" class="form-label">Price (R)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="servicePrice"
                    v-model="formData.price"
                    min="0"
                    required
                  >
                  <div v-if="errors.price" class="text-danger mt-1">{{ errors.price }}</div>
                </div>
                
                <div class="col-md-6">
                  <label for="serviceDuration" class="form-label">Duration (minutes)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="serviceDuration"
                    v-model="formData.duration"
                    min="1"
                    required
                  >
                  <div v-if="errors.duration" class="text-danger mt-1">{{ errors.duration }}</div>
                </div>
              </div>
              
              <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? 'Update' : 'Add' }} Service
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" ref="deleteModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Service</h5>
            <button type="button" class="btn-close" @click="closeDeleteConfirmModal"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the service: <strong>{{ serviceToDelete?.name }}</strong>?</p>
            <p class="text-danger"><small>This action cannot be undone.</small></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDeleteConfirmModal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="deleteService" :disabled="isDeleting">
              <span v-if="isDeleting" class="spinner-border spinner-border-sm me-1"></span>
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div 
        class="toast align-items-center text-white" 
        :class="toastClass" 
        role="alert" 
        aria-live="assertive" 
        aria-atomic="true"
        ref="toast"
      >
        <div class="d-flex">
          <div class="toast-body">
            {{ toastMessage }}
          </div>
          <button type="button" class="btn-close me-2 m-auto" @click="hideToast"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Modal, Toast } from 'bootstrap';

export default {
  data() {
    return {
      services: [],
      filteredServices: [],
      searchQuery: "",
      loading: true,
      isSubmitting: false,
      isDeleting: false,
      isEditing: false,
      serviceToDelete: null,
      serviceModal: null,
      deleteModal: null,
      toast: null,
      toastMessage: "",
      toastClass: "bg-success",
      photoPreview: null,
      photoFile: null,
      formData: {
        name: "",
        description: "",
        base_price: "",
        is_active: "",
      },
      errors: {
        name: "",
        description: "",
        base_price: "",
        is_active: "",
      }
    };
  },
  
  watch: {
    searchQuery(newQuery) {
      this.filterServices();
    }
  },

  mounted() {
    this.fetchServices();
  },

  methods: {
    async fetchServices() {
      this.loading = true;
      try {
        const response = await axios.get("/api/services");
        this.services = response.data;
        this.filterServices();
      } catch (error) {
        this.showToast("Error fetching services. Please try again.", "bg-danger");
        console.error("Error fetching services:", error);
      } finally {
        this.loading = false;
      }
    },

    filterServices() {
      if (!this.searchQuery) {
        this.filteredServices = [...this.services];
        return;
      }
      
      const query = this.searchQuery.toLowerCase();
      this.filteredServices = this.services.filter(service => 
        service.name.toLowerCase().includes(query) ||
        service.description.toLowerCase().includes(query)
      );
    },

    truncateText(text, maxLength) {
      if (!text) return '';
      return text.length > maxLength 
        ? text.substring(0, maxLength) + '...' 
        : text;
    },

    handleImageError(event) {
      // Replace broken image with placeholder
      event.target.style.display = 'none';
      event.target.nextElementSibling.style.display = 'flex';
    },

    handlePhotoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.processPhotoFile(file);
      }
    },

    handlePhotoDrop(event) {
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        this.processPhotoFile(files[0]);
      }
    },

    processPhotoFile(file) {
      // Validate file type
      if (!file.type.startsWith('image/')) {
        this.errors.photo = 'Please select a valid image file';
        return;
      }

      // Validate file size (5MB limit)
      if (file.size > 5 * 1024 * 1024) {
        this.errors.photo = 'Image size must be less than 5MB';
        return;
      }

      this.errors.photo = '';
      this.photoFile = file;

      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        this.photoPreview = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    removePhoto() {
      this.photoPreview = null;
      this.photoFile = null;
      this.formData.photo = "";
      if (this.$refs.photoInput) {
        this.$refs.photoInput.value = '';
      }
    },

    openAddModal() {
      this.isEditing = false;
      this.resetForm();
      if (!this.serviceModal) {
        this.serviceModal = new Modal(this.$refs.serviceModal);
      }
      this.serviceModal.show();
    },

    openEditModal(service) {
      this.isEditing = true;
      this.formData = { ...service };
      this.photoPreview = null;
      this.photoFile = null;
      if (!this.serviceModal) {
        this.serviceModal = new Modal(this.$refs.serviceModal);
      }
      this.serviceModal.show();
    },

    openDeleteModal(service) {
      this.serviceToDelete = service;
      if (!this.deleteModal) {
        this.deleteModal = new Modal(this.$refs.deleteModal);
      }
      this.deleteModal.show();
    },

    closeModal() {
      if (this.serviceModal) {
        this.serviceModal.hide();
      }
      this.resetForm();
    },

    closeDeleteConfirmModal() {
      if (this.deleteModal) {
        this.deleteModal.hide();
      }
      this.serviceToDelete = null;
    },

    resetForm() {
      this.formData = {
        name: "",
        description: "",
        price: "",
        duration: "",
        photo: ""
      };
      this.errors = {
        name: "",
        description: "",
        price: "",
        duration: "",
        photo: ""
      };
      this.photoPreview = null;
      this.photoFile = null;
      if (this.$refs.photoInput) {
        this.$refs.photoInput.value = '';
      }
    },

    async submitForm() {
      // Reset errors
      this.errors = {
        name: "",
        description: "",
        price: "",
        duration: "",
        photo: ""
      };

      // Basic validation
      if (!this.formData.name) {
        this.errors.name = "Service name is required";
      }
      if (!this.formData.description) {
        this.errors.description = "Description is required";
      }
      if (!this.formData.price || isNaN(this.formData.price) || this.formData.price < 0) {
        this.errors.price = "Valid price is required";
      }
      if (!this.formData.duration || isNaN(this.formData.duration) || this.formData.duration < 1) {
        this.errors.duration = "Valid duration is required";
      }

      // If there are errors, don't submit
      if (Object.values(this.errors).some(error => error !== "")) {
        return;
      }

      this.isSubmitting = true;

      try {
        if (this.isEditing) {
          // Update existing service
          await this.updateService();
        } else {
          // Create new service
          await this.createService();
        }
      } catch (error) {
        console.error("Form submission error:", error);
        
        // Handle validation errors from the server
        if (error.response && error.response.data && error.response.data.errors) {
          const serverErrors = error.response.data.errors;
          Object.keys(serverErrors).forEach(key => {
            this.errors[key] = serverErrors[key][0];
          });
        } else {
          this.showToast(
            `Error ${this.isEditing ? 'updating' : 'creating'} service. Please try again.`, 
            "bg-danger"
          );
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    async createService() {
      const formData = new FormData();
      formData.append('name', this.formData.name);
      formData.append('description', this.formData.description);
      formData.append('price', this.formData.price);
      formData.append('duration', this.formData.duration);
      
      if (this.photoFile) {
        formData.append('photo', this.photoFile);
      }

      const response = await axios.post("/api/services", formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      // Add the new service to the list
      this.services.push(response.data.service);
      this.filterServices();
      
      // Close modal and show success message
      this.closeModal();
      this.showToast("Service added successfully!", "bg-success");
    },

    async updateService() {
      const formData = new FormData();
      formData.append('name', this.formData.name);
      formData.append('description', this.formData.description);
      formData.append('price', this.formData.price);
      formData.append('duration', this.formData.duration);
      formData.append('_method', 'PUT');
      
      if (this.photoFile) {
        formData.append('photo', this.photoFile);
      }

      const response = await axios.post(`/api/services/${this.formData.id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      // Update the service in the list
      const index = this.services.findIndex(service => service.id === this.formData.id);
      if (index !== -1) {
        this.services[index] = response.data.service;
        this.filterServices();
      }
      
      // Close modal and show success message
      this.closeModal();
      this.showToast("Service updated successfully!", "bg-success");
    },

    async deleteService() {
      if (!this.serviceToDelete) return;
      
      this.isDeleting = true;
      
      try {
        await axios.delete(`/api/services/${this.serviceToDelete.id}`);
        
        // Remove the service from the list
        this.services = this.services.filter(service => service.id !== this.serviceToDelete.id);
        this.filterServices();
        
        // Close modal and show success message
        this.closeDeleteConfirmModal();
        this.showToast("Service deleted successfully!", "bg-success");
      } catch (error) {
        console.error("Error deleting service:", error);
        this.showToast("Error deleting service. Please try again.", "bg-danger");
      } finally {
        this.isDeleting = false;
      }
    },

    showToast(message, colorClass = "bg-success") {
      this.toastMessage = message;
      this.toastClass = colorClass;
      
      if (!this.toast) {
        this.toast = new Toast(this.$refs.toast);
      }
      
      this.toast.show();
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        this.hideToast();
      }, 5000);
    },
    
    hideToast() {
      if (this.toast) {
        this.toast.hide();
      }
    }
  }
};
</script>

<style scoped>
.table th {
  font-weight: 600;
}

.toast {
  min-width: 250px;
}

.service-card {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  transition: all 0.2s ease-in-out;
}

.service-card:hover {
  border-color: #0d6efd;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.service-card .card-title {
  color: #212529;
  font-size: 1.1rem;
  font-weight: 600;
}

.service-card .card-text {
  font-size: 0.9rem;
  line-height: 1.4;
}

/* Photo styling */
.service-photo-sm {
  width: 60px;
  height: 60px;
}

.service-photo-card {
  width: 80px;
  height: 80px;
}

.service-photo-sm img,
.service-photo-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-photo-placeholder {
  width: 100%;
  height: 100%;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
}

/* Photo upload styles */
.photo-upload-section {
  max-width: 400px;
}

.photo-preview-container {
  position: relative;
  display: inline-block;
  max-width: 200px;
}

.photo-preview-container img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.remove-photo-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}

.upload-placeholder {
  cursor: pointer;
  transition: all 0.2s;
  background-color: #f8f9fa;
  border-color: #dee2e6 !important;
}

.upload-placeholder:hover {
  background-color: #e9ecef;
  border-color: #0d6efd !important;
}

/* Mobile modal improvements */
@media (max-width: 576px) {
  .modal-dialog {
    margin: 1rem 0.5rem;
  }
  
  .modal-footer .btn {
    font-size: 0.875rem;
  }
  
  .btn-group .btn {
    padding: 0.25rem 0.5rem;
  }
  
  .photo-upload-section {
    max-width: 100%;
  }
}

/* Ensure buttons stack on very small screens */
@media (max-width: 575px) {
  .d-flex.flex-column.flex-sm-row .btn {
    width: 100%;
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
  }
  
  .btn-group .btn:last-child {
    margin-bottom: 0;
  }
}

.min-width-0 {
  min-width: 0;
}
</style>