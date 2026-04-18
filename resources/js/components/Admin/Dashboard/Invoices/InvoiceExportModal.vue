<template>
  <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal modal-medium" @click.stop>
      <div class="modal-header">
        <h2>Export to PDF</h2>
        <button class="btn-close" @click="$emit('close')"></button>
      </div>

      <div class="modal-body text-center">
        <div>
          <img
            src="/assets/images/pdf_image.png"
            alt="PDF Icon"
            class="mx-auto mb-4 w-24 h-24"
          />
        </div>
        <p v-if="!exporting">Your invoice is ready to be downloaded as a PDF.</p>
        <p v-else class="text-muted">Generating PDF…</p>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" :disabled="exporting" @click="$emit('close')">
          Cancel
        </button>
        <button class="btn-primary" :disabled="exporting" @click="exportOnly">
          <span v-if="exporting">⏳ Generating…</span>
          <span v-else>Download Only</span>
        </button>
        <button class="btn-primary" :disabled="exporting" @click="exportAndMark">
          <span v-if="exporting">⏳ Generating…</span>
          <span v-else>Download &amp; Mark as Sent</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['show', 'invoice'],
  emits: ['close', 'mark-sent'],

  data() {
    return { exporting: false };
  },

  methods: {
    getCsrf() {
      return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    },

    async downloadPdf() {
      this.exporting = true;
      try {
        const res = await fetch(`/api/invoices/${this.invoice.id}/export`, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': this.getCsrf(),
          },
        });

        if (!res.ok) throw new Error('Export failed');

        const blob = await res.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `invoice-${this.invoice.invoice_number}.pdf`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
      } catch (err) {
        console.error(err);
        alert('Failed to generate PDF. Please try again.');
      } finally {
        this.exporting = false;
      }
    },

    async exportOnly() {
      await this.downloadPdf();
      this.$emit('close');
    },

    async exportAndMark() {
      await this.downloadPdf();
      this.$emit('mark-sent', this.invoice);
      this.$emit('close');
    },
  },
};
</script>

<style scoped>
.modal { max-height: 56vh !important; }
.text-muted { color: #888; font-style: italic; }
</style>