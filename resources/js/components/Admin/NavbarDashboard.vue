<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <!-- Sidebar Toggle -->
      <button @click="$emit('toggleSidebar')" class="btn text-secondary">
        <font-awesome-icon :icon="icons.faBars" />
      </button>

      <!-- Right Section: Dark Mode, Language, Profile -->
      <div class="d-flex align-items-center gap-3">
        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode" class="btn text-secondary">
          <font-awesome-icon :icon="darkMode ? icons.faSun : icons.faMoon" />
        </button>

        <!-- Language Switcher -->
        <button @click="changeLocale" class="btn text-secondary">
          <font-awesome-icon :icon="icons.faGlobe" />
        </button>

        <!-- Profile Dropdown -->
        <div class="position-relative">
          <button 
            class="btn d-flex align-items-center"
            @click="toggleDropdown"
          >
            <img :src="user.avatar" alt="Profile" class="rounded-circle me-2" width="35" height="35" />
            <span class="d-none d-sm-inline text-dark">{{ user.name }}</span>
          </button>

          <!-- Dropdown Menu -->
          <ul v-if="dropdownOpen" class="dropdown-menu show position-absolute end-0 mt-2 shadow rounded">
            <li><button class="dropdown-item" @click="goToProfile"><font-awesome-icon :icon="icons.faUser" class="me-2" /> Profile</button></li>
            <li><button class="dropdown-item" @click="viewNotifications"><font-awesome-icon :icon="icons.faBell" class="me-2" /> Notifications</button></li>
            <li><button class="dropdown-item" @click="goToSettings"><font-awesome-icon :icon="icons.faCog" class="me-2" /> Settings</button></li>
            <li><button class="dropdown-item" @click="goHome"><font-awesome-icon :icon="icons.faHome" class="me-2" /> Home</button></li>
            <li><hr class="dropdown-divider"></li>
            <li><button class="dropdown-item text-danger" @click="logout"><font-awesome-icon :icon="icons.faSignOutAlt" class="me-2" /> Logout</button></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faBars, faMoon, faSun, faGlobe, faUser, faBell, faCog, faHome, faSignOutAlt } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

export default {
  name: "Navbar",
  components: { FontAwesomeIcon },
  data() {
    return {
      darkMode: false,
      dropdownOpen: false,
      user: {
        name: "John Doe",
        role: "Admin",
        avatar: "https://i.pravatar.cc/100", // Placeholder image
      },
      icons: { faBars, faMoon, faSun, faGlobe, faUser, faBell, faCog, faHome, faSignOutAlt },
    };
  },
  methods: {
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      document.documentElement.classList.toggle("dark", this.darkMode);
    },
    changeLocale() {
      console.log("Change language");
    },
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    goToProfile() {
      window.location.href = "/admin/profile";
      console.log("Go to Profile");
    },
    viewNotifications() {
      console.log("View Notifications");
    },
    goToSettings() {
      window.location.href = "/admin/settings";
      console.log("Go to Settings");
    },
    goHome() {
      window.location.href = "/";
      console.log("Go to Home");
    },
    logout() {
      axios.post("/logout").then(() => {
        window.location.href = "/";
      })
      console.log("Logging out...");
    },
  },
};
</script>

<style scoped>
.dropdown-menu {
  display: block;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease-in-out;
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
}
</style>
