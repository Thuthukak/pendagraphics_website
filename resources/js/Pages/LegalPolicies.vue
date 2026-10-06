<template>
  <div class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased text-slate-800">
    <div>
        <img src="/assets/images/penda_logo2.png" alt="Penda Graphics Logo" class="mx-auto w-32 h-auto mb-6" />
    </div>
    <div class="max-w-4xl mx-auto bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-200">
      
      <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8 text-white border-b border-slate-700">
        <div class="flex items-center space-x-3 mb-2">
          <span class="text-xs font-bold tracking-widest uppercase bg-blue-600 px-2.5 py-1 rounded">Compliance Matrix</span>
          <span class="text-xs text-slate-400 font-mono">v1.2 (2026)</span>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Legal Center &amp; Policies</h1>
        <p class="text-slate-400 mt-1 text-sm sm:text-base">Penda Graphics — Creative Web Design, Branding &amp; Digital Solutions</p>
      </div>

      <div class="bg-slate-100 p-2 flex border-b border-slate-200 overflow-x-auto space-x-1 scrollbar-none">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex-1 text-center py-3 px-4 rounded-xl font-medium text-xs sm:text-sm transition-all whitespace-nowrap',
            activeTab === tab.id 
              ? 'bg-white text-slate-900 shadow-sm border border-slate-200/60' 
              : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-900'
          ]"
        >
          {{ tab.name }}
        </button>
      </div>

      <div class="p-6 sm:p-10 space-y-8 leading-relaxed">
        
        <div v-if="activeTab === 'refunds'" class="space-y-6 animate-fadeIn">
          <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Refund &amp; Cancellation Policy</h2>
            <p class="text-xs text-slate-500 mt-0.5">Last updated: June 2026</p>
          </div>
          <p class="text-slate-600">
            At <strong>Penda Graphics</strong>, we deliver bespoke, highly customized digital solutions. Because our services involve intensive creative labor, code configuration, and tailored design frameworks, our refund patterns depend heavily on your current project milestone.
          </p>

          <div class="space-y-6 mt-4 border-l-2 border-slate-200 pl-4 sm:pl-6">
            <div v-for="(item, index) in refundPolicies" :key="index">
              <h3 class="text-base font-semibold text-slate-900 flex items-center">
                <span class="text-blue-600 mr-2 font-mono text-sm">1.{{ index + 1 }}</span> {{ item.title }}
              </h3>
              <p class="text-slate-600 text-sm mt-1.5 text-justify">{{ item.content }}</p>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'terms'" class="space-y-6 animate-fadeIn">
          <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Terms of Service</h2>
            <p class="text-xs text-slate-500 mt-0.5">Last updated: June 2026</p>
          </div>
          <p class="text-slate-600">
            By utilizing the Penda Graphics marketplace, ordering design sprints, or initializing staging development environments, you formally consent to the terms outline below:
          </p>

          <div class="space-y-6 mt-4 border-l-2 border-slate-200 pl-4 sm:pl-6">
            <div v-for="(item, index) in termsPolicies" :key="index">
              <h3 class="text-base font-semibold text-slate-900 flex items-center">
                <span class="text-slate-500 mr-2 font-mono text-sm">2.{{ index + 1 }}</span> {{ item.title }}
              </h3>
              <p class="text-slate-600 text-sm mt-1.5 text-justify">{{ item.content }}</p>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'privacy'" class="space-y-6 animate-fadeIn">
          <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Privacy Policy (POPIA Compliant)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Last updated: June 2026</p>
          </div>
          <p class="text-slate-600">
            Penda Graphics strictly operates under the compliance framework of the South African <strong>Protection of Personal Information Act (POPIA)</strong>, Act No. 4 of 2013.
          </p>

          <div class="space-y-6 mt-4 border-l-2 border-slate-200 pl-4 sm:pl-6">
            <div v-for="(item, index) in privacyPolicies" :key="index">
              <h3 class="text-base font-semibold text-slate-900 flex items-center">
                <span class="text-emerald-600 mr-2 font-mono text-sm">3.{{ index + 1 }}</span> {{ item.title }}
              </h3>
              <p class="text-slate-600 text-sm mt-1.5 text-justify">{{ item.content }}</p>
            </div>
          </div>
        </div>

        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200/80 mt-8">
          <div class="flex items-center space-x-2 text-slate-900 mb-2">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <h4 class="text-sm font-bold uppercase tracking-wider">Compliance Registry Contact</h4>
          </div>
          <p class="text-xs text-slate-600 mb-4">
            For operational cancellations, structured POPIA information record retrieval, or billing clarity regarding credit card workflows via Paystack, connect with our administration office.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
            <div class="bg-white p-2.5 rounded border border-slate-200/60">
              <span class="block text-slate-400 font-medium">Support & Legal Mail:</span>
              <a href="mailto:info@pendagraphics.co.za" class="text-blue-600 font-semibold hover:underline">info@pendagraphics.co.za</a>
            </div>
            <div class="bg-white p-2.5 rounded border border-slate-200/60">
              <span class="block text-slate-400 font-medium">Registered Phone Line:</span>
              <span class="text-slate-800 font-semibold">+27 73 811 4652</span>
            </div>
            <div class="bg-white p-2.5 rounded border border-slate-200/60 md:col-span-2">
              <span class="block text-slate-400 font-medium">Operational Location:</span>
              <span class="text-slate-800 font-semibold">Kempton Park, Johannesburg, South Africa</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const activeTab = ref('refunds')

const tabs = [
  { id: 'refunds', name: 'Refunds & Cancellations' },
  { id: 'terms', name: 'Terms of Service' },
  { id: 'privacy', name: 'Privacy Policy (POPIA)' }
]

const refundPolicies = [
  {
    title: 'Initial Booking Cancellations',
    content: 'Clients are entitled to cancel a pending design project assignment within forty-eight (48) hours of completing their initial booking deposit for a full 100% refund, on the explicit condition that no creative onboarding, strategy discovery, brand alignment matrix layout, or visual design wireframes have been officially executed.'
  },
  {
    title: 'Work In Progress Milestones (Web & Development)',
    content: 'Once creative digital design frameworks, system setups, layout compositions, or application coding layers have commenced, project onboarding fees or milestone deposits become entirely non-refundable. If an assignment is halted mid-cycle by a client, Penda Graphics retains all upfront payments and holds standard contractual authority to bill for the exact volume of development hours completed.'
  },
  {
    title: 'Graphic Design & Graphic Asset Logistics',
    content: 'Given the immediate, dynamic formulation of raw digital layout concepts (flyers, banners, corporate stationery guidelines, illustrations, digital packages), refunds are entirely restricted from the exact moment initial design drafts, concept maps, or layouts are pushed to the client for structural evaluation.'
  },
  {
    title: 'Final Approvals & Deployments',
    content: 'Once a web system, database configuration, branding file, or e-commerce storefront is officially approved, signed off, and transferred to live production servers or digital environments, all payment streams are considered final. Penda Graphics does not permit any financial refunding, chargebacks, or billing adjustments on fully signed-off digital structures.'
  }
]

const termsPolicies = [
  {
    title: 'Scope & Project Adjustments',
    content: 'Penda Graphics executes creative services in strict accordance with individual project outlines or quotes. Standard assignments incorporate explicit design configuration loops. Any adjustments, functional expansions, or system adjustments request post-signoff will attract extra billing at our baseline hourly digital development rate.'
  },
  {
    title: 'Client Content Requirements',
    content: 'Clients assume full legal responsibility for providing all requisite text blocks, brand logos, high-resolution media imagery, and strategic business data required for project operations. Penda Graphics holds zero liability for delays encountered due to missing asset packages.'
  },
  {
    title: 'Intellectual Property Transmission',
    content: 'All frontend layouts, software architecture structures, database engineering parameters, vector designs, and custom files generated during production remain under the explicit lock and copyright of Penda Graphics until all outstanding invoices are fully cleared. Upon successful completion of full account balances, ownership of specific visuals and code properties is transferred to the client.'
  }
]

const privacyPolicies = [
  {
    title: 'Information Matrix Gathered',
    content: 'When initializing transaction forms or checking options on our domain, we capture name parameters, company details, telephone phone contacts, valid enterprise email pipelines, and contextual operational data required to process payment entries or secure quotes.'
  },
  {
    title: 'The Purpose of Your Private Records',
    content: 'Records are analyzed strictly to finalize your transaction protocols via encrypted channels, build out custom service parameters, optimize framework responsiveness, and fulfill South African statutory bookkeeping or general accounting provisions.'
  },
  {
    title: 'Secured Payment Workflows via Paystack',
    content: 'All online monetary clearings are channeled securely via Paystack infrastructure using industry-grade PCI-DSS tokenization algorithms. Penda Graphics does not process, interact with, or store raw credit card numbers or individual card parameters on its local hosting environments.'
  }
]
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(3px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}
/* Utility helper to clear default horizontal scrollbar styling on mobile webkit engines */
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>