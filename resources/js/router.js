import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue';
import Quotations from './components/Admin/Dashboard/Quotations/Quotations.vue';
import Profile from './components/Admin/Dashboard/Profile/Profile.vue';
import Settings from './components/Admin/Dashboard/Settings/Settings.vue';
import Services from './components/Admin/Dashboard/Services/Services.vue';
import Invoice from './components/Admin/Dashboard/Invoices/Invoice.vue';
import Enquiries from './components/Admin/Dashboard/Enquiries/Enquiries.vue';
import Users from './components/Admin/Dashboard/Settings/Users.vue';
import Customers from './components/Admin/Dashboard/Clients/Clients.vue';
import DeliveryNotes from './components/Admin/Dashboard/DeliveryNotes/DeliveryNotes.vue';


const routes = [
    { path: '/admin/dashboard', name: 'dashboard', component: Dashboard },
    { path: '/admin/enquiries', name: 'enquiries', component: Enquiries },
    { path: '/admin/quotations', component: Quotations },
    { path: '/admin/profile', component: Profile },
    { path: '/admin/settings', component: Settings },
    { path: '/admin/services', component: Services },
    { path: '/admin/invoices', component: Invoice },
    { path: '/admin/users', name: 'user-management', component: Users },
    { path: '/admin/services', component: Services },
    { path: '/admin/invoices', component: Invoice },
    { path: '/admin/customers', component: Customers },
    { path: '/admin/delivery-notes', component: DeliveryNotes },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
