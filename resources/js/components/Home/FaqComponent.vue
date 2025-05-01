<template>
    <section class="p-4">
      <div class="mb-6">
        <div class="flex flex-wrap gap-3 justify-center">
          <button
            v-for="cat in categories"
            :key="cat"
            @click="selectedCategory = cat"
            :class="['px-4 py-2 rounded text-white', selectedCategory === cat ? 'bg-blue-600' : 'bg-gray-500']"
          >
            {{ cat }}
          </button>
        </div>
      </div>
  
      <div class="max-w-4xl mx-auto mt-5">
        <div v-for="(item, index) in filteredFaqs" :key="index" class="mb-4 border-b pb-2">
          <button
            class="w-full text-left font-semibold text-lg flex justify-between items-center"
            @click="toggle(index)"
          >
            {{ item.question }}
            <span>{{ activeIndex === index ? '-' : '+' }}</span>
          </button>
          <transition name="fade">
            <p v-if="activeIndex === index" class="mt-2 text-gray-700">
              {{ item.answer }}
            </p>
          </transition>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  
  const categories = [
    'Web Design',
    'Graphic Design',
    'Identity Design',
    'Product Design',
    'E-Commerce Solutions',
    'Digital Marketing',
  ]
  
  const selectedCategory = ref('Web Design')
  const activeIndex = ref(null)
  
  const faqs = ref([
    //web design
    {
      category: 'Web Design',
      question: 'Do you build responsive websites?',
      answer: 'Yes, all our websites are mobile-friendly and fully responsive across devices.',
    },
    {
      category: 'Web Design',
      question: 'Can you redesign my current website?',
      answer: 'Absolutely. We modernize existing websites while preserving your brand identity.',
    },
    {
      category: 'Web Design',
      question: 'What is responsive web design?',
      answer: 'Responsive web design is an approach to designing websites that ensures they adapt and display properly across different devices and screen sizes, including desktops, laptops, tablets, and smartphones.',
    },
    {
      category: 'Web Design',
      question: 'How much does web design cost?',
      answer: 'The cost of web design can vary widely depending on factors such as the complexity of the project, the level of customization required, the experience of the design team, and additional services such as SEO or content creation. It’s best to discuss your specific needs and budget with a web design agency to get an accurate quote.',
    },
    {
      category: 'Web Design',
      question: 'Should I pay up front?',
      answer: 'Yes, you pay a 50% deposit upfront. The remaining balance is due when the website is completed.',
    },
    //product design
    {
      category: 'Product Design',
      question: 'Do you offer product design services?',
      answer: 'Yes, we specialize in creating visually appealing and functional product designs that align with your brand identity.',
    },
    {
      category: 'Product Design',
      question: 'What is product design?',
      answer: 'Product design is the process of creating new products or improving existing ones to meet the needs of users and solve specific problems. It involves a combination of creativity, engineering, and user-centered design principles to develop products that are functional, aesthetically pleasing, and marketable.',
    },
    {
      category: 'Product Design',
      question: 'What types of products do you design?',
      answer: 'We design everything from packaging to mockups, ensuring your product stands out on the shelf and in the digital space.',
    },
    {
      category: 'Product Design',
      question: 'Why is product design important?',
      answer: 'Product design is important because it directly impacts the success and viability of a product in the market. A well-designed product not only meets user needs but also differentiates itself from competitors, enhances brand reputation, and drives sales and customer satisfaction.',
    },
    {
      category: 'Product Design',
      question: 'Is there a minimum order quantity?',
      answer: 'No, there is no minimum order quantity for product design services.',
    },
    {
      category: 'Product Design',
      question: 'How much does product design cost?',
      answer: 'Depending on the complexity of the project, the cost of product design can vary. It’s best to contact us and discuss your specific needs and budget.',
    },

    //graphic design
    {
      category: 'Graphic Design',
      question: 'What types of designs do you offer?',
      answer: 'We offer everything from logos and flyers to packaging and promotional materials.',
    },
    {
      category: 'Graphic Design',
      question: 'Are you able to create custom designs?',
      answer: 'Yes, we can create custom designs that align with your brand identity.',
    },
    {
      category: 'Graphic Design',
      question: 'What file formats will I receive for my graphic design project?',
      answer: 'Upon completion of the project, you will typically receive the final designs in various file formats suitable for both print and digital use. Common file formats include vector formats such as .ai or .eps for logos and illustrations, and raster formats such as .jpg or .png for web graphics.',
    },
    {
      category: 'Graphic Design',
      question: 'How much does graphic design services cost?',
      answer: 'The cost of graphic design services can vary depending on factors such as the complexity of the project, the level of customization required. Request a quotation to get an accurate estimate.',
    },
    {
      category: 'Graphic Design',
      question: 'Can I request revisions to the designs?',
      answer: 'Yes, we offer revisions to the designs based on your feedback. It’s important to communicate your feedback clearly and promptly to ensure that the designs meet your expectations.',
    },

    //e-commerce
    {
      category: 'E-Commerce Solutions',
      question: 'Do you offer payment gateway integration?',
      answer: 'Yes, we integrate with major gateways like PayFast, PayPal, and Stripe.',
    },
    {
      category: 'E-Commerce Solutions',
      question: 'What features should I look for in an e-commerce solution?',
      answer: 'When choosing an e-commerce solution, it’s important to consider features such as customizable website design, user-friendly navigation, mobile responsiveness, secure payment processing, inventory management, order tracking, and integration with other business systems such as CRM and accounting software.',
    },
    {
      category: 'E-Commerce Solutions',
      question: 'How can I ensure the security of my customers payment information?',
      answer: ' Our e-commerce solutions offer secure payment gateways with features such as SSL encryption, PCI compliance, and fraud detection measures. Additionally, we regularly update your website’s security protocols and educate your customers about safe online shopping.',
    },
    {
      category: 'E-Commerce Solutions',
      question: 'Do I get ongoing support and maintenance for my e-commerce website?',
      answer: 'Yes, Ongoing support and maintenance for an e-commerce website may include updating product listings, managing inventory, processing orders, monitoring website performance, addressing technical issues, and implementing updates and security patches. ',
    },
    {
      category: 'E-Commerce Solutions',
      question: 'How do I measure the success of my e-commerce website?',
      answer: 'The success of an e-commerce website can be measured using key performance indicators (KPIs) such as website traffic, conversion rate, average order value, customer acquisition cost, customer lifetime value, and return on investment (ROI). By tracking these metrics, you can assess the effectiveness of your e-commerce efforts and make data-driven decisions to optimize performance and drive growth.',
    },
    

    //digital marketing
    {
      category: 'Digital Marketing',
      question: 'Do you manage social media accounts?',
      answer: 'Yes, we can manage and grow your social media presence across multiple platforms.',
    },
    {
      category: 'Digital Marketing',
      question: 'What types of digital marketing services do you offer?',
      answer: 'We offer a wide range of digital marketing services, including search engine optimization (SEO), social media marketing, content marketing, email marketing, etc.',
    },
    {
      category: 'Digital Marketing',
      question: 'How do I ensure the success of my digital marketing campaign?',
      answer: 'To ensure the success of your digital marketing campaign, it’s important to set clear goals, create a strategic plan, and monitor and optimize your campaign performance regularly. ',
    },
    {
      category: 'Digital Marketing',
      question: 'Are you able to create custom landing pages?',
      answer: 'Yes, we can create custom landing pages that align with your brand identity and meet your specific marketing goals.',
    },
    {
      category: 'Digital Marketing',
      question: 'What is the difference between organic and paid traffic?',
      answer: 'Organic traffic refers to visitors who find your website through unpaid search engine results, while paid traffic refers to visitors who click on paid advertisements (such as Google Ads or social media ads) to reach your website. Both types of traffic are important for driving website visitors and achieving marketing goals, but they require different strategies and approaches.',
    },
    //identity design
    {
      category: 'Identity Design',
      question: 'What types of identity design services do you offer?',
      answer: 'We offer everything from logo design to brand guidelines.',
    },
    {
      category: 'Identity Design',
      question: 'Are you able to create custom identity designs?',
      answer: 'Yes, we can create custom identity designs that align with your brand identity.',
    },
    {
      category: 'Identity Design',
      question: 'What file formats will I receive for my identity design project?',
      answer: 'We provide vector files in SVG, EPS, and AI formats.',
    },
    {
      category: 'Identity Design',
      question: 'What is the cost of identity design services?',
      answer: 'The cost of identity design services depends on the complexity of the design project and the number of files you need.',
    },
    {
      category: 'Identity Design',
      question: 'What services are included in identity design?',
      answer: 'Identity design services typically include logo design, color palette development, typography selection, brand guidelines creation, and collateral design such as business cards, letterheads, and packaging. We also offer additional services such as brand strategy development and brand naming.',
    },
    {
      category: 'Identity Design',
      question: 'How will I receive the final identity design assets?',
      answer: 'Once the identity design is finalized, we’ll provide you with a comprehensive brand identity package that includes all necessary assets such as logo files in various formats, color codes, typography guidelines, and brand usage guidelines. These assets will be delivered to you digitally for easy access and implementation.',
    },
    {
      category: 'Identity Design',
      question: 'What services are included in identity design?',
      answer: 'Identity design services typically include logo design, color palette development, typography selection, brand guidelines creation, and collateral design such as business cards, letterheads, and packaging. We also offer additional services such as brand strategy development and brand naming.',
    },
    {
      category: 'Identity Design',
      question: "What if I'm not satisfied with the initial design concepts?",
      answer: 'Client satisfaction is our top priority, and we welcome feedback throughout the design process. If you’re not satisfied with the initial design concepts, we’ll work closely with you to understand your concerns and make revisions until we achieve a design that meets your expectations.',
    },
  ])
  
  const filteredFaqs = computed(() =>
    faqs.value.filter((faq) => faq.category === selectedCategory.value)
  )
  
  function toggle(index) {
    activeIndex.value = activeIndex.value === index ? null : index
  }
  </script>
  
  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>
  