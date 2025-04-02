<template>
    <nav class="bg-white shadow-md sticky top-0 z-50">
      <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <!-- Logo -->
        <a href="#" class="text-xl font-bold">
            <img src="/public/assets/images/penda_logo.png" alt="Logo" style="width: 120px; height: auto;" />
        </a>
  
        <!-- Navigation -->
        <div class="hidden md:flex space-x-6 items-center">
          <a href="/" class="nav-item">Home</a>
          <a href="/bookings" class="nav-item">About</a>

          <!-- dropdown menu -->
          <div class="relative inline-block">
            <button @click="toggleDropdown" class="nav-item dropdown-toggle">Services</button>
            <div class="dropdown-menu">
              <a href="/bookings" class="dropdown-item">Web Design</a>
              <a href="/bookings" class="dropdown-item">Graphic Design</a>
              <a href="/bookings" class="dropdown-item">Product Design</a>
              <a href="/bookings" class="dropdown-item">Identity Design</a>
              <a href="/bookings" class="dropdown-item">E-Commerce</a>
              <a href="/bookings" class="dropdown-item">Digital Marketing</a>
            </div>
          </div>

          <a href="/contact-us" class="nav-item">Contact Us</a>
  
          <!-- Auth Buttons -->
          <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white">
            Get Quote
          </a>
        </div>
  
        <!-- Mobile Menu Button -->
        <button @click="toggleMobileMenu" class="md:hidden">
          <i class="fa fa-bars text-xl"></i>
        </button>
      </div>
  
      <!-- Mobile Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden bg-white shadow-md">
        <a href="/" class="block px-6 py-2">Home</a>
        <a href="#" class="block px-6 py-2">About Us</a>
        
        <div class="relative inline-block">
            <button @click="toggleDropdown" class="nav-item dropdown-toggle">Services</button>
            <div class="dropdown-menu">
              <a href="/bookings" class="dropdown-item">Web Design</a>
              <a href="/bookings" class="dropdown-item">Graphic Design</a>
              <a href="/bookings" class="dropdown-item">Product Design</a>
              <a href="/bookings" class="dropdown-item">Identity Design</a>
              <a href="/bookings" class="dropdown-item">E-Commerce</a>
              <a href="/bookings" class="dropdown-item">Digital Marketing</a>
            </div>
          </div>

        <a href="/contact-us" class="block px-6 py-2">Contact Us</a>
      </div>
    </nav>

    <!-- Modals -->
   
  </template>
  
  <script>

  import { ref } from "vue";

  export default {
    setup() {
      const isMobileMenuOpen = ref(false);
           
      const toggleMobileMenu = () => {
        isMobileMenuOpen.value = !isMobileMenuOpen.value;
      };

      const toggleDropdown = () => {
        isMobileMenuOpen.value = !isMobileMenuOpen.value;
      };

      return {
        isMobileMenuOpen,
        toggleMobileMenu,
        toggleDropdown,
      };
    },
  };

  </script>
  
  <style scoped>

    .nav-item {
      display: inline-block;
      margin-right: 20px;
      text-decoration: none;
      color: #333;
    }

    .dropdown-toggle {
      cursor: pointer;
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      background-color: #fff;
      border: 1px solid #ccc;
      padding: 10px;
    }

    .dropdown-item {
      display: block;
      margin-bottom: 5px;
      text-decoration: none;
      color: #333;
    }

    .dropdown-item:hover {
      text-decoration: underline;
    }

    .bg-blue-500 {
      background-color: #3b82f6;
    }

    .text-white {
      color: #fff;
    }
    

  </style>
  