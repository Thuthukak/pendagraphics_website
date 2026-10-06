<template>
  <div class="row-menu" ref="rootEl">
    <button class="icon-btn" ref="btnEl" title="More actions" @click.stop="toggle">
      <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" />
    </button>

    <Teleport to="body">
      <div
        v-if="open"
        class="dropdown"
        :style="{ top: coords.top + 'px', left: coords.left + 'px' }"
        @click.stop
      >
      <button @click="emitAndClose('view')">
        <font-awesome-icon :icon="['fas', 'eye']" /> View details
      </button>
      <button @click="emitAndClose('resend')" :disabled="estimate.status === 'converted'">
        <font-awesome-icon :icon="['fas', 'envelope']" /> Resend email
      </button>
      <button @click="emitAndClose('download')">
        <font-awesome-icon :icon="['fas', 'file-pdf']" /> Download PDF
      </button>
      <button @click="emitAndClose('duplicate')">
        <font-awesome-icon :icon="['fas', 'clone']" /> Duplicate
      </button>

      <div class="divider"></div>

      <button
        v-if="estimate.status !== 'converted'"
        class="highlight"
        @click="emitAndClose('convert')"
        :disabled="estimate.status === 'cancelled'"
      >
        <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" /> Convert to invoice
      </button>
      <button v-else class="muted" disabled>
        <font-awesome-icon :icon="['fas', 'circle-check']" /> Converted (Inv. {{ estimate.invoice?.invoice_number || estimate.invoice_id }})
      </button>

      <div class="divider"></div>

      <button
        class="danger"
        @click="emitAndClose('delete')"
        :disabled="estimate.status === 'converted'"
      >
        <font-awesome-icon :icon="['fas', 'trash']" /> Delete
      </button>
    </div>
  </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  estimate: { type: Object, required: true },
})

const emit = defineEmits(['view', 'resend', 'download', 'duplicate', 'convert', 'delete'])

const open = ref(false)
const rootEl = ref(null)
const btnEl  = ref(null)
const coords = reactive({ top: 0, left: 0 })

function toggle() {
  if (!open.value) positionDropdown()
  open.value = !open.value
}

function positionDropdown() {
  const rect = btnEl.value.getBoundingClientRect()
  const menuWidth = 210 // matches .dropdown min-width
  coords.top  = rect.bottom + 4          // 4px gap below the button
  coords.left = rect.right - menuWidth   // right-align to the button, like before
}

function emitAndClose(name) {
  emit(name, props.estimate)
  open.value = false
}
function onClickOutside(e) {
  if (rootEl.value && !rootEl.value.contains(e.target) &&
      !e.target.closest('.dropdown')) {
    open.value = false
  }
}

// reposition on scroll/resize while open, so it doesn't drift from the button
function onReposition() { if (open.value) positionDropdown() }

onMounted(() => {
  document.addEventListener('click', onClickOutside)
  window.addEventListener('scroll', onReposition, true)
  window.addEventListener('resize', onReposition)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
  window.removeEventListener('scroll', onReposition, true)
  window.removeEventListener('resize', onReposition)
})

</script>

<style scoped>
.row-menu { position: relative; display: inline-block; }
.icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px; border: none; background: none; border-radius: 6px;
  color: #aaa; cursor: pointer; transition: all 0.15s;
}
.icon-btn:hover { background: #f0f0e8; color: #333; }

.dropdown {
  position: fixed;
  top: 34px;
  min-width: 210px;
  background: white;
  border: 1px solid #e8e8e0;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  padding: 6px;
  z-index: 99999;
}
.dropdown button {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  text-align: left;
  padding: 8px 10px;
  border: none;
  background: none;
  border-radius: 6px;
  font-size: 13px;
  color: #333;
  cursor: pointer;
}
.dropdown button:hover:not(:disabled) { background: #f7f7f2; }
.dropdown button:disabled { opacity: 0.4; cursor: not-allowed; }
.dropdown button svg { width: 13px; }
.dropdown .highlight { color: #2f9e8f; font-weight: 600; }
.dropdown .danger { color: #c0392b; }
.dropdown .muted { color: #999; font-style: italic; }
.divider { height: 1px; background: #f0f0e8; margin: 4px 2px; }
</style>