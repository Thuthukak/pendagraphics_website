import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/custom.css';
import { createApp } from 'vue';
import App from './components/App.vue';
import Home from './components/Home/Home.vue';
import Bookings from './components/Home/Bookings.vue';
import Admin from './components/Admin/Admin.vue';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue'; 
import DashboardLayout from './Layouts/DashboardLayout.vue';
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"; 
import router from './router'; 

import WebDesign from './components/Home/Services/WebDesign.vue';
import ProductDesign from './components/Home/Services/ProductDesign.vue';
import GraphicDesign from './components/Home/Services/GraphicDesign.vue';
import ECommerce from './components/Home/Services/ECommerce.vue';
import DigitalMarketing from './components/Home/Services/DigitalMarketing.vue';
import IdentityDesign from './components/Home/Services/IdentityDesign.vue';
import WebDesignCarousel from './components/Home/Services/WebDesignCarousel.vue';
import GraphicDesignCarousel from './components/Home/Services/GraphicDesignCarousel.vue';
import ProductDesignCarousel from './components/Home/Services/ProductDesignCarousel.vue';
import IdentityDesignCarousel from './components/Home/Services/IdentityDesignCarousel.vue';
import ECommerceCarousel from './components/Home/Services/ECommerceCarousel.vue';
import FAQ from './components/Home/Faq.vue';
import FaqComponent from './components/Home/FaqComponent.vue';

import { faBars, 
        faMoon, 
        faSun, 
        faGlobe, 
        faUser, 
        faBell, 
        faCog, 
        faHome, 
        faSignOutAlt,
        faCalendarAlt,
    } from "@fortawesome/free-solid-svg-icons";

library.add(faBars, 
            faMoon, 
            faSun, 
            faGlobe, 
            faUser, 
            faBell, 
            faCog, 
            faHome, 
            faSignOutAlt,
            faCalendarAlt);



const app = createApp({});

app.component('Home', Home);
app.component('faq', FAQ);
app.component('Bookings', Bookings);
app.component('Admin', Admin);
app.component('Dashboard', Dashboard);
app.component('DashboardLayout', DashboardLayout);
app.component("font-awesome-icon", FontAwesomeIcon);
app.use(router);

app.component('web-design', WebDesign);
app.component('product-design', ProductDesign);
app.component('graphic-design', GraphicDesign);
app.component('e-commerce', ECommerce);
app.component('digital-marketing', DigitalMarketing);
app.component('identity-design', IdentityDesign);
app.component('WebDesignCarousel', WebDesignCarousel);
app.component('GraphicDesignCarousel', GraphicDesignCarousel);
app.component('ProductDesignCarousel', ProductDesignCarousel);
app.component('IdentityDesignCarousel', IdentityDesignCarousel);
app.component('ECommerceCarousel', ECommerceCarousel);
app.component('faq-component', FaqComponent);

app.mount('#app');
