<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar stays fixed with a set width -->
    <Sidebar />
    
    <div class="flex-1 flex flex-col">
      <!-- Navbar stays fixed at the top -->
      <Navbar />
      
      <!-- Main content area which should grow or scroll as needed -->
      <main class="flex-1 p-6 overflow-y-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import Sidebar from "../components/Admin/Sidebar.vue";
import Navbar from "../components/Admin/Navbar.vue";

export default {
  name: 'DashboardLayout',
  components: { Sidebar, Navbar },
};
</script>

