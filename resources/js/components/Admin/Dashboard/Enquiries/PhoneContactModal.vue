<template>
    <teleport to="body">
        <div v-if="show" class="modal-backdrop" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <div class="modal-card">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact Options</h5>
                        <button type="button" class="close-btn" @click="$emit('close')">
                            <font-awesome-icon :icon="['fas', 'xmark']" />
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="phone-number">{{ phoneNumber }}</p>
                        <div class="action-stack">
                            <button class="btn-whatsapp" @click="openWhatsApp" type="button">
                                <font-awesome-icon :icon="['fab', 'whatsapp']" /> Open in WhatsApp
                            </button>
                            <button class="btn-secondary" @click="copyPhone" type="button">
                                <font-awesome-icon :icon="['fas', 'copy']" /> Copy Number
                            </button>
                        </div>
                        <div v-if="copied" class="copied-note">
                            <font-awesome-icon :icon="['fas', 'circle-check']" /> Phone number copied!
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
    background-color: rgba(26, 26, 26, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2050;
    padding: 20px;
}

.modal-container {
    width: 100%;
    max-width: 360px;
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
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0e8;
}

.modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -0.2px;
    margin: 0;
}

.close-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: none;
    background: none;
    border-radius: 6px;
    color: #aaa;
    cursor: pointer;
    transition: all 0.15s;
}
.close-btn:hover { background: #f0f0e8; color: #333; }
.close-btn svg { width: 13px; height: 13px; }

.modal-body { padding: 24px; text-align: center; }

.phone-number {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -0.2px;
    margin: 0 0 20px;
    font-variant-numeric: tabular-nums;
}

.action-stack { display: flex; flex-direction: column; gap: 10px; }

.btn-whatsapp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    background: #25a866;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-whatsapp:hover { background: #1f8f56; }

.btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    background: #ffffff;
    color: #333;
    border: 1px solid #e0e0d8;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-secondary:hover { border-color: #d4a853; color: #a9770c; background: #fdf8ee; }

.copied-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 14px;
    padding: 10px 14px;
    background: #e8f5ec;
    color: #2f7d4f;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}
</style>