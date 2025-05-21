<template>
  <div>
    <!-- Teleport Modal to body -->
    <Teleport to="body">
      <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <!-- Modal Content -->
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold">Quick Project Estimate</h3>
            <button @click="closeModal" class="text-white hover:text-gray-200">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-6">
            <div v-if="!submitted">
              <!-- Loading state -->
              <div v-if="loading" class="flex justify-center items-center py-12">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>

              <div v-else>
                <!-- Services Selection -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold mb-3 text-gray-800">Select Services</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div 
                      v-for="service in services" 
                      :key="service.id"
                      @click="toggleService(service.id)"
                      :class="[
                        'border rounded-lg p-3 cursor-pointer transition-all',
                        service.selected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'
                      ]"
                    >
                      <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-800">{{ service.name }}</span>
                        <span class="text-indigo-600 font-bold">R{{ service.price.toFixed(2) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Total Estimate -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Estimated Total:</span>
                    <span class="text-2xl font-bold text-indigo-600">
                      R{{ totalEstimate.toFixed(2) }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-500 mt-2">
                    This is a ballpark estimate. Final pricing may vary based on project specifics.
                  </p>
                </div>

                <!-- Contact Info -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold mb-3 text-gray-800">Your Details</h4>
                  <div class="space-y-4">
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Name</label>
                      <input 
                        type="text" 
                        v-model="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                      <input 
                        type="email" 
                        v-model="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required
                      />
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <button 
                  @click="handleSubmit"
                  :disabled="!hasSelectedServices || !name || !email"
                  :class="[
                    'w-full py-3 px-6 rounded-lg font-bold text-white',
                    hasSelectedServices && name && email ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'
                  ]"
                >
                  Request Detailed Quote
                </button>
              </div>
            </div>
            <div v-else class="text-center py-6">
              <div class="flex justify-center mb-4">
                <div class="bg-green-100 p-3 rounded-full inline-flex">
                  <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                    <polyline points="14 2 14 8 20 8" />
                    <path d="m9 15 2 2 4-4" />
                  </svg>
                </div>
              </div>
              <h4 class="text-xl font-bold text-gray-800 mb-2">Estimate Sent!</h4>
              <p class="text-gray-600 mb-6">
                Thank you! We've received your estimate request and will contact you at {{ email }} with a detailed quote shortly.
              </p>
              <button 
                @click="closeModal"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'EstimateModal',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close'],
  data() {
    return {
      services: [],
      name: '',
      email: '',
      submitted: false,
      loading: true,
      error: null
    };
  },
  computed: {
    totalEstimate() {
      return this.services.reduce((sum, service) => {
        return sum + (service.selected ? service.price : 0);
      }, 0);
    },
    hasSelectedServices() {
      return this.services.some(service => service.selected);
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        this.fetchServices();
      }
    }
  },
  methods: {
    fetchServices() {
      this.loading = true;
      axios.get('/api/services')
        .then(response => {
          this.services = response.data.map(service => ({
            ...service,
            price: parseFloat(service.base_price), // Convert base_price to a number
            selected: false
          }));
        })
        .catch(error => {
          console.error('Error fetching services:', error);
          this.error = 'Failed to load services. Please try again later.';
        })
        .finally(() => {
          this.loading = false;
        });
    },
    toggleService(id) {
      const serviceIndex = this.services.findIndex(service => service.id === id);
      if (serviceIndex !== -1) {
        this.services[serviceIndex].selected = !this.services[serviceIndex].selected;
      }
    },
    handleSubmit() {
      if (!this.hasSelectedServices || !this.name || !this.email) return;
      
      // Get selected services with their full details including ID and price
      const selectedServices = this.services.filter(s => s.selected).map(service => ({
        id: service.id,
        price: service.price
      }));
      
      const requestData = {
        name: this.name,
        email: this.email,
        selectedServices: selectedServices,
        totalEstimate: this.totalEstimate
      };
      
      axios.post('/api/estimates', requestData)
        .then(response => {
          this.submitted = true;
        })
        .catch(error => {
          console.error('Error submitting estimate request:', error);
          
          // Show more detailed error message if available
          if (error.response && error.response.data && error.response.data.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
            alert(`Validation errors:\n${errorMessages}`);
          } else {
            alert('There was a problem submitting your request. Please try again.');
          }
        });
    },
    resetForm() {
      this.submitted = false;
      this.name = '';
      this.email = '';
      this.services.forEach(service => {
        service.selected = false;
      });
    },
    closeModal() {
      this.resetForm();
      this.$emit('close');
    }
  }
};
</script>