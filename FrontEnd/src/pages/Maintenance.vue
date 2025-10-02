<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto space-y-6">
      <h2 class="text-2xl font-semibold mb-4">Maintenance Management</h2>

      <!-- Add/Edit Form -->
      <div class="border p-4 rounded bg-gray-50">
        <h3 class="text-lg font-semibold mb-2">{{ form.id ? 'Edit Maintenance' : 'Add New Maintenance' }}</h3>
        <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <select v-model="form.unit_id" class="form-input" required>
            <option disabled value="">Select Unit</option>
            <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.unit_name }}</option>
          </select>

          <input v-model="form.title" type="text" placeholder="Title" class="form-input" required />

          <input v-model.number="form.cost" type="number" step="0.01" placeholder="Cost" class="form-input" required />

          <input v-model="form.maintenance_date" type="date" class="form-input" required />

          <select v-model="form.status" class="form-input" required>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>

          <input v-model="form.description" type="text" placeholder="Description (optional)" class="form-input md:col-span-3" />

          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 md:col-span-3">
            {{ form.id ? 'Update' : 'Add' }} Maintenance
          </button>
        </form>
      </div>

      <!-- Maintenance Table -->
      <div>
        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">#</th>
              <th class="border px-2 py-1">Unit</th>
              <th class="border px-2 py-1">Title</th>
              <th class="border px-2 py-1">Cost</th>
              <th class="border px-2 py-1">Date</th>
              <th class="border px-2 py-1">Status</th>
              <th class="border px-2 py-1">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in maintenances" :key="item.id" class="hover:bg-gray-50">
              <td class="border px-2 py-1">{{ index + 1 }}</td>
              <td class="border px-2 py-1">{{ item.unit?.unit_name }}</td>
              <td class="border px-2 py-1">{{ item.title }}</td>
              <td class="border px-2 py-1">{{ item.cost }}</td>
              <td class="border px-2 py-1">{{ item.maintenance_date }}</td>
              <td class="border px-2 py-1 capitalize">{{ item.status.replace('_', ' ') }}</td>
              <td class="border px-2 py-1 flex space-x-2">
                <button @click="editMaintenance(item)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</button>
                <button @click="deleteMaintenance(item.id)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
              </td>
            </tr>
            <tr v-if="maintenances.length === 0">
              <td colspan="7" class="text-center text-gray-500 py-4">No maintenance records found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>
<script setup>
import { ref, onMounted } from "vue";
import Layout from "../components/Layout.vue";
import api from "../services/api.js";

const units = ref([]);
const maintenances = ref([]);

const form = ref({
  id: null,
  unit_id: null,
  title: "",
  description: "",
  cost: null,
  maintenance_date: "",
  status: "pending",
});

// Fetch units and maintenances
const fetchData = async () => {
  try {
    const [unitsRes, maintRes] = await Promise.all([
      api.get("/units"),
      api.get("/maintenances")
    ]);

    units.value = unitsRes.data;
    maintenances.value = maintRes.data;
  } catch (err) {
    console.error("Failed to fetch maintenance data:", err);
  }
};

onMounted(fetchData);

// Add or update maintenance
const submitForm = async () => {
  try {
    if (form.value.id) {
      await api.put(`/maintenances/${form.value.id}`, form.value);
    } else {
      await api.post("/maintenances", form.value);
    }
    resetForm();
    fetchData();
  } catch (err) {
    console.error("Error saving maintenance:", err);
  }
};

const editMaintenance = (item) => {
  form.value = { ...item };
};

const deleteMaintenance = async (id) => {
  if (!confirm("Are you sure you want to delete this record?")) return;
  try {
    await api.delete(`/maintenances/${id}`);
    fetchData();
  } catch (err) {
    console.error("Failed to delete maintenance:", err);
  }
};

const resetForm = () => {
  form.value = {
    id: null,
    unit_id: null,
    title: "",
    description: "",
    cost: null,
    maintenance_date: "",
    status: "pending",
  };
};
</script>
<style scoped>
.form-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
}
</style>

