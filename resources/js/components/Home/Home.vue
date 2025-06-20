<template>
  <Layout>
    <!-- hero section -->
    <section class="about py-5 bg-light bg-md-white">
      <div class="container px-4">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="content">
              <h2 class="fw-bold">Empowering Your Brand with Stunning Designs & Websites</h2>
              <p class="mt-4 text-secondary">
                We are a team of passionate designers and developers dedicated to creating exceptional digital experiences for our clients.
              </p>
              <button @click.prevent="openQuoteModal" class="btn btn-primary rounded">Get A Quote</button>   
            </div>
          </div>
          <!-- hero images - hidden on mobile -->
          <div class="col-md-6 d-none d-md-block">
              <div class="row">
                <div class="col-md-6">
                  <div class="card border-0 shadow rounded mt-5">                 
                      <img src="/public/assets/images/left.png" alt="Hero Image" class="w-100 rounded" style="height: 400px; object-fit: cover;">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card border-0 shadow ">
                        <img src="/public/assets/images/top.png" alt="Hero Image" class="w-100 rounded" style="height: 200px; object-fit: cover; ">
                      </div>
                    </div>
                    <div class="col-md-12 mt-4">
                      <div class="card border-0 shadow">
                        <img src="/public/assets/images/right.png" alt="Hero Image" class="w-100 rounded" style="height: 200px; object-fit: cover;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </section>


    <!-- Our Featured Services -->
    <section class="py-5">
      <div class="container px-4">
        <h2 class="fw-bold mb-4 text-center">Our Featured Services</h2>
        <div class="row">
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/worldwide.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/web-design" class="text-decoration-none">Web Development</a>
                </h4>
              </div>
              <p>
                We incorporate the latest design trends, intuitive navigation, and engaging visuals to ensure that your website stands out.
              </p>
              <a href="/services/web-design" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/dairy-products.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/product-design" class="text-decoration-none">Product Design</a>
                </h4>
              </div>
              <p>
                We specialize in turning your innovative ideas into tangible products that resonate with your audience and drive success.
              </p>
              <a href="/services/product-design" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/design.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/graphic-design" class="text-decoration-none">Graphic Design</a>
                </h4>
              </div>
              <p>
                Posters, flyers, and banners, brochures, business cards, infographics, social media graphics, and packaging design.
              </p>
              <a href="/services/graphic-design" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/passport.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/identity-design" class="text-decoration-none">Identity Design</a>
                </h4>
              </div>
              <p>
                We help develop a strong brand identity through logo design, brand guidelines, brand strategy, and brand messaging.
              </p>
              <a href="/services/identity-design" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/online-supermarket.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/e-commerce" class="text-decoration-none">E-Commerce Solutions</a>
                </h4>
              </div>
              <p>
                We provide e-commerce design and development services, including online store setup, product listing, payment integration, and order management.
              </p>
              <a href="/services/e-commerce" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card p-3 service-card border-0">
              <div class="d-flex align-items-center mb-2">
                <img src="/public/assets/gifs/responsive-design.gif" alt="Web" style="height: 50px; width: 50px; object-fit: cover;" class="me-3">
                <h4 class="card-title mb-0">
                  <a href="/services/digital-marketing" class="text-decoration-none">Digital Marketing</a>
                </h4>
              </div>
              <p>
                We provide digital marketing services, including search engine optimization (SEO), social media marketing, content marketing, email marketing, etc. 
              </p>
              <a href="/services/digital-marketing" class="btn btn-primary mt-2">Discover More</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- portfolio -->
    <section id="portfolio">
      <div class="container">
        <Portfolio />
      </div>
    </section>

    <!-- about -->
    <section>
      <div class="container mt-4">
        <About />
      </div>
    </section>

    
    <!-- form -->
    <section class="py-5 text-center bg-dark text-white">
      <div class="container px-4">
        <div class="row">
          <div class="col-md-6">
            <h2 class="fw-bold text-left">Let's work together</h2>
            <p class="text-left">
              We're excited to hear about your vision and explore how we can bring it to life. Whether you're looking to build a modern website, create a strong visual identity, or launch a compelling marketing campaign — we're here to help.  
            </p>
            <p class="text-left">
              Fill out the form with your project details, goals, and any specific requirements you have in mind. One of our team members will review your message and get back to you promptly to discuss the next steps.  
            </p>
            <p class="text-left">
              Let's turn your ideas into powerful digital experiences — together.
            </p>
          </div>
          <div class="col-md-6">
            <form  @submit.prevent="submitForm" class="p-4 shadow rounded bg-white">
              <div class="mb-3">
                <input
                  type="text"
                  v-model="form.name"
                  class="form-control"
                  placeholder="Enter your name"
                  required
                />
              </div>

              <div class="mb-3">
                <input
                  type="email"
                  v-model="form.email"
                  class="form-control"
                  placeholder="Enter your email"
                  required
                />
              </div>
              <div class="mb-3">
              <input 
                  type="text" 
                  id="subject" 
                  v-model="form.subject" 
                  class="form-control"
                  placeholder="Subject"
                  required
                />
            </div>

              <div class="mb-3">
                <textarea
                  class="form-control"
                  v-model="form.message"
                  rows="4"
                  placeholder="Enter your message"
                  required
                ></textarea>
              </div>

              <div class="text-end">
                <button type="submit" class="btn btn-primary">
                  Send Message
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </Layout>
  <EstimateModal :isOpen="isModalOpen" @close="closeQuoteModal" />
</template>


<script setup>
import Layout from "../../Layouts/HomeLayout.vue";
import Portfolio from "@/components/Home/Portfolio.vue";
import About from "@/components/Home/About.vue";
import EstimateModal from "@/components/Home/EstimateModal.vue";
import { ref} from "vue";
import axios from "axios";

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
});

const isModalOpen = ref(false);

const submitForm = async () => {
  try {
    const response = await axios.post('/contact-form', form.value);
    console.log(response.data);

    // Reset form
    form.value = {
      name: '',
      email: '',
      subject: '',
      message: '',
    };
  } catch (error) {
    console.error('Form submission error:', error);
  }
}

const openQuoteModal = () => {
  isModalOpen.value = true;
};

const closeQuoteModal = () => {
  isModalOpen.value = false;
}
</script>


<style scoped>

.service-card:hover{
  box-shadow: 0 0 10px rgba(48, 48, 48, 0.3);
  transform: scale(1.05);
  transition: transform 0.3s ease;
}

</style>