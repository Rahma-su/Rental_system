import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../pages/Dashboard.vue";
import TenantForm from "../pages/TenantForm.vue";
import Maintenance from "../pages/Maintenance.vue";
import Payments from "../pages/Payments.vue";
import Units from "../pages/Units.vue";
import Leases from "../pages/Leases.vue";
import BillManagment from "../pages/BillManagment.vue";
import TenantList from "../pages/TenantList.vue";
import LeasesList from "../pages/LeasesList.vue";
import UnitsList from "../pages/UnitsList.vue";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";


const routes = [
  // { path: "/", component: Dashboard },
  { path: "/tenantform", component: TenantForm,  meta: { requiresAuth: true }, },
  { path: "/maintenance", component: Maintenance ,  meta: { requiresAuth: true },},
  { path: "/payments", component: Payments ,  meta: { requiresAuth: true }, },
  { path: "/units", component: Units ,  meta: { requiresAuth: true },},
  { path: "/leases", component: Leases ,  meta: { requiresAuth: true },},
  { path: "/billmanagment", component: BillManagment ,  meta: { requiresAuth: true },},
  { path: "/tenantlist", component: TenantList ,  meta: { requiresAuth: true },},
  { path: "/leaseslist", component: LeasesList ,  meta: { requiresAuth: true },},
  { path: "/unitslist", component: UnitsList ,  meta: { requiresAuth: true },},
  
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guest: true }, // only for non-logged users
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
    meta: { guest: true },
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
    meta: { requiresAuth: true }, // protected
  },
  {
    path: "/",
    redirect: "/login",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

 






// Auth Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    // user not logged in → redirect to login
    next("/login");
  } else if (to.meta.guest && token) {
    // already logged in but trying to access login/register → redirect to dashboard
    next("/dashboard");
  } else {
    next();
  }
});



export default router;

