import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import '../css/custom.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"; 
import router from './router'; 

import { faBars, 
        faMoon, 
        faSun, 
        faGlobe, 
        faUser, 
        faBell, 
        faCog, 
        faHome,
        faTrash,
        faFilePdf, 
        faSignOutAlt,
        faCalendarAlt,
        faClipboardList,
        faEye,
        faSpinner,
        faPaperPlane,
        faMailForward,
        faEnvelope,
        faPaintBrush,
        faFileInvoiceDollar
    } from "@fortawesome/free-solid-svg-icons";

import { 
    faTiktok,
    faYoutube,
    faInstagram,
    faFacebook
} from "@fortawesome/free-brands-svg-icons";

library.add(faBars,
            faFacebook,
            faInstagram,
            faYoutube,
            faTiktok, 
            faMoon,
            faFileInvoiceDollar, 
            faSun, 
            faGlobe, 
            faUser, 
            faBell, 
            faCog, 
            faTrash,
            faEye,
            faFilePdf,
            faHome, 
            faSignOutAlt,
            faClipboardList,
            faCalendarAlt,
            faSpinner,
            faEnvelope,
            faMailForward,
            faPaperPlane,
            faPaintBrush,
            
        );


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

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
app.component('about-us', AboutUS);
app.component('contact-us', ContactUs);
app.component('estimate-modal', EstimateModal);

app.mount('#app');
