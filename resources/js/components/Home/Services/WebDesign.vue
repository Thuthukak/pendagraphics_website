<template>
    <navbar />
    <section class="bg-white text-gray-800">
      <div class="container mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold mb-6 text-blue-600">Website Design</h1>
        <p class="text-lg mb-8 max-w-3xl">
          At Penda Graphics, our web design services combine creativity and functionality to give your brand an unforgettable digital presence...
        </p>
  
        <div class="grid md:grid-cols-2 gap-10">
          <div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">What We Offer</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Custom website design tailored to your brand</li>
              <li>Responsive, mobile-friendly layouts</li>
              <li>SEO-optimized structure</li>
              <li>Modern UI/UX design practices</li>
              <li>Fast loading times and performance optimization</li>
            </ul>
          </div>
          <div>
            <img src="#" alt="Web design" class="rounded-lg shadow-md" />
          </div>
        </div>
  
        <div class="mt-16">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900">Why Choose Us?</h2>
          <p class="mb-6 max-w-3xl">
            We don’t just build websites — we craft experiences that connect with your audience. Our design team ensures every site is visually compelling and technically sound.
          </p>
          <a href="#" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
            Get in Touch
          </a>
        </div>
      </div>
    </section>
  
    <Footer />
  </template>
  
  <script setup>
  import Navbar from "@/components/Home/Navbar.vue";
  import Footer from "@/components/Home/Footer.vue";


  </script>
  