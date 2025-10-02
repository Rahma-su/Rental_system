<template>
  <div
  class="flex h-screen min-h-screen font-times text-gray-800 relative"
  :style="route.path !== '/dashboard' ? { backgroundColor: '#D1D3D4' } : {}"
>
  <!-- Purple background only on Dashboard -->
  <div
    v-if="route.path === '/dashboard'"
    class="absolute top-0 left-0 w-full"
    :style="{ height: '35%', backgroundColor: '#9AA6B2' }"
  >
    <!-- Purple header background -->
  </div>

    <!-- Sidebar -->
    <aside class="relative w-64 bg-white text-gray-800 shadow-md flex flex-col hidden md:flex z-20 mt-6 ml-4 rounded-xl overflow-hidden">
      <div class="p-6 text-xl font-bold border-b border-gray-200">
        Rental System
      </div>
      <nav class="flex-1 p-6 space-y-2">
        <router-link to="/" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
      <img src="../assets/icons/dashboard.png" class="w-5 h-5 mr-3" alt="Dashboard" />
       Dashboard
       </router-link>

        <router-link to="/units" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
      <img src="../assets/icons/units.png" class="w-6 h-6 mr-3" alt="Units" />
     Units
    </router-link>

        <router-link to="/unitslist" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
  <img src="../assets/icons/list.png" class="w-5 h-5 mr-3" alt="Units List" />
  Units List
</router-link>
        <router-link to="/tenantform" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
  <img src="../assets/icons/tenant.png" class="w-5 h-5 mr-3" alt="Tenant Form" />
  Tenant Form
</router-link>

        <router-link to="/tenantlist" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
  <img src="../assets/icons/list.png" class="w-5 h-5 mr-3" alt="Tenant List" />
  Tenant List
</router-link>

        <router-link to="/leases" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
  <img src="../assets/icons/lease.png" class="w-5 h-5 mr-3" alt="Lease" />
  Lease
</router-link>

        <router-link to="/leaseslist" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
  <img src="../assets/icons/list.png" class="w-5 h-5 mr-3" alt="Leases List" />
  Leases List
</router-link>

       <router-link to="/billmanagment" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
        <img src="../assets/icons/money.png" class="w-5 h-5 mr-3" alt="Bill Management" />
             Bill Management
        </router-link>
        <router-link to="/maintenance" class="flex items-center px-4 py-2 rounded hover:bg-gray-100 transition-colors">
        <img src="../assets/icons/maintenance.png" class="w-5 h-5 mr-3" alt="Bill Management" />
            Maintenance
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative z-10">
      <!-- Navbar -->
      <header class="p-4 flex justify-between items-center sticky top-0 z-20 bg-transparent">
  <!-- <h1
    class="text-lg font-semibold drop-shadow"
    :class="route.path === '/' ? 'text-white' : 'text-gray-800'"
  >
    {{ pageTitle }}
  </h1> -->
  <h1
  class="text-2xl font-semibold drop-shadow uppercase"
  :class="route.path === '/dashboard' ? 'text-white' : 'text-gray-800'"
>
  {{ pageTitle }}
</h1>


  <div class="flex items-center space-x-4">
    <input
      type="text"
      placeholder="Search..."
      class="px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-200"
    />
    <button
      @click="logout" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-blue-400 transition-colors"
    >
      Logout
    </button>
  </div>
</header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto relative p-8 w-full">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router"; // ✅ import useRouter

const route = useRoute();
const router = useRouter(); // ✅ define router

const logout = () => {
  localStorage.removeItem("token");
  router.push("/login"); // ✅ works now
};

const pageTitle = computed(() => {
  switch (route.path) {
    case "/dashboard":
      return "Dashboard";
    case "/units":
      return "Units";
    case "/unitslist":
      return "Units List";
    case "/tenantform":
      return "Tenant Form";
    case "/tenantlist":
      return "Tenant List";
    case "/leases":
      return "Lease";
    case "/leaseslist":
      return "Leases List";
    case "/billmanagment":
      return "Bill Management";
    case "/maintenance":
      return "Maintenance";
    default:
      return "Page";
  }
});
</script>


<style scoped>
.font-times {
  font-family: 'Times New Roman', Times, serif;
}
</style>
