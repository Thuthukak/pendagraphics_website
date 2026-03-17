<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-white">
                        <div class="modal-header">
                            <h5 class="modal-title">Enquiry Details</h5>
                            <button type="button" class="btn-close" @click="$emit('close')"></button>
                        </div>
                        <div class="modal-body">
                            <div v-if="enquiry" class="enquiry-details">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Name</label>
                                        <p class="mb-0">{{ enquiry.name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Company</label>
                                        <p class="mb-0">{{ enquiry.company || 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Status</label>
                                        <p class="mb-0">
                                            <span 
                                                class="badge" 
                                                :class="statusBadgeClass(enquiry.status)"
                                            >
                                                {{ formatStatus(enquiry.status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Phone Number</label>
                                        <p class="mb-0">
                                            <button 
                                                class="btn btn-link text-decoration-none p-0" 
                                                @click="$emit('open-phone-modal', enquiry.phone)"
                                                type="button"
                                            >
                                                {{ enquiry.phone }}
                                            </button>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Email Address</label>
                                        <p class="mb-0">{{ enquiry.email || 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Service</label>
                                        <p class="mb-0">{{ formatService(enquiry.service) }}</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small">Date Created</label>
                                        <p class="mb-0">{{ formatDate(enquiry.created_at) }}</p>
                                    </div>
                                    <div class="col-md-6" v-if="enquiry.updated_at && enquiry.updated_at !== enquiry.created_at">
                                        <label class="form-label fw-bold text-muted small">Last Updated</label>
                                        <p class="mb-0">{{ formatDate(enquiry.updated_at) }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-muted small">Message</label>
                                        <div class="message-box p-3 bg-light rounded">
                                            {{ enquiry.message }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
                            <button type="button" class="btn btn-primary" @click="$emit('edit', enquiry)">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
export default {
    name: 'EnquiryViewModal',
    props: {
        show: {
            type: Boolean,
            required: true
        },
        enquiry: {
            type: Object,
            default: null
        }
    },
    methods: {
        formatDate(date) {
            if (!date) return 'N/A';
            try {
                return new Date(date).toLocaleDateString('en-ZA', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
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

.message-box {
    white-space: pre-wrap;
    word-wrap: break-word;
    min-height: 100px;
}

.btn-link {
    color: #0d6efd;
    cursor: pointer;
    font-size: inherit;
}

.btn-link:hover {
    color: #0a58ca;
    text-decoration: underline !important;
}
</style>