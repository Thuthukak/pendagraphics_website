<template>
    <section>
      <header>
        <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
        <p class="mt-1 text-sm text-gray-600">
          Ensure your account is using a long, random password to stay secure.
        </p>
      </header>
  
      <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
        <div>
          <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
          <input
            id="current_password"
            type="password"
            v-model="form.current_password"
            autocomplete="current-password"
            class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
          />
          <p v-if="errors.current_password" class="mt-2 text-sm text-red-600">{{ errors.current_password }}</p>
        </div>
  
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
          <input
            id="password"
            type="password"
            v-model="form.password"
            autocomplete="new-password"
            class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
          />
          <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
        </div>
  
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input
            id="password_confirmation"
            type="password"
            v-model="form.password_confirmation"
            autocomplete="new-password"
            class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
          />
          <p v-if="errors.password_confirmation" class="mt-2 text-sm text-red-600">{{ errors.password_confirmation }}</p>
        </div>
  
        <div class="flex items-center gap-4">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save</button>
  
          <p v-if="status === 'password-updated'" class="text-sm text-gray-600" v-show="showSavedMessage">
            Saved.
          </p>
        </div>
      </form>
    </section>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        form: {
          current_password: "",
          password: "",
          password_confirmation: "",
        },
        errors: {},
        status: "",
        showSavedMessage: false,
      };
    },
    methods: {
      async updatePassword() {
        try {
          await axios.put("/api/user/password", this.form);
          this.status = "password-updated";
          this.showSavedMessage = true;
          this.form.current_password = "";
          this.form.password = "";
          this.form.password_confirmation = "";
          setTimeout(() => (this.showSavedMessage = false), 2000);
        } catch (error) {
          this.errors = error.response?.data?.errors || {};
        }
      },
    },
  };
  </script>
  