import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap';
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
app.component('Bookings', Bookings);
app.component('Admin', Admin);
app.component('Dashboard', Dashboard);
app.component('DashboardLayout', DashboardLayout);
app.component("font-awesome-icon", FontAwesomeIcon);
app.use(router);

app.mount('#app');
