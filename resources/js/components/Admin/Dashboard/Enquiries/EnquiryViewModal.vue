<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <div class="modal-card">
                    <div class="modal-header">
                        <h5 class="modal-title">Enquiry Details</h5>
                        <button type="button" class="close-btn" @click="$emit('close')">
                            <font-awesome-icon :icon="['fas', 'xmark']" />
                        </button>
                    </div>
                    <div class="modal-body" v-if="enquiry">
                        <div class="detail-grid">
                            <div class="detail">
                                <span class="detail-label">Name</span>
                                <span class="detail-value">{{ enquiry.name }}</span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Company</span>
                                <span class="detail-value">{{ enquiry.company || 'N/A' }}</span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    <span class="status-badge" :class="statusBadgeClass(enquiry.status)">
                                        {{ formatStatus(enquiry.status) }}
                                    </span>
                                </span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Phone Number</span>
                                <span class="detail-value">
                                    <button class="phone-link" @click="$emit('open-phone-modal', enquiry.phone)" type="button">
                                        {{ enquiry.phone }}
                                    </button>
                                </span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Email Address</span>
                                <span class="detail-value">{{ enquiry.email || 'N/A' }}</span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Service</span>
                                <span class="detail-value">{{ formatService(enquiry.service) }}</span>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Date Created</span>
                                <span class="detail-value">{{ formatDate(enquiry.created_at) }}</span>
                            </div>
                            <div class="detail" v-if="enquiry.updated_at && enquiry.updated_at !== enquiry.created_at">
                                <span class="detail-label">Last Updated</span>
                                <span class="detail-value">{{ formatDate(enquiry.updated_at) }}</span>
                            </div>
                        </div>

                        <div class="detail detail-full">
                            <span class="detail-label">Message</span>
                            <div class="message-box">{{ enquiry.message }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" @click="$emit('close')">Close</button>
                        <button type="button" class="btn-primary" @click="$emit('edit', enquiry)">
                            <font-awesome-icon :icon="['fas', 'pencil']" /> Edit
                        </button>
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
    max-width: 640px;
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

.modal-body { padding: 24px 28px 8px; }

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 20px;
    margin-bottom: 20px;
}

.detail { display: flex; flex-direction: column; gap: 5px; }
.detail-full { padding: 0 28px 24px; }

.detail-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
}
.detail-value {
    font-size: 14px;
    color: #1a1a1a;
}

.message-box {
    white-space: pre-wrap;
    word-wrap: break-word;
    min-height: 90px;
    padding: 14px 16px;
    background: #fafaf7;
    border: 1px solid #f0f0e8;
    border-radius: 8px;
    font-size: 14px;
    color: #333;
    line-height: 1.5;
}

.phone-link {
    background: none;
    border: none;
    padding: 0;
    color: #a9770c;
    font-size: 14px;
    cursor: pointer;
}
.phone-link:hover { text-decoration: underline; }

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

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px 28px;
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
.btn-primary:hover { background: #333; }
.btn-primary svg { width: 12px; height: 12px; }

@media (max-width: 640px) {
    .detail-grid { grid-template-columns: 1fr; padding: 0; }
    .modal-header, .modal-body, .detail-full, .modal-footer { padding-left: 20px; padding-right: 20px; }
}
</style>