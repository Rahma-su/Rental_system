import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../pages/Dashboard.vue";
import TenantForm from "../pages/TenantForm.vue";
import Maintenance from "../pages/Maintenance.vue";
import Payments from "../pages/Payments.vue";
import Units from "../pages/Units.vue";
import Leases from "../pages/Leases.vue";
import BillManagment from "../pages/BillManagment.vue";



const routes = [
  { path: "/", component: Dashboard },
  { path: "/tenantform", component: TenantForm },
  { path: "/maintenance", component: Maintenance },
  { path: "/payments", component: Payments },
  { path: "/units", component: Units },
  { path: "/leases", component: Leases },
  { path: "/billmanagment", component: BillManagment },
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

