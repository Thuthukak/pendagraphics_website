<template>
  <div class="min-h-screen flex flex-col">
    <Navbar />
    <main class="flex-grow">
      <slot></slot>
    </main>
    <Footer />
  </div>
</template>
  
  <script>
  import Navbar from "@/components/Home/Navbar.vue";
  import Footer from "@/components/Home/Footer.vue";
  
  export default {
    components: {  Navbar, Footer },
  };
  </script>