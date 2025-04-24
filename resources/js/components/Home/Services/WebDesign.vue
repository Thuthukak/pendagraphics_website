<template>
    <navbar />
    <!-- banner -->
    <section id="apply" class="cta">
      <div class="container" data-aos="zoom-in">
        <div class="text-center text-white">
          <h2 class="fw-bold">Website Design</h2>
          <p> At Penda Graphics, our web design services combine creativity and functionality to give your brand an unforgettable digital presence</p>
        </div>
      </div>
    </section>

    <section>
      <div class="container mt-5 my-5 py-5 px-4">
        <div class="row">
          <div class="col-md-6">
            <h2 class="fw-bold ">What we offer</h2>
            <ul class="list-disc pl-6 space-y-2 mt-3">
              <li>Custom website design tailored to your brand</li>
              <li>Responsive, mobile-friendly layouts</li>
              <li>SEO-optimized structure</li>
              <li>Modern UI/UX design practices</li>
              <li>Fast loading times and performance optimization</li>
            </ul>
          </div>
          <div class="col-md-6">
            <img src="/public/assets/images/homepage-seen-computer-screen.jpg" 
                alt="Web design" 
                class="rounded-lg shadow-md"
                style="width:500px; height:300px; object-fit: cover;"  />
          </div>
        </div>
      </div>
    </section>

    <section class="py-5 banner-image">
      <div class="container px-4">
            <div class="row">
              <div class="col-md-4 col-sm-6 mt-4 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">Modern & Clean</h4>
                    <p>At Website Design Solutions, we craft websites using cutting-edge trends and technologies to meet the demands of today’s digital landscape.</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 mt-4 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">Easy to use</h4>
                    <p>User Experience (UX) has emerged as a critical aspect in website design, influencing how visitors interact, comprehend, and engage with your company’s online presence.</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 mt-4 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">Responsive Design</h4>
                    <p>A responsive website dynamically adjusts its layout and features to suit the screen size and browser specifications, ensuring optimal functionality and preserving its visual appeal across various devices.</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">Quick Turnaround Time</h4>
                    <p>We utilize cutting-edge technology tools to expedite the development process, enabling us to meet our clients’ deadlines efficiently and effectively.</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">SEO Friendly</h4>
                    <p>We strategically position your brand across various platforms, ensuring it stands out and outperforms your competitors.</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 mb-4">
                <div class="p-3 border-0">
                  <div class="align-items-center mb-2 text-white">
                    <h4 class="fw-bold">Tech Support</h4>
                    <p>Quick technical support to enure the smooth running of you website and respond to enquiries on content changes.</p>
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
          <div class="col-md-6 ">
          <div class="mt-5">
            <h1 class="fw-bold ">Case Studies</h1>
            <p>Dive into our collection of captivating web design case studies, showcasing our expertise in crafting exceptional online experiences for a diverse range of clients. From startups to established brands, our portfolio highlights our ability to blend creativity with functionality, resulting in websites that not only engage users but also drive tangible results.
            </p>
          </div>
          </div>
          <div class="col-md-6">
            <WebDesignCarousel />
          </div>
        </div>
      </div>
    </section>

    
  
    <Footer />
  </template>
  
  <script setup>
  import Navbar from "@/components/Home/Navbar.vue";
  import Footer from "@/components/Home/Footer.vue";
  import WebDesignCarousel from "./WebDesignCarousel.vue";


  </script>

  <style scoped>
  .hero {
  height: 75vh; /* Adjust height as needed */
  overflow: hidden;
  position: relative;
}

.banner-image {
  background-image: linear-gradient(#5396fc , #2079fd );
}

.banner-image::before {
  background: rgba(53, 111, 238, 0.651); /* Dark overlay to dim the image */
}

.overlay-content {
  width: 90%;
  max-width: 800px;
  position: relative;
}
.cta {
  background: linear-gradient(rgba(2, 2, 2, 0.5), rgba(0, 0, 0, 0.5)), url("/public/assets/images/web-design-banner.jpg") fixed center center;
    background-size: auto, auto;
  background-size: cover;
  padding: 120px 0;
}
</style>
  