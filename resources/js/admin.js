import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import '../css/custom.css';
import { createApp } from 'vue';
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import Dashboard from './components/Admin/Dashboard/Dashboard.vue'; 
import DashboardLayout from './Layouts/DashboardLayout.vue';
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
        faFileInvoiceDollar,
        faUserCog,
        faPencil,
        faClone,
        faPlus,
        faPause,
        faPlay,
        faEllipsis,
        faSearch
    } from "@fortawesome/free-solid-svg-icons";

import { 
    faTiktok,
    faYoutube,
    faInstagram,
    faFacebook
} from "@fortawesome/free-brands-svg-icons";
import { faZ } from '@fortawesome/free-solid-svg-icons/faZ';

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
            faUserCog,
            faCog, 
            faTrash,
            faEye,
            faPencil,
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
            faClone,
            faZ,
            faPlus,
            faPause,
            faPlay,
            faEllipsis,
            faSearch,
            faFilePdf
            
            

            
        );



const app = createApp({});

app.component('Dashboard', Dashboard);
app.component('DashboardLayout', DashboardLayout);
app.component("font-awesome-icon", FontAwesomeIcon);
app.use(router);
app.use(Toast, {
    position: 'bottom-right',
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
});


app.mount('#app');