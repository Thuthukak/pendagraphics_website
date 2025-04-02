<template>
  <Layout>
    <!-- Hero Section -->
    <section class="relative min-h-[80vh] flex items-center bg-[#007ec7] px-4 md:px-8 overflow-hidden">
      <div class="container mx-auto relative z-10">
        <div class="flex flex-col md:flex-row items-center gap-12">
          <!-- Text Content -->
          <div class="md:w-1/2 text-center md:text-left">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
              Transform Your Digital Presence with
              <span class="text-[#f6df35]">Premium Web Solutions</span>
            </h1>
            
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto md:mx-0">
              Full-service web development & design agency specializing in Laravel & Vue.js applications. 
              From concept to deployment - we craft digital experiences that convert.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
              <button class="bg-[#f6df35] hover:bg-[#e21c26] text-black hover:text-white 
                px-8 py-4 rounded-lg font-bold transition-all duration-300 shadow-lg
                transform hover:scale-105">
                Get Free Consultation
              </button>
            </div>
          </div>

          <!-- Interactive Web Elements Visualization -->
          <div class="md:w-1/2 mt-12 md:mt-0">
            <div class="relative bg-white/10 p-8 rounded-2xl backdrop-blur-lg">
              <div class="absolute inset-0 border-2 border-[#f6df35]/30 rounded-2xl"></div>
              <div class="relative grid gap-4 grid-cols-2">
                <div class="h-40 bg-white/5 rounded-lg animate-pulse-slow"></div>
                <div class="h-48 bg-[#f6df35]/10 rounded-lg animate-pulse-slow delay-100"></div>
                <div class="h-32 bg-[#e21c26]/10 rounded-lg animate-pulse-slow delay-200"></div>
                <div class="h-44 bg-white/5 rounded-lg animate-pulse-slow delay-300"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Decorative Elements -->
      <div class="absolute inset-0 bg-grid-white/5"></div>
    </section>
  </Layout>
</template>

<script>
import Layout from "../../Layouts/HomeLayout.vue";
import { ref } from "vue";

export default {
  components: {
    Layout,
  },
  setup() {
    // For future interactive elements
    const heroVisible = ref(true);
    
    return { heroVisible };
  }
};
</script>

<style scoped>
.bg-grid-white\/5 {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='%23ffffff10'%3e%3cpath d='M0 .5H31.5V32'/%3e%3c/svg%3e");
}

@keyframes pulse-slow {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.animate-pulse-slow {
  animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }
</style>
