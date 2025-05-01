<template>
  <nav class="bg-shape-primary shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <!-- Logo - keeping original path -->
      <a href="#" class="text-xl font-bold">
        <img src="/public/assets/images/penda_logo.png" alt="Logo" style="width: 160px; height: auto;" />
      </a>

      <!-- Navigation -->
      <div class="hidden md:flex space-x-6 items-center">
        <a href="/" class="nav-item">Home</a>
        <a href="about-us" class="nav-item">About Us</a>

        <!-- Dropdown - fixing visibility -->
        <div class="relative">
          <button @click="toggleDropdown" class="nav-item dropdown flex items-center text-gray-600">
            Services
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-if="isDropdownOpen" class="absolute bg-shape-primary shadow-lg py-2 w-48 mt-2 rounded-lg z-50">
            <a href="/services/web-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Web Design</a>
            <a href="/services/graphic-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Graphic Design</a>
            <a href="/services/product-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Product Design</a>
            <a href="/services/identity-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Identity Design</a>
            <a href="/services/e-commerce" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">E-Commerce</a>
            <a href="/services/digital-marketing" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Digital Marketing</a>
          </div>
        </div>
        <a href="/faq" class="nav-item">FAQ</a>
        <a href="/contact-us" class="nav-item">Contact Us</a>
        <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600 transition">
          Get Quote
        </a>
      </div>

      <!-- Mobile Menu Button - fixing icon visibility -->
      <button @click="toggleMobileMenu" class="md:hidden text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>

    <!-- Mobile Menu - fixing text color -->
    <div v-if="isMobileMenuOpen" class="md:hidden bg-shape-primary shadow-md">
      <a href="/" class="block px-6 py-2 text-black">Home</a>
      <a href="#" class="block px-6 py-2 text-black">About Us</a>
      <button @click="toggleDropdown" class="nav-item dropdown  pl-6  py-2 flex items-center">
            Services
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-if="isDropdownOpen" class="absolute bg-shape-primary shadow-lg py-2 w-48 mt-2 rounded-lg z-50">
            <a href="/services/web-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Web Design</a>
            <a href="/services/graphic-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600 no-underline">Graphic Design</a>
            <a href="/services/product-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Product Design</a>
            <a href="/services/identity-design" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Identity Design</a>
            <a href="/services/e-commerce" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">E-Commerce</a>
            <a href="/services/digital-marketing" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">Digital Marketing</a>
          </div>
      <a href="/faq" class="nav-item">FAQ</a>
      <a href="/contact-us" class="block px-6 py-2 text-black">Contact Us</a>
      <div class="px-6 py-2 flex space-x-2">
      </div>
    </div>
  </nav>

</template>

<script>
// Keep your original imports

export default {
  data() {
    return {
      isDropdownOpen: false,
      isMobileMenuOpen: false
    };
  },
  methods: {
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    }
  }
};
</script>

<style scoped>
.nav-item {
  color: rgb(0, 0, 0);
  padding-left: 0.75rem;  /* equivalent to px-3 */
  padding-right: 0.75rem;
  padding-top: 0.5rem;     /* equivalent to py-2 */
  padding-bottom: 0.5rem;
}

.nav-item:hover {
  color: #0d6efd; /* replace with your actual 'shapeOrange' color if different */
}

.dropdown {
  position: relative;
}


.bg-shape-primary {
  background-color: #ffffff !important;
}

a {
  text-decoration: none;
}

/* Adding explicit text color for menu items */
.nav-item, .block {
  color: rgb(0, 0, 0);
}
.block:hover {
  color: #0d6efd;
}

/* Making sure hover color is defined */
.hover\:text-shapeOrange:hover {
  color: #0d6efd;
}
</style>