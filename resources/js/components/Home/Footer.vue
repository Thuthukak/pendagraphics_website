<template>
  <footer class="bg-shape-primary text-white py-5">
    <div class="container px-4">
      
      <!-- Row for Layout -->
      <div class="row">

        <!-- Column 1: Logo & Slogan -->
        <div class="col-md-4 mb-4 mb-md-0">
          <img src="/public/assets/images/penda_logo2.png" alt="Logo" class="w-60 mb-3" />
          <p class="text-white">Empowering Your Brand with Stunning Designs & Websites</p>
        </div>

        <!-- Column 2: Contact Info -->
        <div class="col-md-4 mb-4 mb-md-0">
          <h5 class="mb-3">Contact Us</h5>
          <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-2">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <span>Kempton Park, Johannesburg</span>
            </li>
            <li class="d-flex align-items-center mb-2">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <i class="fas fa-phone"></i>
              </div>
              <span>+27 73 811 4652</span>
            </li>
            <li class="d-flex align-items-center">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <i class="fas fa-envelope"></i>
              </div>
              <span>info@pendagraphics.co.za</span>
            </li>
          </ul>
        </div>

        <!-- Column 3: Social Media -->
        <div class="col-md-4">
          <h5 class="mb-3">Find us on social media</h5>
          <div class="d-flex gap-2">
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="fab fa-linkedin"></i>
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="fab fa-youtube"></i>
            </a>
          </div>
          <div class="mt-3"> Subscribe to our newsletter</div>
          <form @submit.prevent="subscribe">
            <div class="input-group mb-3 mt-2">
              <input 
                type="email" 
                v-model="form.email"
                class="form-control" 
                placeholder="Enter your email">
              <button class="btn btn-primary" type="submit">Subscribe</button>
            </div>
          </form>
        </div>

      </div>

    </div>
  </footer>
  <!-- Bottom Bar -->
  <div class="bg-white text-black text-center py-3 ">
    <p class="mb-0">Penda Graphics &copy; {{ currentYear }}. All rights reserved.</p>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
  email: '',
});

const subscribe = async () => {
  try {
    const response = await axios.post('/newsletter', form.value);
    console.log(response.data);

    // Reset form
    form.value = {
      email: '',
    };
  } catch (error) {
    console.error('Form submission error:', error);
  }
}
const currentYear = ref(new Date().getFullYear());
</script>

<style scoped>
.bg-shape-primary {
  background-color: #383838 !important;
}
.foot{
  background-color: #FD7E14 !important;
}
</style>
