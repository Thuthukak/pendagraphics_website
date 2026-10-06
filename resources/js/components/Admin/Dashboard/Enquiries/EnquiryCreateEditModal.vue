<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="handleBackdropClick">
            <div class="modal-container" @click.stop>
                <div class="modal-card">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Enquiry' : 'Add New Enquiry' }}</h5>
                        <button type="button" class="close-btn" @click="handleClose">
                            <font-awesome-icon :icon="['fas', 'xmark']" />
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="errorMessages.length" class="form-alert">
                            <ul class="form-alert-list">
                                <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
                            </ul>
                        </div>
                        <form @submit.prevent="submitForm">
                            <div class="form-grid">
                                <div class="field">
                                    <label for="enquiryName" class="field-label">Your Name *</label>
                                    <input
                                        class="field-input"
                                        id="enquiryName"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Enter your full name"
                                        required
                                    >
                                </div>
                                <div class="field">
                                    <label for="enquiryCompany" class="field-label">Company Name</label>
                                    <input
                                        class="field-input"
                                        id="enquiryCompany"
                                        v-model="form.company"
                                        type="text"
                                        placeholder="Enter your company name"
                                    >
                                </div>
                                <div class="field">
                                    <label for="enquiryPhone" class="field-label">Phone Number *</label>
                                    <input
                                        class="field-input"
                                        id="enquiryPhone"
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="Your contact number"
                                        required
                                    >
                                </div>
                                <div class="field">
                                    <label for="enquiryEmail" class="field-label">Email Address</label>
                                    <input
                                        class="field-input"
                                        id="enquiryEmail"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="your.email@example.com"
                                    >
                                </div>
                                <div class="field">
                                    <label for="enquiryService" class="field-label">Service Interested In *</label>
                                    <select
                                        class="field-input"
                                        id="enquiryService"
                                        v-model="form.service"
                                        required
                                        :disabled="servicesLoading"
                                    >
                                        <option value="">{{ servicesLoading ? 'Loading services…' : 'Select a service' }}</option>
                                        <option v-for="opt in serviceOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                        <option value="enquiry">General Enquiry</option>
                                    </select>
                                    <p v-if="servicesError" class="field-hint field-hint-error">
                                        Couldn't load services.
                                        <button type="button" class="field-hint-retry" @click="fetchServices">Retry</button>
                                    </p>
                                </div>
                                <div class="field" v-if="isEditing">
                                    <label for="enquiryStatus" class="field-label">Status *</label>
                                    <select
                                        class="field-input"
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
                                <div class="field field-full">
                                    <label for="enquiryMessage" class="field-label">Your Message *</label>
                                    <textarea
                                        class="field-input"
                                        id="enquiryMessage"
                                        v-model="form.message"
                                        placeholder="Tell us about your business needs…"
                                        rows="5"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-secondary" @click="handleClose">Cancel</button>
                                <button type="submit" class="btn-primary" :disabled="isSubmitting">
                                    <span v-if="isSubmitting" class="spinner"></span>
                                    {{ isEditing ? 'Update' : 'Add' }} Enquiry
                                </button>
                            </div>
                        </form>
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
            errorMessages: [],
            services: [],
            servicesLoading: false,
            servicesError: false
        };
    },
    computed: {
        // Fetched services for the dropdown. If the enquiry being edited has a
        // service value that no longer matches an active service (e.g. it was
        // renamed or deactivated), keep it selectable so we don't silently
        // wipe out existing data.
        serviceOptions() {
            const opts = this.services.map(s => ({ value: s.name, label: s.name }));
            if (
                this.form.service &&
                this.form.service !== 'enquiry' &&
                !opts.some(o => o.value === this.form.service)
            ) {
                opts.unshift({ value: this.form.service, label: this.form.service });
            }
            return opts;
        }
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
    mounted() {
        this.fetchServices();
    },
    methods: {
        async fetchServices() {
            this.servicesLoading = true;
            this.servicesError = false;
            try {
                const response = await fetch('/api/services?status=active', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                // Handles both a plain array and a paginated { data: [...] } response
                this.services = Array.isArray(data) ? data : (data.data || []);
            } catch (error) {
                console.error('Error fetching services:', error);
                this.services = [];
                this.servicesError = true;
            } finally {
                this.servicesLoading = false;
            }
        },
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
                    if (data.errors) {
                        this.errorMessages = Object.values(data.errors).flat();
                    } else {
                        this.errorMessages = [data.message || 'An error occurred'];
                    }
                    return;
                }

                this.$emit('submit', data);
                this.$emit('close');
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
    background-color: rgba(26, 26, 26, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 20px;
}

.modal-container {
    width: 100%;
    max-width: 620px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 22px 28px;
    border-bottom: 1px solid #f0f0e8;
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -0.3px;
    margin: 0;
}

.close-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: none;
    background: none;
    border-radius: 6px;
    color: #aaa;
    cursor: pointer;
    transition: all 0.15s;
}
.close-btn:hover { background: #f0f0e8; color: #333; }
.close-btn svg { width: 14px; height: 14px; }

.modal-body { padding: 24px 28px 28px; }

.form-alert {
    background: #fbe9e7;
    border: 1px solid #f3cdc8;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 18px;
}
.form-alert-list {
    margin: 0;
    padding-left: 18px;
    color: #c0392b;
    font-size: 13px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 4px;
}
.field { display: flex; flex-direction: column; gap: 6px; }
.field-full { grid-column: 1 / -1; }

.field-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
}

.field-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e0e0d8;
    border-radius: 8px;
    background: #fafaf7;
    font-size: 14px;
    color: #1a1a1a;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.field-input:focus {
    border-color: #d4a853;
    box-shadow: 0 0 0 3px rgba(212, 168, 83, 0.12);
    background: #ffffff;
}
textarea.field-input { resize: vertical; min-height: 100px; }
.field-input:disabled { opacity: 0.65; cursor: not-allowed; }

.field-hint {
    font-size: 12px;
    color: #888;
    margin: 2px 0 0;
}
.field-hint-error { color: #c0392b; }
.field-hint-retry {
    background: none;
    border: none;
    padding: 0;
    margin-left: 4px;
    color: #a9770c;
    font-size: 12px;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #f0f0e8;
}

.btn-secondary {
    padding: 10px 18px;
    background: #ffffff;
    color: #555;
    border: 1px solid #e0e0d8;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-secondary:hover { border-color: #ccc; background: #fafaf7; }

.btn-primary {
    display: inline-flex;
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
.btn-primary:hover:not(:disabled) { background: #333; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(240, 239, 231, 0.4);
    border-top-color: #f0efe7;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
    .modal-header, .modal-body { padding-left: 20px; padding-right: 20px; }
}
</style>