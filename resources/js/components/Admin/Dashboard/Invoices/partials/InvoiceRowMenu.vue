<template>
  <div class="menu-wrap" v-click-outside="close">
    <button class="icon-btn" ref="triggerRef" @click.stop="toggle">
      <font-awesome-icon :icon="['fas', 'ellipsis']" class="ml-1" />
    </button>

    <Teleport to="body">
      <div
        v-if="open"
        class="menu-dropdown"
        :style="dropdownStyle"
      >
        <button v-if="invoice.status === 'draft'" class="menu-item" @click="emit('send', invoice); close()">
          Send Email
        </button>
        <button class="menu-item" @click="emit('export', invoice); close()">
          Export as PDF
        </button>
        <button class="menu-item" @click="emit('reminder', invoice); close()">
          Send Reminder
        </button>
        <button class="menu-item" @click="emit('recurring', invoice); close()">
          Make Recurring
        </button>
        <button v-if="invoice.status === 'draft'" class="menu-item" @click="emit('mark-sent', invoice); close()">
          Mark as Sent
        </button>
        <button v-if="invoice.status === 'sent'" class="menu-item" @click="emit('payment', invoice); close()">
          Record Payment
        </button>
        <button class="menu-item danger" @click="emit('delete', invoice); close()">
          Delete
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'

defineProps({ invoice: Object })
const emit = defineEmits(['send', 'export', 'reminder', 'recurring', 'mark-sent', 'payment', 'delete'])

const open       = ref(false)
const triggerRef = ref(null)
const rect       = ref(null)

const DROPDOWN_HEIGHT = 260

// ── Global singleton: only one menu open at a time ───────────────────────────
// A single shared ref that holds the "close" function of whichever menu is open.
if (!window.__activeInvoiceMenuClose) window.__activeInvoiceMenuClose = null

function toggle() {
  if (open.value) {
    close()
    return
  }

  // Close whichever other menu is currently open
  if (window.__activeInvoiceMenuClose && window.__activeInvoiceMenuClose !== close) {
    window.__activeInvoiceMenuClose()
  }

  rect.value = triggerRef.value.getBoundingClientRect()
  open.value = true
  window.__activeInvoiceMenuClose = close

  // Close on any scroll anywhere in the page
  window.addEventListener('scroll', closeOnScroll, { capture: true, passive: true })
}

function close() {
  open.value = false
  window.__activeInvoiceMenuClose = null
  window.removeEventListener('scroll', closeOnScroll, { capture: true })
}

function closeOnScroll() { close() }

// Clean up if the component is destroyed while menu is open
onUnmounted(() => {
  if (window.__activeInvoiceMenuClose === close) close()
})

const dropdownStyle = computed(() => {
  if (!rect.value) return {}
  const spaceBelow = window.innerHeight - rect.value.bottom
  const flipUp     = spaceBelow < DROPDOWN_HEIGHT
  return {
    position: 'fixed',
    ...(flipUp
      ? { bottom: `${window.innerHeight - rect.value.top + 4}px` }
      : { top:    `${rect.value.bottom + 4}px` }
    ),
    right:  `${window.innerWidth - rect.value.right}px`,
    zIndex: 9999,
  }
})

const vClickOutside = {
  mounted(el, { value }) {
    el._click = (e) => { if (!el.contains(e.target)) value() }
    document.addEventListener('click', el._click)
  },
  unmounted(el) {
    document.removeEventListener('click', el._click)
  }
}
</script>

<style scoped>
.menu-wrap { position: relative; }

.icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px;
  border: none; background: none; border-radius: 6px;
  color: #aaa; cursor: pointer;
}
.icon-btn:hover { background: #f0f0e8; color: #333; }
.icon-btn svg  { width: 15px; height: 15px; }

/* Teleported — not scoped, add to global styles or use :deep() */
.menu-dropdown {
  background: white;
  border: 1px solid #e8e8e0;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  min-width: 160px;
  overflow: hidden;
}
.menu-item {
  display: block; width: 100%;
  padding: 10px 14px; text-align: left;
  border: none; background: none;
  font-size: 13px; color: #333; cursor: pointer;
}
.menu-item:hover { background: #fafaf7; }
.menu-item.danger { color: #c0392b; }
.menu-item.danger:hover { background: #fff5f5; }
</style>