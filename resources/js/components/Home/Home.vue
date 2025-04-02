<template>
  <Layout>
    <!-- hero section -->
    <section class="about py-5 bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="content">
              <h2 class="fw-bold">Empowering Your Brand with Stunning Designs & Websites</h2>
              <p class="mt-4 text-secondary">
                We are a team of passionate designers and developers dedicated to creating exceptional digital experiences for our clients.
              </p>
              <button class="btn btn-primary rounded">Get A Quote</button>   
            </div>
          </div>
          <div class="col-md-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mt-5">
                    <div class="card-body">
                      <h5 class="card-title">Card title</h5>
                      <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Card title</h5>
                          <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 mt-4">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Card title</h5>
                          <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
          </div>
        </div>
      </div>
    </section>


    <!-- <section class="hero text-white py-5 position-relative" style="background-color: #f8f9fa;">
      <div class="container text-center">
        <h1 class="fw-bold text-dark">Discover the World's <span class="text-primary">Hidden</span> Wonders</h1>
        <p class="lead text-muted">Find the unique moments and hidden gems that ignite unforgettable experiences. From raw encounters to remarkable destinations, we help you uncover the spark that turns every trip into a cherished year.</p>
        <button class="btn btn-dark fw-bold mt-3">Plan Your Trip</button>
      </div>
      <div class="position-absolute top-0 start-50 translate-middle-x w-100 h-100" style="background: url('https://via.placeholder.com/800x400') center/cover no-repeat; opacity: 0.1;"></div>
    </section> -->


    <!-- Hero Section -->
    <!-- <section class="hero text-center text-white py-5" style="background: linear-gradient(to right, #007ec7, #000000);">
      <div class="container">
        <h1 class="fw-bold">Discover the World's Hidden Wonders</h1>
        <p class="lead">Find the unique moments and hidden gems that ignite unforgettable experiences.</p>
        <button class="btn btn-warning text-black fw-bold mt-3">Plan Your Trip</button>
      </div>
    </section> -->
    
    <!-- Top Destinations -->
    <section class="py-5">
      <div class="container">
        <h2 class="fw-bold">Top Destinations</h2>
        <div class="row">
          <div class="col-md-3 col-6 mb-4" v-for="n in 4" :key="n">
            <div class="card">
              <div class="card-img-top"></div>
              <div class="card-body text-center">
                <h5 class="card-title">Destination Name</h5>
                <p class="text-muted">Country</p>
              </div>
            </div>
          </div>
        </div>
        <button class="btn btn-outline-dark">Explore all destinations</button>
      </div>
    </section>

    <!-- Latest Stories -->
    <section class="py-5 bg-light">
      <div class="container">
        <h2 class="fw-bold">Latest Stories</h2>
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-img-top"></div>
              <div class="card-body">
                <h5 class="card-title">Story Title</h5>
                <p class="text-muted">Category | Date</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-6 mb-3" v-for="n in 3" :key="n">
                <div class="card">
                  <div class="card-img-top"></div>
                  <div class="card-body">
                    <h6 class="card-title">Story Title</h6>
                    <p class="text-muted small">Category | Date</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button class="btn btn-outline-dark mt-3">Read more articles</button>
      </div>
    </section>
    
    <!-- Highlights -->
    <section class="py-5">
      <div class="container">
        <h2 class="fw-bold">Trekker’s Highlights</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Person Name</h5>
                <p class="small text-muted">Review</p>
                <p>Testimonial text...</p>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-6 mb-4" v-for="n in 2" :key="n">
                <div class="card">
                  <div class="card-img-top"></div>
                  <div class="card-body">
                    <h6 class="card-title">Experience Title</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Newsletter -->
    <section class="py-5 text-center bg-dark text-white">
      <div class="container">
        <h2>Get Your Travel Inspiration Straight to Your Inbox</h2>
        <div class="mt-4">
          <input type="email" class="form-control w-50 mx-auto d-inline" placeholder="Enter your email">
          <button class="btn btn-primary">Subscribe</button>
        </div>
      </div>
    </section>
    
    <!-- Footer -->
    <footer class="py-4 bg-black text-white">
      <div class="container text-center">
        <p>© 2024 Your Company. All rights reserved.</p>
      </div>
    </footer>
  </Layout>
</template>

<script>
import Layout from "../../Layouts/HomeLayout.vue";
export default {
  components: {
    Layout,
  },
};
</script>

<style scoped>
.card-img-top {
  height: 150px;
  background: url('https://via.placeholder.com/300') center/cover no-repeat;
}

.hero-image {
  background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
              url("/public/assets/images/hero.jpg") center/cover no-repeat;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  animation: zoomEffect 20s infinite alternate;
}

.overlay-content {
  width: 90%;
  max-width: 800px;
  position: relative;
  z-index: 2;
}
</style>
