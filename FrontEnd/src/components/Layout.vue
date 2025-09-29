<template> 
  <div class="flex h-screen min-h-screen font-times bg-gray-50 text-gray-800">
    <!-- Sidebar -->
    <aside class="w-64 bg-white text-gray-800 shadow-md flex flex-col hidden md:flex">
      <div class="p-6 text-xl font-bold border-b border-gray-200">Rental System</div>
      <nav class="flex-1 p-6 space-y-2">
        <router-link
          to="/"
          class="block px-4 py-2 rounded hover:bg-gray-100 transition-colors"
        >
          Dashboard
        </router-link>
        
         
         <router-link
          to="/units"
          class="block px-4 py-2 rounded hover:bg-gray-100 transition-colors"
        >
        Units
        </router-link>
        <router-link
          to="/tenantform"
          class="block px-4 py-2 rounded hover:bg-gray-100 transition-colors"
        >
          TenantForm
        </router-link>
        <router-link
          to="/leases"
          class="block px-4 py-2 rounded hover:bg-gray-100 transition-colors"
        >
          Lease
        </router-link>
        <router-link
          to="/billmanagment"
          class="block px-4 py-2 rounded hover:bg-gray-100 transition-colors"
        >
        Bill Managment
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Navbar -->
      <header class="bg-white shadow p-4 flex justify-between items-center sticky top-0 z-10">
        <h1 class="text-lg font-semibold">{{ pageTitle }}</h1>
        <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-400 transition-colors">
          Logout
        </button>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-8 w-full">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

const pageTitle = computed(() => {
  switch (route.path) {
    case "/":
      return "Dashboard";
    
    case "/units":
      return "Units";
    case "/tenantform":
      return "TenantForm";
    case "/maintenance":
      return "Maintenance";
    case "/billmanagment":
      return "Bill Managment";
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
