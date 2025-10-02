<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto">
      <h2 class="text-xl font-semibold mb-4">Tenant List</h2>

      <div class="flex justify-between mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search by name or business"
          class="border p-2 rounded w-1/3"
        />
        <button @click="exportExcel"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          Export to Excel
        </button>
      </div>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="border p-2">ID</th>
            <th class="border p-2">Full Name</th>
            <th class="border p-2">Business</th>
            <th class="border p-2">Phone</th>
            <th class="border p-2">Email</th>
            <th class="border p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tenant in filteredTenants" :key="tenant.id">
            <td class="border p-2">{{ tenant.id }}</td>
            <td class="border p-2">{{ tenant.full_name }}</td>
            <td class="border p-2">{{ tenant.business_name }}</td>
            <td class="border p-2">{{ tenant.phone }}</td>
            <td class="border p-2">{{ tenant.email }}</td>
            <td class="border p-2 space-x-2">
              <button @click="editTenant(tenant)"
                      class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                Edit
              </button>
              <button @click="deleteTenant(tenant.id)"
                      class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Tenant Form Modal -->
      <TenantForm v-if="showForm"
                  :editTenant="selectedTenant"
                  @created="onTenantUpdated"
                  @updated="onTenantUpdated"
                  @close="closeForm"/>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Layout from "../components/Layout.vue";
import api from "../services/api.js";
import TenantForm from "./TenantForm.vue";

const tenants = ref([]);
const search = ref('');
const showForm = ref(false);
const selectedTenant = ref(null);

const fetchTenants = async () => {
  const res = await api.get('/tenantform');
  tenants.value = res.data;
};

onMounted(fetchTenants);

const filteredTenants = computed(() => {
  if (!search.value) return tenants.value;
  return tenants.value.filter(t =>
    t.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
    (t.business_name && t.business_name.toLowerCase().includes(search.value.toLowerCase()))
  );
});

const exportExcel = () => {
  window.open(`${api.defaults.baseURL}/tenantform/export`, '_blank');
};

const editTenant = (tenant) => {
  selectedTenant.value = { ...tenant };
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedTenant.value = null;
};

const onTenantUpdated = () => {
  fetchTenants();
  closeForm();
};

const deleteTenant = async (id) => {
  if (confirm('Are you sure you want to delete this tenant?')) {
    try {
      await api.delete(`/tenantform/${id}`);
      tenants.value = tenants.value.filter(t => t.id !== id);
      alert('Tenant deleted successfully!');
    } catch (err) {
      alert('Failed to delete tenant');
      console.error(err);
    }
  }
};
</script>

<style scoped>
table th, table td {
  text-align: left;
}
</style>
