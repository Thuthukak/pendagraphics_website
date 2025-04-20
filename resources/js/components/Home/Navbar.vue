<template>
  <nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <!-- Logo -->
      <a href="/" class="text-xl font-bold">
        <img src="/public/assets/images/penda_logo.png" alt="Logo" class="w-[120px] h-auto" />
      </a>

      <!-- Desktop Nav -->
      <div class="hidden md:flex space-x-6 items-center relative">
        <a href="/" class="px-4 py-2 text-gray-800 hover:text-blue-600 no-underline">Home</a>
        <a href="/bookings" class="px-2 py-2 text-gray-800 hover:text-blue-600 no-underline">About</a>

        <!-- Dropdown -->
        <div class="relative" @mouseleave="isDropdownOpen = false">
          <button @click="toggleDropdown" class="px-2 py-2 text-gray-800 hover:text-blue-600">
            Services<i class="fas fa-chevron-down sm ml-2"></i>
          </button>

          <!-- Dropdown Menu -->
          <div
            v-show="isDropdownOpen"
            class="absolute left-0 mt-2 w-56 bg-white shadow-lg border rounded-lg z-50"
          >
            <a href="/services/web-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Web Design</a>
            <a href="/services/graphic-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Graphic Design</a>
            <a href="/services/product-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Product Design</a>
            <a href="/services/identity-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Identity Design</a>
            <a href="/services/e-commerce" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">E-Commerce</a>
            <a href="/services/digital-marketing" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Digital Marketing</a>
          </div>
        </div>

        <a href="/contact-us" class="px-2 py-2 text-gray-800 hover:text-blue-600">Contact Us</a>

        <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600 transition">
          Get Quote
        </a>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const isDropdownOpen = ref(false)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}
</script>
<style scope>

.a{
  text-decoration: none !important;
}
</style>
