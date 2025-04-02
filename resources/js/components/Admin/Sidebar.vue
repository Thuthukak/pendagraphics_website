<template>
  <aside class="bg-dark text-white vh-100 d-flex flex-column p-3">
    <!-- Logo Section -->
    <div class="d-flex align-items-center gap-3 mb-4">
      <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-lg" style="width: 40px; height: 40px;">
        <span class="fw-bold">KFI</span>
      </div>
      <h4 class="m-0">KOFI</h4>
    </div>

    <!-- Navigation Links -->
    <ul class="list-group list-group-flush flex-grow-1">
      <li class="list-group-item bg-transparent border-0">
        <router-link to="/admin/dashboard" class="d-flex align-items-center text-white p-2 rounded hover-effect">
          <font-awesome-icon :icon="['fas', 'home']" class="me-2" />
          Dashboard
        </router-link>
      </li>
      <li class="list-group-item bg-transparent border-0">
        <router-link to="/admin/bookings" class="d-flex align-items-center text-white p-2 rounded hover-effect">
          <font-awesome-icon :icon="['fas', 'calendar-alt']" class="me-2" />
          Bookings
        </router-link>
      </li>
      <!-- services -->
       <li class="list-group-item bg-transparent border-0">
        <router-link to="/admin/services" class="d-flex align-items-center text-white p-2 rounded hover-effect">
          <font-awesome-icon :icon="['fas', 'building']" class="me-2" />
          Services
        </router-link>
      </li>
      <li class="list-group-item bg-transparent border-0">
        <router-link to="/admin/settings" class="d-flex align-items-center text-white p-2 rounded hover-effect">
          <font-awesome-icon :icon="['fas', 'cog']" class="me-2" />
          Settings
        </router-link>
      </li>
    </ul>

    <!-- Logout -->
    <div>
      <button @click="logout" class="d-flex align-items-center text-white p-2 rounded hover-effect text-danger">
        <font-awesome-icon :icon="['fas', 'sign-out-alt']" class="me-2" />
        Logout
      </button>
    </div>
  </aside>
</template>

<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import axios from 'axios';

export default {
  name: "Sidebar",
  components: {
    FontAwesomeIcon,
  },
  methods: {
    logout() {
      axios.post('/logout').then(() => {
        window.location.href = '/login';
      });
    },
  },
};
</script>

<style scoped>
/* Sidebar takes full height */
aside {
  width: 260px;
}

/* Hover effect for links */
.hover-effect {
  display: block;
  text-decoration: none;
  transition: background-color 0.3s ease-in-out;
}

.hover-effect:hover {
  background-color: lightblue;
}
</style>
