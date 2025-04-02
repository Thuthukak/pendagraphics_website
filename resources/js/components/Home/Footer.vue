<template>
  <footer class="bg-shape-primary text-white py-5">
    <div class="container">
      
      <!-- Row for Layout -->
      <div class="row">

        <!-- Column 1: Logo & Slogan -->
        <div class="col-md-4 mb-4 mb-md-0">
          <img src="#" alt="Logo" class="w-50 mb-3" />
          <p class="text-white">Best Grooming Services</p>
        </div>

        <!-- Column 2: Contact Info -->
        <div class="col-md-4 mb-4 mb-md-0">
          <h5 class="mb-3">Contact Us</h5>
          <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-2">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <font-awesome-icon :icon="['fas', 'map-marker-alt']" />
              </div>
              <span>123 Street, City, Country</span>
            </li>
            <li class="d-flex align-items-center mb-2">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <font-awesome-icon :icon="['fas', 'phone']" />
              </div>
              <span>+123 456 7890</span>
            </li>
            <li class="d-flex align-items-center">
              <div class="text-white p-2 d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <font-awesome-icon :icon="['fas', 'envelope']" />
              </div>
              <span>contact@example.com</span>
            </li>
          </ul>
        </div>

        <!-- Column 3: Social Media -->
        <div class="col-md-4">
          <h5 class="mb-3">Find us on social media</h5>
          <div class="d-flex gap-2">
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <font-awesome-icon :icon="['fab', 'facebook-f']" />
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <font-awesome-icon :icon="['fab', 'twitter']" />
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <font-awesome-icon :icon="['fab', 'linkedin-in']" />
            </a>
            <a href="#" class="text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <font-awesome-icon :icon="['fab', 'youtube']" />
            </a>
          </div>
        </div>

      </div>

    </div>
  </footer>
  <!-- Bottom Bar -->
  <div class="bg-white text-black text-center py-3 "><hr>
    <p class="mb-0">Penda Graphics &copy; {{ currentYear }}. All rights reserved.</p>
    </div>
</template>

<script>
export default {
  name: "Footer",
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};

</script>

<style scoped>
.bg-shape-primary {
  background-color: #383838 !important;
}
.foot{
  background-color: #FD7E14 !important;
}
</style>
