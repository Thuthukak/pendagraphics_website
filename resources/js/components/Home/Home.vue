<template>
  <Layout>
    <!-- Hero Section -->
    <section class="hero position-relative text-white text-center">
      <div class="banner-image"></div>
      <div class="overlay-content position-absolute top-50 start-50 translate-middle text-center">
        <h1 class="display-4 fw-bold">Strengthening Advancements in Potential and Excellence</h1>
        <p class="lead">Promoting growth and development through education, enhanced opportunities, and the connection of talent to the global market</p>
        <button @click="openBookingModal" class="btn btn-light btn-lg mt-3">Book Now</button>
      </div>
    </section>


  </Layout>

  <!-- Modals -->
<BookingModal :isOpen="showBookingModal" @close="closeBookingModal" />
</template>

<script>
import Layout from "../../Layouts/HomeLayout.vue";
import BookingModal from "@/components/Home/BookingModal.vue";
import { ref } from "vue"; 


export default {
  components: {
    Layout,
    BookingModal,
  },

  setup() {
    const showBookingModal = ref(false);
    

    const openBookingModal = () => {
      showBookingModal.value = true;
      
    };
    const closeBookingModal = () => showBookingModal.value = false;

    return {
      showBookingModal,
      openBookingModal,
      closeBookingModal,
    };
  }
  
};
</script>

<style scoped>
 
 .hero {
  height: 75vh; /* Adjust height as needed */
  overflow: hidden;
  position: relative;
}

.banner-image {
  background: url("/public/assets/images/hero.jpg") center/cover no-repeat;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

.banner-image::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4); /* Dark overlay to dim the image */
}

.overlay-content {
  width: 90%;
  max-width: 800px;
  position: relative;
}

.cta {
  background: linear-gradient(rgba(2, 2, 2, 0.5), rgba(0, 0, 0, 0.5)), url("/public/assets/images/cta-bg.jpg") fixed center center;
    background-size: auto, auto;
  background-size: cover;
  padding: 120px 0;
}

</style>
