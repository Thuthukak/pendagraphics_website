<template>
    <navbar />
    <!-- banner -->
    <section id="apply" class="cta">
      <div class="container px-5" data-aos="zoom-in">
        <div class="text-center text-white">
          <h1 class="fw-bold">Identity Design</h1>
          <p>Your brand identity is more than a logo — it’s your voice, your style, your first impression. We create cohesive, memorable brand systems that resonate, connect, and last.</p>
        </div>
      </div>
    </section>

    <section>
      <div class="container mt-5 my-5 py-5 px-4">
        <div class="row">
            <div class="col-md-5">
            <img src="/public/assets/images/branding-identity.jpg" 
                alt="Web design" 
                class="shadow-lg rounded align-self-center"
                style="width:400px; height:400px; object-fit: cover;"  />
          </div>
          <div class="col-md-7 align-self-center">
            <h2 class="fw-bold mb-3">Build a Brand People Remember</h2>
            <p>We design complete visual identities that go beyond aesthetics — they tell your story. From logos and color palettes to business cards and brand guidelines, every element works together to create a consistent, professional image that builds trust and leaves a lasting impression.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="py-5 banner-image">
  <div class="container px-4">
    <div class="row">
      <div class="col-md-4 col-sm-6 mt-4 mb-4">
        <div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box">
            <div class="icon-circle">
            <i class="fas fa-signature"></i>
            </div>
            <div>
            <h3 class="fw-bold mb-1">Logo Design</h3>
            <p class="mb-0">We craft distinctive logos that capture your brand’s essence and stand the test of time.</p>
            </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6 mt-4 mb-4">
        <div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box">
            <div class="icon-circle">
            <i class="fas fa-palette"></i>
            </div>
            <div>
            <h3 class="fw-bold mb-1">Color & Typography</h3>
            <p class="mb-0">We develop consistent color schemes and font pairings that align with your brand personality.</p>
            </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6 mt-4 mb-4">
        <div class="p-3 border-0 text-white d-flex align-items-center justify-content-between gap-3 icon-box">
            <div class="icon-circle">
            <i class="fas fa-id-badge"></i>
            </div>
            <div>
            <h3 class="fw-bold mb-1">Brand Collateral</h3>
            <p class="mb-0">From business cards to letterheads, we design cohesive materials that strengthen your identity.</p>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>


    <!-- case studies -->
    <section>
      <div class="container mt-5 my-5 py-5 px-4">
        <div class="row">
          <div class="col-md-6 align-self-center">
          <div class="mt-5">
            <h1 class="fw-bold mb-3">Case Study</h1>
            <p>Experience the transformation of Bakili, an innovative IT company, through our bespoke identity design. From crafting captivating business colors to developing a distinctive logo and designing professional business cards, we’ve meticulously curated every element to reflect Bakili’s unique vision and values. Witness the seamless fusion of creativity and functionality as we bring Bakili’s brand identity to life, setting them apart in the competitive tech landscape</p> 
          </div>
          </div>
          <div class="col-md-6">
            <IdentityDesignCarousel />
          </div>
        </div>
      </div>
    </section>

    
  
    <Footer />
  </template>
  
  <script setup>
  import Navbar from "@/components/Home/Navbar.vue";
  import Footer from "@/components/Home/Footer.vue";
  import IdentityDesignCarousel from "./IdentityDesignCarousel.vue";


  </script>

  <style scoped>
  .hero {
  height: 75vh; /* Adjust height as needed */
  overflow: hidden;
  position: relative;
}

.banner-image {
  position: relative;
  background-image: url(/public/assets/images/Visual-Identity.png);
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
  background-color: #01213b; /* Your overlay color */
  opacity: 0.8; /* Adjust the opacity as needed */
  z-index: 1;
}
.banner-image > * {
  position: relative;
  z-index: 2;
}

.overlay-content {
  width: 90%;
  max-width: 800px;
  position: relative;
}
.cta {
  background: linear-gradient(rgba(2, 2, 2, 0.5), rgba(0, 0, 0, 0.5)), url("/public/assets/images/Identity.svg") fixed center center;
    background-size: auto, auto;
  background-size: cover;
  padding: 120px 0;
}

.icon-box {
  background: transparent;
  border-radius: 12px;
  padding: 20px;
}

.icon-circle {
  width: 60px;
  height: 60px;
  border: 2px solid #ffffff; /* Yellow ring */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: #ffffff;
  margin-left: 15px;
  flex-shrink: 0;
}
</style>
  