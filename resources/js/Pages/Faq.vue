<template>
    <Layout :seo="seo">
      <!-- banner section -->
      <section id="apply" class="cta">
      <div class="container" data-aos="zoom-in">
        <div class="text-center text-white">
          <h1 class="fw-bold">Frequently Asked Questions</h1>
        </div>
      </div>
    </section>
    
    <section class="banner-image">
      <div class="container mt-5 my-5 py-5 px-4">
        <FaqComponent />
      <div class="mb-5 mt-2 px-4">
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center">
            <h5 class="mb-0 fw-bold">If your question is not answered here, please</h5>
            <a href="/contact-us" class="btn btn-primary">Contact Us</a>
        </div>
      </div>
      </div>
    </section>

    </Layout>
  </template>
  
  <script setup>
  import Layout from "@/Layouts/HomeLayout.vue";
  import FaqComponent from "@/components/Home/FaqComponent.vue";
  
  defineProps({
    seo: Object,
  });
  
  </script>
  
  <style scoped>
    .cta {
  background: linear-gradient(rgba(2, 2, 2, 0.65), rgba(0, 0, 0, 0.65)), url("/public/assets/images/blue-question.webp") fixed center center;
  background-size: auto, auto;
  background-size: cover;
  padding: 120px 0;
}
.banner-image {
  position: relative;
  background-image: url(/public/assets/images/faq-BG.png);
  background-position:center center;
  background-repeat: no-repeat;
  background-size: cover;
}

.banner-image::before {
  content: ""; /* Required for pseudo-elements */
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #0d5b5b00; /* Your overlay color */
  opacity: 0.8; /* Adjust the opacity as needed */
  z-index: 1;
}
.banner-image > * {
  position: relative;
  z-index: 2;
}

  </style>
  