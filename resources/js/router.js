import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue';
import Quotations from './components/Admin/Dashboard/Quotations/Quotations.vue';
import Profile from './components/Admin/Dashboard/Profile/Profile.vue';
import Settings from './components/Admin/Dashboard/Settings/Settings.vue';
import Services from './components/Admin/Dashboard/Services.vue';


const routes = [
    { path: '/admin/dashboard', component: Dashboard },
    { path: '/admin/quotations', component: Quotations },,
    { path: '/admin/profile', component: Profile },
    { path: '/admin/settings', component: Settings },
    { path: '/admin/services', component: Services },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
