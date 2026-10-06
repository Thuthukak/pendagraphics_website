import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import '../css/custom.css';
import { createApp } from 'vue';
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
        faPencil,
        faTrashAlt,
        faUserCog,
        faEllipsisH,
        faUserCheck,
        faLeaf,
        faHeart,
        faScaleBalanced,
        faSeedling,
        faLock,
        faHandHolding,
        faTruck,
        faUsers,
        faPlus,
        faSearch,
        faXmark,
        faEllipsisVertical,
        faClone,
        faPause,
        faPlay,
        faCircleCheck,
        faCheck,
        faBan,
        faCopy

    } from "@fortawesome/free-solid-svg-icons";

import { 
    faTiktok,
    faYoutube,
    faInstagram,
    faFacebook,
    faWhatsapp,
} from "@fortawesome/free-brands-svg-icons";

library.add(faBars,
            faFacebook,
            faInstagram,
            faYoutube,
            faTiktok, 
            faWhatsapp,
            faMoon,
            faFileInvoiceDollar, 
            faSun, 
            faGlobe, 
            faUser,
            faUserCog, 
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
            faPencil,
            faTrashAlt,
            faEllipsisH,
            faUserCheck,
            faLeaf,
            faHeart,
            faScaleBalanced,
            faSeedling,
            faLock,
            faHandHolding,
            faTruck,
            faUsers,
            faPlus,
            faSearch,
            faXmark,
            faEllipsisVertical,
            faClone,
            faPause,
            faPlay,
            faCircleCheck,
            faCheck,
            faBan,
            faCopy
            
        );



const app = createApp({});

app.component('Dashboard', Dashboard);
app.component('DashboardLayout', DashboardLayout);
app.component("font-awesome-icon", FontAwesomeIcon);
app.use(router);


app.mount('#app');