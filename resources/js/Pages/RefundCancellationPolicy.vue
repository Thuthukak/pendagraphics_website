<template>
  <div class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-4xl mx-auto bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
      
      <!-- Header -->
      <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-8 text-white">
        <h1 class="text-3xl font-bold tracking-tight">Refund &amp; Cancellation Policy</h1>
        <p class="text-slate-400 mt-2 text-sm">Last Updated: June 2026 | Penda Graphics</p>
      </div>

      <!-- Content -->
      <div class="p-8 space-y-8 text-gray-700 leading-relaxed">
        
        <section>
          <p class="text-base">
            At <strong>Penda Graphics</strong>, we strive to deliver exceptional web design, development, branding, and digital asset solutions. Because our services involve highly customized digital labor and creative assets, our refund and cancellation terms are structured to reflect the progression of project milestones.
          </p>
        </section>

        <hr class="border-gray-100" />

        <!-- Policy Sections -->
        <div class="space-y-6">
          <section v-for="(policy, index) in policies" :key="index" class="flex items-start space-x-4">
            <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-800 font-semibold text-sm">
              {{ index + 1 }}
            </span>
            <div>
              <h2 class="text-lg font-semibold text-gray-900 mb-1">{{ policy.title }}</h2>
              <p class="text-gray-600 text-sm sm:text-base">{{ policy.content }}</p>
            </div>
          </section>
        </div>

        <hr class="border-gray-100" />

        <!-- Contact / Action Section -->
        <section class="bg-slate-50 rounded-lg p-6 border border-slate-100">
          <h3 class="text-md font-semibold text-gray-900 mb-2">How to Request a Cancellation or Dispute</h3>
          <p class="text-sm text-gray-600 mb-4">
            If you need to cancel an active project request or discuss a billing issue, please reach out to our administration team in writing. We process all valid cancellation claims within 7 business days.
          </p>
          <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-6 text-sm">
            <div>
              <span class="font-medium text-gray-900">Email:</span> 
              <a href="mailto:info@pendagraphics.co.za" class="text-blue-600 hover:underline ml-1">info@pendagraphics.co.za</a>
            </div>
            <div>
              <span class="font-medium text-gray-900">Phone:</span> 
              <span class="text-gray-600 ml-1">+27 73 811 4652</span>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const policies = ref([
  {
    title: 'Initial Project Cancellations',
    content: 'Clients may cancel a project request within 48 hours of making an initial booking deposit for a full refund, provided that no official onboarding, strategy discovery sessions, or creative work has officially commenced.'
  },
  {
    title: 'Web Design & Development Work',
    content: 'Once design concepts or development sprints have initiated, project deposits become non-refundable to compensate for reserved resources and professional hours logged. If a project is terminated mid-cycle by the client, Penda Graphics reserves the right to bill for the exact percentage of milestones completed up to that date.'
  },
  {
    title: 'Graphic Design & Corporate Branding Assets',
    content: 'Due to the explicit nature of digital design assets (logos, flyers, vectors, guidelines), refunds are completely restricted once initial mockups, drafts, or layout options have been delivered to the client for evaluation.'
  },
  {
    title: 'Finalized & Delivered Solutions',
    content: 'Once a digital product, website platform, or brand asset package is formally approved, signed off, and delivered or deployed live to the client environment, all sales are considered final. No refunds will be issued for completed configurations.'
  }
])
</script>