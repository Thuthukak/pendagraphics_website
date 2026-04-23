<template>
  <div class="container-fluid p-0">
    <div class="row min-vh-100 m-0">
      <!-- Hero Image (8 columns) -->
          <div class="col-md-8 d-none d-md-block p-0">
            <img 
            src="/assets/images/auth_bannner.png" 
            alt="Hero Image" 
            class="w-100 min-vh-100 object-fit-cover">
          </div>

          <!-- Auth Form (4 columns) -->
          <div class="col-md-4 d-flex bg-neema-primary align-items-center justify-content-center p-0">
            <div class="w-100  p-4">
              <h2 class="text-center text-white fw-bold mb-3">
                Complete Your Registration
              </h2>
              <p class="mt-2 text-center text-sm text-white mb-5">
                Create your account for {{ email }}
              </p>

              <form @submit.prevent="submitForm">
                <div class="mb-3">
                    <label for="name" class="form-label text-white">
                      Full Name
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="form-control"
                      :class="{ 'border-red-300': form.errors.name }"
                      placeholder="John Doe"
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                      {{ form.errors.name[0] }}
                    </p>
                  </div>

                  <div class="mb-3">
                    <label for="password" class="form-label text-white">
                      Password
                    </label>
                    <input
                      id="password"
                      v-model="form.password"
                      type="password"
                      required
                      class="form-control"
                      :class="{ 'border-red-300': form.errors.password }"
                      placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                      {{ form.errors.password[0] }}
                    </p>
                  </div>

                  <div class="mb-3">
                    <label for="password_confirmation" class="form-label text-white">
                      Confirm Password
                    </label>
                    <input
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      type="password"
                      required
                      class="form-control"
                      placeholder="••••••••"
                    />
                  </div>
                

                
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="neema-btn neema-btn-secondary border w-100"
                  >
                    {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                  </button>
                
              </form>
            </div>
          </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AcceptInvitation',
  props: {
    token: {
      type: String,
      required: true,
    },
    email: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      form: {
        name: '',
        password: '',
        password_confirmation: '',
        processing: false,
        errors: {},
      },
    };
  },
  methods: {
    async submitForm() {
      this.form.processing = true;
      this.form.errors = {};

      try {
        const response = await axios.post(`/accept-invitation/${this.token}`, {
          name: this.form.name,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation,
        });

        // Redirect to dashboard
        window.location.href = response.data.redirect;
      } catch (error) {
        if (error.response?.status === 422) {
          this.form.errors = error.response.data.errors || {};
          if (error.response.data.message) {
            alert(error.response.data.message);
          }
        } else {
          alert('An error occurred. Please try again.');
        }
      } finally {
        this.form.processing = false;
      }
    },
  },
};
</script>