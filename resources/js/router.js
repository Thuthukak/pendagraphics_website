import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue';
import Quotations from './components/Admin/Dashboard/Quotations/Quotations.vue';
import Profile from './components/Admin/Dashboard/Profile/Profile.vue';
import Settings from './components/Admin/Dashboard/Settings/Settings.vue';
import Services from './components/Admin/Dashboard/Services/Services.vue';
import Invoice from './components/Admin/Dashboard/Invoices/Invoice.vue';
import Users from './components/Admin/Dashboard/Settings/Users.vue';
import Enquiries from './components/Admin/Dashboard/Enquiries/Enquiries.vue';


const routes = [
    { path: '/admin/dashboard', component: Dashboard },
    { path: '/admin/quotations', component: Quotations },,
    { path: '/admin/profile', component: Profile },
    { path: '/admin/enquiries', name: 'enquiries', component: Enquiries },
    { path: '/admin/settings', component: Settings },
    { path: '/admin/users', name: 'user-management', component: Users },
    { path: '/admin/services', component: Services },
    { path: '/admin/invoices', component: Invoice },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
