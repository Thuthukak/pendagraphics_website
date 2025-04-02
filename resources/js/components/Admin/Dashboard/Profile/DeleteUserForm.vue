<template>
    <section class="space-y-6">
      <header>
        <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>
        <p class="mt-1 text-sm text-gray-600">
          Once your account is deleted, all of its resources and data will be permanently deleted.
          Before deleting your account, please download any data or information that you wish to retain.
        </p>
      </header>
  
      <!-- Delete Button -->
      <button @click="openModal" class="bg-red-600 text-white px-4 py-2 rounded-md">
        Delete Account
      </button>
  
      <!-- Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
          <h2 class="text-lg font-medium text-gray-900">Are you sure you want to delete your account?</h2>
          <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            Please enter your password to confirm you would like to permanently delete your account.
          </p>
  
          <!-- Password Input -->
          <div class="mt-6">
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="Password"
              class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
            />
            <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
          </div>
  
          <!-- Buttons -->
          <div class="mt-6 flex justify-end">
            <button @click="closeModal" class="bg-gray-300 px-4 py-2 rounded-md">Cancel</button>
            <button @click="deleteAccount" class="bg-red-600 text-white px-4 py-2 rounded-md ml-3">Delete Account</button>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        isModalOpen: false,
        password: "",
        errors: {},
      };
    },
    methods: {
      openModal() {
        this.isModalOpen = true;
      },
      closeModal() {
        this.isModalOpen = false;
        this.password = "";
        this.errors = {};
      },
      async deleteAccount() {
        try {
          await axios.delete("/profile", { data: { password: this.password } });
          window.location.href = "/"; // Redirect after account deletion
        } catch (error) {
          this.errors = error.response?.data?.errors || {};
        }
      },
    },
  };
  </script>
  