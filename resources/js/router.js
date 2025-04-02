import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue';
import AdminBookings from './components/Admin/Dashboard/AdminBookings.vue';
import Profile from './components/Admin/Dashboard/Profile/Profile.vue';
import Settings from './components/Admin/Dashboard/Settings/Settings.vue';
import Services from './components/Admin/Dashboard/Services.vue';


const routes = [
    { path: '/admin/dashboard', component: Dashboard },
    { path: '/admin/bookings', component: AdminBookings },
    { path: '/admin/profile', component: Profile },
    { path: '/admin/settings', component: Settings },
    { path: '/admin/services', component: Services },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
