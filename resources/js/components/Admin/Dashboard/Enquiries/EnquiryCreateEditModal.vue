<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="handleBackdropClick">
            <div class="modal-container" @click.stop>
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-white">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ isEditing ? 'Edit Enquiry' : 'Add New Enquiry' }}</h5>
                            <button type="button" class="btn-close" @click="handleClose"></button>
                        </div>
                        <div class="modal-body">
                            <div v-if="errorMessages.length" class="alert alert-danger">
                                <ul class="mb-0">
                                    <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                            <form @submit.prevent="submitForm">   
                                <div class="mb-3">
                                    <label for="enquiryName" class="form-label">Your Name *</label>
                                    <input 
                                        class="form-control" 
                                        id="enquiryName"
                                        v-model="form.name" 
                                        type="text" 
                                        placeholder="Enter your full name" 
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label for="enquiryCompany" class="form-label">Company Name </label>
                                    <input 
                                        class="form-control" 
                                        id="enquiryCompany"
                                        v-model="form.company" 
                                        type="text" 
                                        placeholder="Enter your company name" 
                                    >
                                </div>
                                <div class="mb-3">
                                    <label for="enquiryPhone" class="form-label">Phone Number *</label>
                                    <input 
                                        class="form-control" 
                                        id="enquiryPhone"
                                        v-model="form.phone" 
                                        type="tel" 
                                        placeholder="Your contact number" 
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label for="enquiryEmail" class="form-label">Email Address</label>
                                    <input 
                                        class="form-control" 
                                        id="enquiryEmail"
                                        v-model="form.email" 
                                        type="email" 
                                        placeholder="your.email@example.com"
                                    >
                                </div>
                                <div class="mb-3">
                                    <label for="enquiryService" class="form-label">Service Interested In *</label>
                                    <select 
                                        class="form-select" 
                                        id="enquiryService"
                                        v-model="form.service" 
                                        required
                                    >
                                        <option value="">Select a service</option>
                                        <option value="website-design">Website Design</option>
                                        <option value="Logo-Design">Logo Design</option>
                                        <option value="Graphic Design">Graphic Design</option>
                                        <option value="Business Card Design">Business Card Design</option>
                                        <option value="banner-billboard-design">Banner/Billboard Design</option>
                                        <option value="enquiry">General Enquiry</option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="isEditing">
                                    <label for="enquiryStatus" class="form-label">Status *</label>
                                    <select 
                                        class="form-select" 
                                        id="enquiryStatus"
                                        v-model="form.status" 
                                        required
                                    >
                                        <option value="new">New</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="waiting_for_response">Awaiting Response</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="spam">Spam</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="enquiryMessage" class="form-label">Your Message *</label>
                                    <textarea 
                                        class="form-control" 
                                        id="enquiryMessage"
                                        v-model="form.message" 
                                        placeholder="Tell us about your business needs..." 
                                        rows="5" 
                                        required
                                    ></textarea>
                                </div>
                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary" @click="handleClose">Cancel</button>
                                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                                        {{ isEditing ? 'Update' : 'Add' }} Enquiry
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
export default {
    name: 'EnquiryCreateEditModal',
    props: {
        show: {
            type: Boolean,
            required: true
        },
        isEditing: {
            type: Boolean,
            default: false
        },
        enquiryData: {
            type: Object,
            default: null
        }
    },
    emits: ['close', 'submit'],
    data() {
        return {
            form: {
                name: '',
                company: '',
                phone: '',
                email: '',
                service: '',
                message: '',
                status: 'new'
            },
            isSubmitting: false,
            errorMessages: []
        };
    },
    watch: {
        show(newVal) {
            if (newVal) {
                this.resetForm();
                if (this.isEditing && this.enquiryData) {
                    this.populateForm();
                }
            }
        },
        enquiryData: {
            handler(newVal) {
                if (newVal && this.isEditing) {
                    this.populateForm();
                }
            },
            deep: true
        }
    },
    methods: {
        handleClose() {
            this.$emit('close');
        },
        handleBackdropClick() {
            this.$emit('close');
        },
        resetForm() {
            this.form = {
                name: '',
                company: '',
                phone: '',
                email: '',
                service: '',
                message: '',
                status: 'new'
            };
            this.errorMessages = [];
            this.isSubmitting = false;
        },
        populateForm() {
            if (this.enquiryData) {
                this.form = {
                    name: this.enquiryData.name || '',
                    company: this.enquiryData.company || '',
                    phone: this.enquiryData.phone || '',
                    email: this.enquiryData.email || '',
                    service: this.enquiryData.service || '',
                    message: this.enquiryData.message || '',
                    status: this.enquiryData.status || 'new'
                };
            }
        },
        async submitForm() {
            this.isSubmitting = true;
            this.errorMessages = [];

            try {
                const url = this.isEditing 
                    ? `/api/contacts/${this.enquiryData.id}` 
                    : '/api/contacts';
                
                const method = this.isEditing ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content || ''
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    // Handle validation errors
                    if (data.errors) {
                        this.errorMessages = Object.values(data.errors).flat();
                    } else {
                        this.errorMessages = [data.message || 'An error occurred'];
                    }
                    return;
                }

                // Emit success event with data
                this.$emit('submit', data);
                // Close modal after successful submission
                this.$emit('close');
                // Reset form
                this.resetForm();
            } catch (error) {
                console.error('Error submitting form:', error);
                this.errorMessages = ['Failed to submit form. Please try again.'];
            } finally {
                this.isSubmitting = false;
            }
        }
    }
};
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.modal-container {
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    padding: 1rem;
}

.modal-dialog {
    margin: 0 auto;
}

.modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>