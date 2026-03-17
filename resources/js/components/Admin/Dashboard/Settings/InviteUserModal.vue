<template>
  <teleport to="body">
    <div
      v-if="show"
      class="modal-backdrop"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="modal-container" @click="$emit('close')"></div>
      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6" @click.stop>
            <div>
              <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                Invite New User
              </h3>
              <form @submit.prevent="submitForm" class="mt-4">
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address
                  </label>
                  <input
                    v-model="formData.email"
                    type="email"
                    id="email"
                    required
                    class="mt-1 block w-full rounded border-1 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :class="{ 'border-red-300': errors.email }"
                  />
                  <p v-if="errors.email" class="mt-2 text-sm text-red-600">
                    {{ errors.email }}
                  </p>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 mt-4">
                    <div>
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="penda-btn penda-btn-primary"
                  >
                    {{ isSubmitting ? 'Sending...' : 'Send Invitation' }}
                  </button>
                  </div>
                  <div>
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="penda-btn penda-btn-secondary"
                  >
                    Cancel
                  </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  name: 'InviteUserModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    isSubmitting: {
      type: Boolean,
      default: false
    }
  },

  emits: ['close', 'submit'],

  data() {
    return {
      formData: {
        email: ''
      },
      errors: {
        email: ''
      }
    };
  },

  watch: {
    show(newVal) {
      if (newVal) {
        this.resetForm();
      }
    }
  },

  methods: {
    resetForm() {
      this.formData = {
        email: ''
      };
      this.errors = {
        email: ''
      };
    },

    submitForm() {
      // Reset errors
      this.errors = {
        email: ''
      };

      // Basic validation
      if (!this.formData.email) {
        this.errors.email = "Email address is required";
        return;
      }

      // Email format validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.formData.email)) {
        this.errors.email = "Please enter a valid email address";
        return;
      }

      this.$emit('submit', { ...this.formData });
    },

    setErrors(serverErrors) {
      if (serverErrors) {
        Object.keys(serverErrors).forEach(key => {
          if (this.errors.hasOwnProperty(key)) {
            this.errors[key] = Array.isArray(serverErrors[key]) 
              ? serverErrors[key][0] 
              : serverErrors[key];
          }
        });
      }
    }
  }
};
</script>
<style>

.modal-backdrop {
  --bs-backdrop-zindex: 1050;
  --bs-backdrop-bg: #0000009e;
  --bs-backdrop-opacity: 0.5;
}

</style>