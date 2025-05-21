<template>
  <Navbar />
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container text-center py-5">
      <h1 class="display-4 fw-bold mb-3">Crafting Digital Experiences</h1>
      <p class="lead fs-4 mb-4 mx-auto" style="max-width: 700px;">
        Where creativity meets strategy to transform your brand's digital presence
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="/#portfolio" class="btn btn-light align-self-center py-3">View Our Work</a>
        <a href="/contact-us" class="btn btn-outline-light">Get in Touch</a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h2 class="section-title fw-bold">Our Story</h2>
          <p>
            Founded with a passion for visual storytelling, Penda Graphics has evolved from a boutique design studio into a comprehensive digital solutions provider. Our journey began with creating impactful graphic designs—logos that capture brand essence, print materials that command attention, and packaging that sells products before they're even touched.
          </p>
          <p>
            Today, we've expanded our expertise to embrace the full digital ecosystem. We craft engaging websites, develop powerful web applications, and implement strategic digital marketing campaigns that drive meaningful results for businesses across industries.
          </p>
          <p>
            Through every transformation, our core mission remains unchanged: to help brands communicate their unique value through exceptional design and technical innovation.
          </p>
        </div>
        <div class="col-lg-6">
          <div class="position-relative">
            <img src="/public/assets/images/our_story.png" alt="Penda Graphics creative process" class="img-fluid rounded-4 shadow-md">
            <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle p-3" style="width: 80px; height: 80px;">
              <div class="text-center text-white">
                <small class="d-block">SINCE</small>
                <span class="fw-bold">2019</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Journey Section -->
  <section class="py-5 accent-bg">
    <div class="container">
      <h2 class="section-title fw-bold text-center mb-5">Our Evolution</h2>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="journey-line">
            <div class="journey-item">
              <h5 class="fw-bold">Design Foundation</h5>
              <p>
                Started as a specialized graphic design studio creating brand identities and print collateral for local businesses.
              </p>
            </div>
            <div class="journey-item">
              <h5 class="fw-bold">Digital Expansion</h5>
              <p>
                Extended services to include responsive website design and development, helping clients establish their digital presence.
              </p>
            </div>
            <div class="journey-item">
              <h5 class="fw-bold">Marketing Integration</h5>
              <p>
                Added digital marketing capabilities to help clients not just exist online, but thrive through strategic campaigns and content.
              </p>
            </div>
            <div class="journey-item">
              <h5 class="fw-bold">Technical Innovation</h5>
              <p>
                Mastered web application development, creating custom solutions that solve business challenges and enhance operations.
              </p>
            </div>
            <div class="journey-item">
              <h5 class="fw-bold">Today & Beyond</h5>
              <p>
                Operating as a full-service digital partner, blending creativity, strategy, and technology to deliver comprehensive solutions.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="section-title fw-bold text-center">What We Offer</h2>
      <p class="text-center mb-5" style="max-width: 700px; margin: 0 auto;">
        Our comprehensive suite of services is designed to elevate your brand at every digital touchpoint
      </p>
      
      <div class="row g-4">
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-pencil-ruler fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/identity-design">Brand & Visual Identity</a></h5>
            <p class="mb-0">
              Logo design, brand guidelines, stationery, marketing collateral, and comprehensive visual systems that tell your brand's story.
            </p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-laptop-code fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/web-design">Web Design & Development</a></h5>
            <p class="mb-0">
              Responsive websites with intuitive user experiences, optimized for performance across all devices and platforms.
            </p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-code fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/web-design">Web Applications</a></h5>
            <p class="mb-0">
              Custom web applications and software solutions that streamline operations, enhance customer experiences, and solve business challenges.
            </p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-shopping-cart fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/e-commerce">E-commerce Solutions</a></h5>
            <p class="mb-0">
              Online stores built for conversions, with secure payment gateways, inventory management, and seamless shopping experiences.
            </p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-bullhorn fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/digital-marketing">Digital Marketing</a></h5>
            <p class="mb-0">
              Strategic campaigns across search, social, and email channels to drive traffic, generate leads, and increase brand awareness.
            </p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="service-card h-100 p-4 text-center">
            <div class="service-icon" style="background-color: rgba(99, 102, 241, 0.1);">
              <i class="fas fa-box-open fa-2x" style="color: #000000;"></i>
            </div>
            <h5 class="fw-bold mb-3"><a href="/services/product-design">Print & Packaging</a></h5>
            <p class="mb-0">
              Eye-catching print materials and product packaging designed to stand out in physical spaces and on retail shelves.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <!-- <section class="py-5 accent-bg">
    <div class="container">
      <h2 class="section-title fw-bold text-center">Meet Our Experts</h2>
      <p class="text-center mb-5" style="max-width: 700px; margin: 0 auto;">
        The talented minds behind our creative solutions and technical innovations
      </p>
      
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="team-member mb-4">
            <img src="/public/assets/images/penda_logo2.png" alt="Lebo Mokoena" class="img-fluid w-100">
            <div class="team-info mx-3 text-center">
              <h5 class="fw-bold mb-1">Lebo Mokoena</h5>
              <p class="text-primary mb-2">Creative Director</p>
              <p class="small mb-0">Leading our creative vision with over a decade of brand design expertise.</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
          <div class="team-member mb-4">
            <img src="/public/assets/images/blue-question.webp" alt="Thabo Ndlovu" class="img-fluid w-100">
            <div class="team-info mx-3 text-center">
              <h5 class="fw-bold mb-1">Thabo Ndlovu</h5>
              <p class="text-primary mb-2">Technical Director</p>
              <p class="small mb-0">Architect of our web applications and technical solutions.</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
          <div class="team-member mb-4">
            <img src="/public/assets/images/blue-question.webp" alt="Naledi Dlamini" class="img-fluid w-100">
            <div class="team-info mx-3 text-center">
              <h5 class="fw-bold mb-1">Naledi Dlamini</h5>
              <p class="text-primary mb-2">Strategy Director</p>
              <p class="small mb-0">Developing targeted strategies that align brands with business objectives.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- Approach Section -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
          <img src="/public/assets/images/approach.png" alt="Our collaborative process" class="img-fluid ">
        </div>
        <div class="col-lg-6 order-lg-1">
          <h2 class="section-title fw-bold">Our Approach</h2>
          <div class="mb-4">
            <h5 class="fw-bold"><i class="fas fa-search text-primary me-2"></i> Discover</h5>
            <p>We begin by understanding your business goals, audience needs, and competitive landscape to establish a clear direction.</p>
          </div>
          <div class="mb-4">
            <h5 class="fw-bold"><i class="fas fa-lightbulb text-primary me-2"></i> Create</h5>
            <p>Our team develops concepts and solutions that align with your objectives, iterating based on collaboration and feedback.</p>
          </div>
          <div class="mb-4">
            <h5 class="fw-bold"><i class="fas fa-rocket text-primary me-2"></i> Deliver</h5>
            <p>We implement polished solutions with precision, ensuring they work flawlessly across all intended channels and platforms.</p>
          </div>
          <div>
            <h5 class="fw-bold"><i class="fas fa-chart-line text-primary me-2"></i> Optimize</h5>
            <p>Through continuous analysis and refinement, we ensure your digital assets and campaigns perform at their best over time.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-5">
    <div class="container">
      <div class="cta-section p-5">
        <div class="row align-items-center">
          <div class="col-lg-8 mb-4 mb-lg-0">
            <h2 class="fw-bold">Ready to Transform Your Digital Presence?</h2>
            <p class="lead mb-0">Let's create experiences that engage your audience and drive business growth.</p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="/contact-us" class="btn btn-light btn-lg">Get Started</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h5 class="fw-bold mb-3">Penda Graphics</h5>
          <p>Transforming brands through design and digital innovation since 2019.</p>
          <div class="d-flex gap-3 mt-4">
            <a href="https://web.facebook.com/Penda.graphix" class="text-white"><i class="fab fa-facebook-f"></i></a>
            <!-- <a href="#" class="text-white"><i class="fab fa-twitter"></i></a> -->
            <a href="https://www.instagram.com/penda_graphics/" class="text-white"><i class="fab fa-instagram"></i></a>
            <!-- <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a> -->
          </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h6 class="fw-bold mb-3">Services</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="/services/web-design" class="text-white-50 text-decoration-none">Web Design</a></li>
            <li class="mb-2"><a href="/services/graphic-design" class="text-white-50 text-decoration-none">Graphic Design</a></li>
            <li class="mb-2"><a href="/services/product-design" class="text-white-50 text-decoration-none">Product Design</a></li>
            <li class="mb-2"><a href="/services/digital-marketing" class="text-white-50 text-decoration-none">Digital Marketing</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h6 class="fw-bold mb-3">Company</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">About</a></li>
            <li class="mb-2"><a href="/#portfolio" class="text-white-50 text-decoration-none">Portfolio</a></li>
            <!-- <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Blog</a></li>
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Careers</a></li> -->
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <h6 class="fw-bold mb-3">Contact</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Birch Acres, Kempton Park</li>
            <li class="mb-2"><a href="tel:+27738114652"><i class="fas fa-phone me-2 text-primary"></i><span class="text-white">+27 73 811 4652</span></a></li>
            <li class="mb-2"><a href="mailto:info@pendagraphics.co.za"><i class="fas fa-envelope me-2 text-primary"></i><span class="text-white">info@pendagraphics.co.za</span></a></li>
          </ul>
        </div>
      </div>
      <hr class="my-4 bg-white-50">
      <div class="row">
        <div class="col-md-12 text-center">
          <p class="small text-white-50 mb-0">&copy; {{ currentYear }} Penda Graphics. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import Navbar from '@/components/Home/Navbar.vue';
export default {
  components: { Navbar },
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};
</script>

<style scoped>
:root {
  --primary: #005e91;
  --secondary: #4f46e5;
  --accent: #e71c25;
  --light: #f9fafb;
  --dark: #111827;
}
a{
  text-decoration: none !important;

}
body {
  font-family: 'Inter', sans-serif;
  color: #111827;
  background-color: #f9fafb;
  line-height: 1.7;
}

.navbar {
  padding: 1rem 0;
  background-color: rgba(255, 255, 255, 0.95);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.hero-section {
  background: linear-gradient(135deg, #005e91 0%, #008fdc 100%);
  color: white;
  padding: 6rem 0;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.service-card {
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  border: none;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.service-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.service-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.team-member {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
}

.team-member img {
  transition: all 0.5s ease;
}

.team-member:hover img {
  transform: scale(1.05);
}

.team-info {
  background: rgba(255, 255, 255, 0.95);
  padding: 1rem;
  border-radius: 8px;
  position: relative;
  margin-top: -40px;
  z-index: 2;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.section-title {
  position: relative;
  display: inline-block;
  margin-bottom: 2rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  width: 40px;
  height: 3px;
  background-color: #e71c25;
}

.journey-line {
  position: relative;
}

.journey-line::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 20px;
  width: 2px;
  background-color: #005e91;
  opacity: 0.3;
}

.journey-item {
  position: relative;
  padding-left: 50px;
  margin-bottom: 2rem;
}

.journey-item::before {
  content: '';
  position: absolute;
  left: 14px;
  top: 8px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background-color: #005e91;
}

.accent-bg {
  background-color: rgba(99, 102, 241, 0.05);
}

.cta-section {
  background: linear-gradient(135deg, #005e91 0%, #008fdc 100%);
  color: white;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
}

.cta-section::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.btn-primary {
  background-color: #6366f1;
  border-color: #6366f1;
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: #4f46e5;
  border-color: #4f46e5;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-outline-light {
  border-width: 2px;
  font-weight: 600;
  padding: 0.75rem 2rem;
  border-radius: 8px;
}

footer {
  background-color: #111827;
  color: #f9fafb;
}
</style>
