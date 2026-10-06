<template>
  <div class="menu-wrap" ref="wrapEl">
    <button class="icon-btn" ref="btnEl" title="More actions" @click="toggle">
      <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" />
    </button>

    <Teleport to="body">
      <div
        v-if="open"
        class="menu-dropdown"
        :style="{ top: coords.top + 'px', left: coords.left + 'px' }"
        @click="open = false"
      >
        <button v-if="deliveryNote.status === 'draft'" class="menu-item" @click="$emit('dispatch', deliveryNote)">
          <font-awesome-icon :icon="['fas', 'truck']" /> Mark as dispatched
        </button>
        <button v-if="deliveryNote.status !== 'delivered' && deliveryNote.status !== 'cancelled'" class="menu-item" @click="$emit('deliver', deliveryNote)">
          <font-awesome-icon :icon="['fas', 'check']" /> Mark as delivered
        </button>
        <button class="menu-item" @click="$emit('export', deliveryNote)">
          <font-awesome-icon :icon="['fas', 'file-pdf']" /> Export PDF
        </button>
        <button v-if="deliveryNote.status !== 'delivered' && deliveryNote.status !== 'cancelled'" class="menu-item" @click="$emit('cancel', deliveryNote)">
          <font-awesome-icon :icon="['fas', 'ban']" /> Cancel
        </button>
        <button v-if="deliveryNote.status !== 'delivered'" class="menu-item menu-item-danger" @click="$emit('delete', deliveryNote)">
          <font-awesome-icon :icon="['fas', 'trash']" /> Delete
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({ deliveryNote: { type: Object, required: true } })
defineEmits(['dispatch', 'deliver', 'cancel', 'export', 'delete'])

const open   = ref(false)
const wrapEl = ref(null)
const btnEl  = ref(null)
const coords = reactive({ top: 0, left: 0 })

function toggle() {
  if (!open.value) positionMenu()
  open.value = !open.value
}

function positionMenu() {
  const rect = btnEl.value.getBoundingClientRect()
  const menuWidth = 190 // matches .menu-dropdown min-width
  coords.top  = rect.bottom + 4
  coords.left = rect.right - menuWidth
}

function onClickOutside(e) {
  if (wrapEl.value && !wrapEl.value.contains(e.target) && !e.target.closest('.menu-dropdown')) {
    open.value = false
  }
}

function onReposition() { if (open.value) positionMenu() }

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
.menu-wrap { position: relative; display: inline-block; }
.icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: none; background: none; border-radius: 6px; color: #aaa; cursor: pointer; }
.icon-btn:hover { background: #f0f0e8; color: #333; }
.menu-dropdown { position: fixed; background: white; border: 1px solid #e8e8e0; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); min-width: 190px; z-index: 99999; overflow: hidden; }
.menu-item { display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 14px; background: none; border: none; font-size: 13px; color: #333; cursor: pointer; text-align: left; }
.menu-item:hover { background: #fafaf7; }
.menu-item svg { width: 13px; height: 13px; color: #999; }
.menu-item-danger { color: #c0392b; }
.menu-item-danger svg { color: #c0392b; }
</style>