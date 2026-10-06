<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Desktop Sidebar - Hidden on mobile -->
    <div 
      v-if="!isMobile"
      :class="[
        'transition-all duration-300 ease-in-out h-full flex flex-col',
        sidebarCollapsed ? 'w-16' : 'w-64'
      ]"
    >
      <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-0">
      <!-- Navbar -->
      <NavbarDashboard 
        :sidebar-collapsed="sidebarCollapsed" 
        :is-mobile="isMobile"
        @toggle-sidebar="toggleSidebar" 
      />
      
      <!-- Main content area with safe area support -->
      <main 
        class="bg-white flex-1 overflow-y-auto"
        :style="mainContentStyle"
      >
        <div class="p-4 md:p-6">
          <router-view></router-view>
        </div>
      </main>
    </div>
    
    <!-- Mobile Bottom Navigation with safe area support -->
    <div 
      v-if="isMobile"
      class="fixed bottom-0 left-0 right-0  border-t border-gray-600 z-50"
      :style="mobileNavStyle"
      ref="mobileNav"
    >
      <MobileBottomNav />
    </div>
  </div>
</template>

<script>
import Sidebar from "../components/Admin/Sidebar.vue";
import NavbarDashboard from "../components/Admin/NavbarDashboard.vue";
import MobileBottomNav from "../components/Admin/Dashboard/MobileBottomNav.vue";

export default {
  name: 'DashboardLayout',
  components: { 
    Sidebar, 
    NavbarDashboard,
    MobileBottomNav 
  },
  data() {
    return {
      sidebarCollapsed: false,
      isMobile: false,
      mobileNavHeight: 64, // Default height in pixels
    };
  },
  computed: {
    mainContentStyle() {
      if (this.isMobile) {
        return {
          paddingBottom: `${this.mobileNavHeight + 16}px`, // Nav height + extra spacing
          paddingBottom: `calc(${this.mobileNavHeight}px + env(safe-area-inset-bottom) + 16px)` // Support for devices with notches
        };
      }
      return {
        paddingBottom: '24px'
      };
    },
    mobileNavStyle() {
      return {
        paddingBottom: 'env(safe-area-inset-bottom)' // Support for devices with home indicators
      };
    }
  },
  mounted() {
    this.checkMobile();
    window.addEventListener('resize', this.checkMobile);
    
    // Measure actual mobile nav height after render
    this.$nextTick(() => {
      this.measureMobileNavHeight();
    });
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.checkMobile);
  },
  methods: {
    checkMobile() {
      this.isMobile = window.innerWidth < 768;
      // Auto-collapse sidebar on tablet sizes
      if (window.innerWidth < 1024 && window.innerWidth >= 768) {
        this.sidebarCollapsed = true;
      }
      
      // Re-measure nav height when switching between mobile/desktop
      this.$nextTick(() => {
        this.measureMobileNavHeight();
      });
    },
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    },
    measureMobileNavHeight() {
      if (this.isMobile && this.$refs.mobileNav) {
        // Get the actual rendered height of the mobile navigation
        const rect = this.$refs.mobileNav.getBoundingClientRect();
        this.mobileNavHeight = rect.height || 64; // Fallback to 64px
      }
    }
  },
};
</script>

<style scoped>
/* Custom scrollbar */
main::-webkit-scrollbar {
  width: 6px;
}

main::-webkit-scrollbar-track {
  background: #f1f1f1;
}

main::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

main::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Ensure proper viewport handling on iOS */
@supports (padding: max(0px)) {
  main {
    padding-bottom: max(16px, env(safe-area-inset-bottom));
  }
}
</style>