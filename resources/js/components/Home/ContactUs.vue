<template>
    <Navbar />
    <div class="min-h-screen bg-gray-50">
      <!-- Header Section -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-16">
        <div class="container mx-auto px-4">
          <h1 class="text-4xl font-bold text-white text-center">Contact Us</h1>
          <p class="text-xl text-blue-100 text-center mt-4">
            We'd love to hear from you. Get in touch with our team.
          </p>
        </div>
      </div>
  
      <!-- Contact Content -->
      <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <!-- Contact Information -->
          <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Get In Touch</h2>
            
            <div class="space-y-6">
              <div class="flex items-start">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-800">Phone</h3>
                  <p class="text-gray-600 mt-1">(123) 456-7890</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-800">Email</h3>
                  <p class="text-gray-600 mt-1">info@yourcompany.com</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-800">Address</h3>
                  <p class="text-gray-600 mt-1">123 Business Street</p>
                  <p class="text-gray-600">City, State 12345</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-800">Business Hours</h3>
                  <p class="text-gray-600 mt-1">Monday - Friday: 9am - 5pm</p>
                  <p class="text-gray-600">Saturday & Sunday: Closed</p>
                </div>
              </div>
            </div>
            
            <!-- Social Media Links -->
            <div class="mt-8">
              <h3 class="text-lg font-medium text-gray-800 mb-4">Follow Us</h3>
              <div class="flex space-x-4">
                <a href="#" class="bg-blue-100 p-3 rounded-full hover:bg-blue-200 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                  </svg>
                </a>
                <a href="#" class="bg-blue-100 p-3 rounded-full hover:bg-blue-200 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                  </svg>
                </a>
                <a href="#" class="bg-blue-100 p-3 rounded-full hover:bg-blue-200 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 4.237 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.181-.78 1.172-4.97 1.172-4.97s-.299-.6-.299-1.486c0-1.39.806-2.428 1.81-2.428.852 0 1.264.64 1.264 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.177-4.068-2.845 0-4.515 2.135-4.515 4.34 0 .859.331 1.781.745 2.281a.3.3 0 01.069.288l-.278 1.133c-.044.183-.145.223-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.525-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S17.523 2 12 2z"/>
                  </svg>
                </a>
                <a href="#" class="bg-blue-100 p-3 rounded-full hover:bg-blue-200 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.017 2C6.474 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12.017 2z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
          
          <!-- Contact Form -->
          <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Send Us a Message</h2>
            
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Name Input -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="form.name" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Your Name"
                  required
                />
                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
              </div>
              
              <!-- Email Input -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="form.email" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="your.email@example.com"
                  required
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
              </div>
  
              <!-- Subject Input -->
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input 
                  type="text" 
                  id="subject" 
                  v-model="form.subject" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="What is your message about?"
                  required
                />
                <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
              </div>
              
              <!-- Message Input -->
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea 
                  id="message" 
                  v-model="form.message" 
                  rows="5" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Please describe your enquiry in detail..."
                  required
                ></textarea>
                <p v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message }}</p>
              </div>
              
              <!-- Submit Button -->
              <div>
                <button 
                  type="submit" 
                  class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 px-6 rounded-md hover:from-blue-700 hover:to-indigo-800 transition duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting">Sending...</span>
                  <span v-else>Send Message</span>
                </button>
              </div>
            </form>
            
            <!-- Success Message -->
            <div v-if="submitted" class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
              Thank you for your message! We'll get back to you as soon as possible.
            </div>
          </div>
        </div>
      </div>
  
      <!-- Map Section (Optional) -->
      <div class="container mx-auto px-4 py-12">
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Find Us</h2>
          <!-- Replace with your preferred map integration -->
          <div class="h-64 bg-gray-200 rounded-md flex items-center justify-center overflow-hidden">
            <div class="w-full h-full relative">
                <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28675.459433962693!2d28.182524335456343!3d-26.052120488886704!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e956c9e7dd28a2d%3A0x23a8745861e3d627!2sBirch%20Acres%2C%20Kempton%20Park%2C%201618!5e0!3m2!1sen!2sza!4v1747170368580!5m2!1sen!2sza" 
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 0.375rem;"
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
           </div>
        </div>
      </div>
    </div>
    <Footer />
  </template>
  
  <script>
import Navbar from './Navbar.vue';
import Footer from './Footer.vue';

  export default {
    components: { Navbar, Footer },
    name: 'ContactUs',
    data() {
      return {
        form: {
          name: '',
          email: '',
          subject: '',
          message: ''
        },
        errors: {
          name: '',
          email: '',
          subject: '',
          message: ''
        },
        isSubmitting: false,
        submitted: false
      }
    },
    methods: {
      validateForm() {
        let valid = true;
        this.errors = {
          name: '',
          email: '',
          subject: '',
          message: ''
        };
        
        if (!this.form.name.trim()) {
          this.errors.name = 'Name is required';
          valid = false;
        }
        
        if (!this.form.email.trim()) {
          this.errors.email = 'Email is required';
          valid = false;
        } else if (!this.isValidEmail(this.form.email)) {
          this.errors.email = 'Please enter a valid email address';
          valid = false;
        }
        
        if (!this.form.subject.trim()) {
          this.errors.subject = 'Subject is required';
          valid = false;
        }
        
        if (!this.form.message.trim()) {
          this.errors.message = 'Message is required';
          valid = false;
        }
        
        return valid;
      },
      
      isValidEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
      },
      
      async submitForm() {
        if (!this.validateForm()) {
          return;
        }
        
        this.isSubmitting = true;
        
        try {
          // Replace with your Laravel 11 API endpoint
          // Example using Fetch API
          /*
          const response = await fetch('/api/contact', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(this.form)
          });
          
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          
          const data = await response.json();
          */
          
          // Simulating API call delay - remove this in production
          await new Promise(resolve => setTimeout(resolve, 1000));
          
          // Reset form
          this.form = {
            name: '',
            email: '',
            subject: '',
            message: ''
          };
          
          this.submitted = true;
        } catch (error) {
          console.error('Error submitting form:', error);
          // Handle error state here
        } finally {
          this.isSubmitting = false;
        }
      }
    }
  }
  </script>