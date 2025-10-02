<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto">
      <h2 class="text-xl font-semibold mb-4">Unit List</h2>

      <div class="flex justify-between mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search by unit name or type"
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
            <th class="border p-2">Unit Name</th>
            <th class="border p-2">Size (sqm)</th>
            <th class="border p-2">Type</th>
            <th class="border p-2">Monthly Rent</th>
            <th class="border p-2">Status</th>
            <th class="border p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="unit in filteredUnits" :key="unit.id">
            <td class="border p-2">{{ unit.id }}</td>
            <td class="border p-2">{{ unit.unit_name }}</td>
            <td class="border p-2">{{ unit.size_sqm }}</td>
            <td class="border p-2">{{ unit.type }}</td>
            <td class="border p-2">{{ unit.monthly_rent }}</td>
            <td class="border p-2">{{ unit.status }}</td>
            <td class="border p-2 space-x-2">
              <button @click="editUnit(unit)"
                      class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                Edit
              </button>
              <button @click="deleteUnit(unit.id)"
                      class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Unit Form Modal -->
      <Units v-if="showForm"
                :editUnit="selectedUnit"
                @created="onUnitUpdated"
                @updated="onUnitUpdated"
                @close="closeForm"/>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Layout from "../components/Layout.vue";
import api from "../services/api.js";
import Units from "./Units.vue";

const units = ref([]);
const search = ref('');
const showForm = ref(false);
const selectedUnit = ref(null);

const fetchUnits = async () => {
  const res = await api.get('/units');
  units.value = res.data;
};

onMounted(fetchUnits);

const filteredUnits = computed(() => {
  if (!search.value) return units.value;
  return units.value.filter(u =>
    u.unit_name?.toLowerCase().includes(search.value.toLowerCase()) ||
    u.type?.toLowerCase().includes(search.value.toLowerCase())
  );
});

const exportExcel = () => {
  window.open(`${api.defaults.baseURL}/units/export`, '_blank');
};

const editUnit = (unit) => {
  selectedUnit.value = { ...unit };
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedUnit.value = null;
};

const onUnitUpdated = () => {
  fetchUnits();
  closeForm();
};

const deleteUnit = async (id) => {
  if (confirm('Are you sure you want to delete this unit?')) {
    try {
      await api.delete(`/units/${id}`);
      units.value = units.value.filter(u => u.id !== id);
      alert('Unit deleted successfully!');
    } catch (err) {
      alert('Failed to delete unit');
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
