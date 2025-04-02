<template>
    <section>
      <header>
        <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">
          Update your account's profile information and email address.
        </p>
      </header>
  
      <!-- Email Verification Form (Hidden) -->
      <form id="send-verification" @submit.prevent="sendVerificationEmail"></form>
  
      <!-- Profile Update Form -->
      <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
            class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
          />
          <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name }}</p>
        </div>
  
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            autocomplete="username"
            class="mt-1 block w-full border border-gray-300 p-2 rounded-md"
          />
          <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email }}</p>
  
          <!-- Email Verification Notice -->
          <div v-if="mustVerifyEmail && !emailVerified">
            <p class="text-sm mt-2 text-gray-800">
              Your email address is unverified.
              <button
                form="send-verification"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Click here to re-send the verification email.
              </button>
            </p>
  
            <p v-if="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
              A new verification link has been sent to your email address.
            </p>
          </div>
        </div>
  
        <div class="flex items-center gap-4">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save</button>
  
          <p v-if="status === 'profile-updated'" class="text-sm text-gray-600" v-show="showSavedMessage">
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
          name: "",
          email: "",
        },
        mustVerifyEmail: false,
        emailVerified: true,
        errors: {},
        status: "",
        showSavedMessage: false,
      };
    },
    mounted() {
      this.fetchProfile();
    },
    methods: {
      async fetchProfile() {
        try {
          const response = await axios.get("/profile");
          this.form.name = response.data.name;
          this.form.email = response.data.email;
          this.mustVerifyEmail = response.data.must_verify_email;
          this.emailVerified = response.data.email_verified;
        } catch (error) {
          console.error("Error fetching profile:", error);
        }
      },
      async updateProfile() {
        try {
          await axios.patch("/profile", this.form);
          this.status = "profile-updated";
          this.showSavedMessage = true;
          setTimeout(() => (this.showSavedMessage = false), 2000);
        } catch (error) {
          this.errors = error.response?.data?.errors || {};
        }
      },
      async sendVerificationEmail() {
        try {
          await axios.post("/email/verification-notification");
          this.status = "verification-link-sent";
        } catch (error) {
          console.error("Error sending verification email:", error);
        }
      },
    },
  };
  </script>
  