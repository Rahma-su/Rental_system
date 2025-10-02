<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto">
      <h2 class="text-xl font-semibold mb-4">Lease List</h2>

      <!-- Search Bar -->
      <div class="flex justify-between items-center mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search by Tenant or Unit..."
          class="border p-2 rounded w-1/3"
        />
        <button
          @click="exportExcel"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          Export to Excel
        </button>
      </div>

      <!-- Table -->
      <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Tenant</th>
            <th class="border px-4 py-2">Unit</th>
            <th class="border px-4 py-2">Start Date</th>
            <th class="border px-4 py-2">End Date</th>
            <th class="border px-4 py-2">Monthly Rent</th>
            <th class="border px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="lease in filteredLeases"
            :key="lease.id"
            class="hover:bg-gray-50"
          >
            <td class="border px-4 py-2">{{ lease.id }}</td>
            <td class="border px-4 py-2">{{ lease.tenant?.full_name }}</td>
            <td class="border px-4 py-2">{{ lease.unit?.unit_name }}</td>
            <td class="border px-4 py-2">{{ lease.lease_start_date }}</td>
            <td class="border px-4 py-2">{{ lease.lease_end_date }}</td>
            <td class="border px-4 py-2">{{ lease.monthly_rent }}</td>
            <td class="border px-4 py-2 flex space-x-2">
              <button
                @click="editLease(lease)"
                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
              >
                Edit
              </button>
              <button
                @click="deleteLease(lease.id)"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Layout from "../components/Layout.vue";
import api from "../services/api.js";

const leases = ref([]);
const search = ref("");
const editingLease = ref(null);

const fetchLeases = async () => {
  const res = await api.get("/leases", { params: { search: search.value } });
  leases.value = res.data;
};

const filteredLeases = computed(() => {
  if (!search.value) return leases.value;
  return leases.value.filter(
    (l) =>
      l.tenant?.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
      l.unit?.unit_name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const deleteLease = async (id) => {
  if (confirm("Are you sure you want to delete this lease?")) {
    await api.delete(`/leases/${id}`);
    leases.value = leases.value.filter((l) => l.id !== id);
  }
};

const editLease = (lease) => {
  editingLease.value = { ...lease };
};

const updateLease = async () => {
  await api.put(`/leases/${editingLease.value.id}`, editingLease.value);
  await fetchLeases();
  editingLease.value = null;
};

const exportExcel = async () => {
  const response = await api.get("/leases/export", { responseType: "blob" });
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", "leases.xlsx");
  document.body.appendChild(link);
  link.click();
};

onMounted(fetchLeases);
</script>
