<template>
    <!-- <Layout :seo="seo"> -->
      <div class="container-fluid p-0">
        <div class="row min-vh-100 m-0">
          <!-- Hero Image (8 columns) -->
          <div class="col-md-8 d-none d-md-block p-0">
            <img
            :src="seo.hero_image"
            alt="Hero Image"
            class="w-100 min-vh-100 object-fit-cover">
          </div>

          <!-- Auth Form (4 columns) -->
          <div class="col-md-4 d-flex bg-white align-items-center justify-content-center p-0">
            <div class="w-100  p-4">
                <div class="d-flex justify-content-center mb-4 mt-1">
                  <img :src="seo.logo" alt="Logo" style="max-width: 150px;">
                </div>
              <h2 class="text-center text-dark fw-bold mb-5">
                {{ isLogin ? "Admin Login" : "Register as Admin" }}
              </h2>

              <!-- General/server error banner -->
              <div
                v-if="serverError"
                ref="serverErrorEl"
                class="alert alert-danger py-2 d-flex align-items-center gap-2"
                :class="{ 'shake': shakeError }"
                role="alert"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="12"></line>
                  <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ serverError }}</span>
              </div>

              <form @submit.prevent="handleSubmit" novalidate>
                <div class="mb-3">
                  <label class="form-label text-dark">Email</label>
                  <input
                    v-model.trim="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email || invalidCredentials }"
                    @blur="validateField('email')"
                    @input="clearServerError"
                  >
                  <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-dark">Password</label>
                  <div class="position-relative">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="form-control pe-5"
                      :class="{ 'is-invalid': errors.password || invalidCredentials }"
                      @blur="validateField('password')"
                      @input="clearServerError"
                    >
                    <button
                      type="button"
                      class="password-toggle-btn"
                      tabindex="-1"
                      :aria-label="showPassword ? 'Hide password' : 'Show password'"
                      @click="showPassword = !showPassword"
                    >
                      <svg v-if="showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                      </svg>
                    </button>
                  </div>
                  <div class="invalid-feedback d-block" v-if="errors.password">{{ errors.password }}</div>
                </div>

                <div v-if="!isLogin" class="mb-3">
                  <label class="form-label text-white">Confirm Password</label>
                  <div class="position-relative">
                    <input
                      v-model="form.password_confirmation"
                      :type="showPasswordConfirm ? 'text' : 'password'"
                      class="form-control pe-5"
                      :class="{ 'is-invalid': errors.password_confirmation }"
                      @blur="validateField('password_confirmation')"
                    >
                    <button
                      type="button"
                      class="password-toggle-btn"
                      tabindex="-1"
                      :aria-label="showPasswordConfirm ? 'Hide password' : 'Show password'"
                      @click="showPasswordConfirm = !showPasswordConfirm"
                    >
                      <svg v-if="showPasswordConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                      </svg>
                    </button>
                  </div>
                  <div class="invalid-feedback d-block" v-if="errors.password_confirmation">
                    {{ errors.password_confirmation }}
                  </div>
                </div>

                <!-- Forgot Password (Only in login mode) -->
                <!-- <div v-if="isLogin" class="mb-3 text-end">
                  <a href="forgot-password" class="text-decoration-none text-primary">Forgot Password?</a>
                </div> -->

                <!-- Extra fields for registration -->
                <div v-if="!isLogin" class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input
                    v-model.trim="form.name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    @blur="validateField('name')"
                  >
                  <div class="invalid-feedback" v-if="errors.name">{{ errors.name }}</div>
                </div>

                <div v-if="!isLogin" class="mb-3">
                  <label class="form-label">Phone Number</label>
                  <input
                    v-model.trim="form.phone"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.phone }"
                    @blur="validateField('phone')"
                  >
                  <div class="invalid-feedback" v-if="errors.phone">{{ errors.phone }}</div>
                </div>

                <button type="submit" class="penda-btn penda-btn-primary border w-100" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {{ submitting ? (isLogin ? "Logging in..." : "Registering...") : (isLogin ? "Login" : "Register") }}
                </button>
              </form>

              <!-- <div class="text-center mt-3">
                <a href="#" class="text-white text-decoration-none" @click.prevent="toggleForm">
                  {{ isLogin ? "Need an account? Register" : "Already have an account? Login" }}
                </a>
              </div> -->

            </div>
          </div>
        </div>
      </div>
    <!-- </Layout> -->
  </template>

  <script>
  import Layout from "../Layouts/HomeLayout.vue";
  import { ref, reactive, nextTick } from "vue";
  import axios from "axios";

  export default {
    components: {
      Layout,
    },
    props: {
      seo: Object,
    },
    setup() {
      const isLogin = ref(true);
      const submitting = ref(false);
      const serverError = ref("");
      const invalidCredentials = ref(false); // true when server rejects login credentials specifically
      const shakeError = ref(false);
      const serverErrorEl = ref(null);

      const showPassword = ref(false);
      const showPasswordConfirm = ref(false);

      const form = ref({
        email: "",
        password: "",
        password_confirmation: "",
        name: "",
        phone: ""
      });

      const errors = reactive({
        email: "",
        password: "",
        password_confirmation: "",
        name: "",
        phone: ""
      });

      const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      // At least one letter, one number, min 8 chars
      const PASSWORD_REGEX = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
      const PHONE_REGEX = /^\+?[0-9\s\-()]{7,15}$/;

      const clearErrors = () => {
        Object.keys(errors).forEach((key) => (errors[key] = ""));
      };

      // Clears the server-side error state as soon as the user starts correcting
      // their credentials, so stale "wrong password" messages don't linger.
      const clearServerError = () => {
        if (serverError.value) serverError.value = "";
        if (invalidCredentials.value) invalidCredentials.value = false;
      };

      // Validates a single field and sets/clears its error message.
      // Returns true if the field is valid.
      const validateField = (field) => {
        const value = form.value[field];

        switch (field) {
          case "email":
            if (!value) {
              errors.email = "Email is required.";
            } else if (!EMAIL_REGEX.test(value)) {
              errors.email = "Please enter a valid email address.";
            } else {
              errors.email = "";
            }
            break;

          case "password":
            if (!value) {
              errors.password = "Password is required.";
            } else if (!isLogin.value && !PASSWORD_REGEX.test(value)) {
              errors.password = "Password must be at least 8 characters and include a letter and a number.";
            } else if (isLogin.value && value.length < 1) {
              errors.password = "Password is required.";
            } else {
              errors.password = "";
            }
            // Re-check confirmation whenever password changes (registration only)
            if (!isLogin.value && form.value.password_confirmation) {
              validateField("password_confirmation");
            }
            break;

          case "password_confirmation":
            if (!isLogin.value) {
              if (!value) {
                errors.password_confirmation = "Please confirm your password.";
              } else if (value !== form.value.password) {
                errors.password_confirmation = "Passwords do not match.";
              } else {
                errors.password_confirmation = "";
              }
            }
            break;

          case "name":
            if (!isLogin.value) {
              if (!value) {
                errors.name = "Full name is required.";
              } else if (value.length < 2) {
                errors.name = "Please enter your full name.";
              } else {
                errors.name = "";
              }
            }
            break;

          case "phone":
            if (!isLogin.value) {
              if (!value) {
                errors.phone = "Phone number is required.";
              } else if (!PHONE_REGEX.test(value)) {
                errors.phone = "Please enter a valid phone number.";
              } else {
                errors.phone = "";
              }
            }
            break;
        }

        return !errors[field];
      };

      // Validates all relevant fields for the current mode.
      // Returns true if the whole form is valid.
      const validateForm = () => {
        const fieldsToCheck = isLogin.value
          ? ["email", "password"]
          : ["email", "password", "password_confirmation", "name", "phone"];

        let isValid = true;
        fieldsToCheck.forEach((field) => {
          const fieldValid = validateField(field);
          if (!fieldValid) isValid = false;
        });

        return isValid;
      };

      const toggleForm = () => {
        isLogin.value = !isLogin.value;
        clearErrors();
        serverError.value = "";
        invalidCredentials.value = false;
      };

      // Shows the server error banner, flags the credential fields red (login only),
      // scrolls it into view, and triggers a brief shake so it's impossible to miss.
      const triggerServerError = (message, markCredentials = false) => {
        serverError.value = message;
        invalidCredentials.value = markCredentials;

        shakeError.value = false;
        nextTick(() => {
          shakeError.value = true;
          serverErrorEl.value?.scrollIntoView({ behavior: "smooth", block: "nearest" });
          setTimeout(() => { shakeError.value = false; }, 400);
        });
      };

      const handleSubmit = async () => {
        serverError.value = "";
        invalidCredentials.value = false;

        if (!validateForm()) {
          return;
        }

        submitting.value = true;

        try {
          const endpoint = isLogin.value ? "/login" : "/register";
          const response = await axios.post(endpoint, form.value);

          console.log(response.data);

          // Redirect to the appropriate page after login/register
          window.location.href = "/admin/dashboard";
        } catch (error) {
          console.error("Error:", error);

          if (error.response) {
            const { status, data } = error.response;

            // Laravel-style field validation errors (422)
            if (status === 422 && data?.errors) {
              const serverErrors = data.errors;

              const isCredentialFailure = isLogin.value && serverErrors.email && !serverErrors.password;

              if (isCredentialFailure) {
                const message = Array.isArray(serverErrors.email)
                  ? serverErrors.email[0]
                  : serverErrors.email;
                triggerServerError(message, true);
              } else {
                Object.keys(serverErrors).forEach((field) => {
                  if (field in errors) {
                    errors[field] = Array.isArray(serverErrors[field])
                      ? serverErrors[field][0]
                      : serverErrors[field];
                  }
                });
                triggerServerError(data.message || "Please fix the highlighted fields.");
              }

            } else if (status === 419) {
              triggerServerError("Your session has expired. Please refresh the page and try again.");

            } else if (data?.message) {
              triggerServerError(data.message);

            } else {
              triggerServerError("Something went wrong. Please try again.");
            }
          } else if (error.request) {

            triggerServerError("Unable to reach the server. Please check your connection and try again.");
          } else {
            triggerServerError("Something went wrong. Please try again.");
          }
        } finally {
          submitting.value = false;
        }
      };


      return {
        isLogin,
        form,
        errors,
        submitting,
        serverError,
        invalidCredentials,
        shakeError,
        serverErrorEl,
        showPassword,
        showPasswordConfirm,
        toggleForm,
        handleSubmit,
        validateField,
        clearServerError
      };
    }
  };
  </script>

  <style scoped>

  .container-fluid {
    height: 100vh;
    overflow: hidden;
  }

  .row {
    min-height: 100vh;
  }

  .col-md-8 img {
    height: 100vh;
    object-fit: cover;
  }

  /* Adjust form width on smaller screens */
  @media (max-width: 768px) {
    .w-75 {
      width: 100% !important;
    }
  }

  /* Password visibility toggle */
  .password-toggle-btn {
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    line-height: 0;
    color: #6c757d;
    cursor: pointer;
  }

  .password-toggle-btn:hover {
    color: #343a40;
  }

  .password-toggle-btn:focus {
    outline: none;
  }

  /* Shake animation for the server error banner so a failed login is unmistakable */
  .shake {
    animation: shake 0.4s ease-in-out;
  }

  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-6px); }
    40% { transform: translateX(6px); }
    60% { transform: translateX(-4px); }
    80% { transform: translateX(4px); }
  }
  </style>