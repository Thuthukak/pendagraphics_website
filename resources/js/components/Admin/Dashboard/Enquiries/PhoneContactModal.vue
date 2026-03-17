<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-white">
                        <div class="modal-header">
                            <h5 class="modal-title">Contact Options</h5>
                            <button type="button" class="btn-close" @click="$emit('close')"></button>
                        </div>
                        <div class="modal-body text-center">
                            <h4 class="mb-4">{{ phoneNumber }}</h4>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-lg" @click="openWhatsApp" type="button">
                                    <i class="bi bi-whatsapp me-2"></i> Open in WhatsApp
                                </button>
                                <button class="btn btn-outline-primary btn-lg" @click="copyPhone" type="button">
                                    <i class="bi bi-clipboard me-2"></i> Copy Number
                                </button>
                            </div>
                            <div v-if="copied" class="alert alert-success mt-3 mb-0">
                                <i class="bi bi-check-circle me-2"></i> Phone number copied!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
export default {
    name: 'PhoneContactModal',
    props: {
        show: {
            type: Boolean,
            required: true
        },
        phoneNumber: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            copied: false
        };
    },
    watch: {
        show(newVal) {
            if (newVal) {
                this.copied = false;
            }
        }
    },
    methods: {
        openWhatsApp() {
            const cleanPhone = this.phoneNumber.replace(/\D/g, '');
            window.open(`https://wa.me/${cleanPhone}`, '_blank');
            this.$emit('close');
        },
        async copyPhone() {
            try {
                await navigator.clipboard.writeText(this.phoneNumber);
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 3000);
            } catch (error) {
                console.error('Failed to copy:', error);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = this.phoneNumber;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.copied = true;
                    setTimeout(() => {
                        this.copied = false;
                    }, 3000);
                } catch (err) {
                    alert('Failed to copy phone number. Please copy manually: ' + this.phoneNumber);
                }
                document.body.removeChild(textArea);
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
    z-index: 2050;
}

.modal-container {
    width: auto;
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