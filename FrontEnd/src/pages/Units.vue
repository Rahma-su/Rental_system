<template>
  <Layout>
    <template #default>
      <div class="p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Unit Management</h2>

        <!-- Search -->
        <input v-model="searchQuery" type="text" placeholder="Search by Unit Name" class="border p-2 mb-4 w-full" />

        <!-- Add/Edit Unit Form -->
        <div class="mb-6 border p-4 rounded bg-gray-50">
          <h3 class="text-lg font-semibold mb-2">{{ form.id ? 'Edit Unit' : 'Add New Unit' }}</h3>
          <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <input v-model="form.unit_name" placeholder="Unit Name" class="form-input" required />

            <input v-model.number="form.size_sqm" type="number" step="0.01" placeholder="Size (sqm)" class="form-input" />

            <select v-model="form.type" class="form-input" required>
              <option disabled value="">Select Type</option>
              <option>Office</option>
              <option>Shop</option>
              <option>Studio</option>
              <option>Store</option>
              <option>Locker</option>
              <option>Other</option>
            </select>

            <select v-model="form.with_vat" class="form-input">
              <option :value="true">With VAT</option>
              <option :value="false">Without VAT</option>
            </select>

            <input :value="calculatedMonthlyRent" type="number" step="0.01" placeholder="Monthly Rent" class="form-input bg-gray-100" disabled />

            <input v-model.number="form.security_deposit" type="number" step="0.01" placeholder="Security Deposit" class="form-input" />
            <input v-model.number="form.water_and_electric" type="number" step="0.01" placeholder="Water + Electric Total" class="form-input" />
            <input v-model.number="form.water" type="number" step="0.01" placeholder="Water Charge" class="form-input" />
            <input v-model.number="form.electric" type="number" step="0.01" placeholder="Electric Charge" class="form-input" />
            <input v-model.number="form.parking" type="number" step="0.01" placeholder="Parking" class="form-input" />
            <input v-model="form.other" type="text" placeholder="Other Charges" class="form-input" />

            <select v-model="form.lease_term" class="form-input">
              <option disabled value="">Select Lease Term (Months)</option>
              <option v-for="n in 12" :key="n" :value="n.toString()">{{ n }} Month{{ n > 1 ? 's' : '' }}</option>
            </select>

            <select v-model="form.agreement" class="form-input">
               <option disabled value="">Select Agreement Term (Months)</option>
               <option v-for="n in 12" :key="`agree-${n}`" :value="n.toString()">{{ n }} Month{{ n > 1 ? 's' : '' }}</option>
            </select>

            <select v-model="form.status" class="form-input" required>
              <option disabled value="">Select Status</option>
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>

            <div class="md:col-span-3">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ form.id ? 'Update' : 'Add' }} Unit
              </button>
            </div>
          </form>
        </div>

        <!-- Unit List Table -->
        <div v-if="loading" class="text-gray-600">Loading units...</div>
        <div v-else>
          <table class="table-auto w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Name</th>
                <th class="border px-2 py-1">Size</th>
                <th class="border px-2 py-1">Type</th>
                <th class="border px-2 py-1">VAT</th>
                <th class="border px-2 py-1">Rent</th>
                <th class="border px-2 py-1">Deposit</th>
                <th class="border px-2 py-1">Water+Elec</th>
                <th class="border px-2 py-1">Water</th>
                <th class="border px-2 py-1">Electric</th>
                <th class="border px-2 py-1">Parking</th>
                <th class="border px-2 py-1">Other</th>
                <th class="border px-2 py-1">Lease Term</th>
                <th class="border px-2 py-1">Agreement</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(unit, index) in paginatedUnits" :key="unit.id">
                <td class="border px-2 py-1">{{ index + 1 }}</td>
                <td class="border px-2 py-1">{{ unit.unit_name }}</td>
                <td class="border px-2 py-1">{{ unit.size_sqm }}</td>
                <td class="border px-2 py-1">{{ unit.type }}</td>
                <td class="border px-2 py-1">{{ unit.with_vat ? 'Yes' : 'No' }}</td>
                <td class="border px-2 py-1">{{ unit.monthly_rent }}</td>
                <td class="border px-2 py-1">{{ unit.security_deposit }}</td>
                <td class="border px-2 py-1">{{ unit.water_and_electric }}</td>
                <td class="border px-2 py-1">{{ unit.water }}</td>
                <td class="border px-2 py-1">{{ unit.electric }}</td>
                <td class="border px-2 py-1">{{ unit.parking }}</td>
                <td class="border px-2 py-1">{{ unit.other }}</td>
                <td class="border px-2 py-1">{{ unit.lease_term }}</td>
                <td class="border px-2 py-1">{{ unit.agreement }}</td>
                <td class="border px-2 py-1">{{ unit.status }}</td>
                <td class="border px-2 py-1 whitespace-nowrap">
                  <button @click="editUnit(unit)" class="text-blue-600 hover:underline mr-2">Edit</button>
                  <button @click="deleteUnit(unit)" class="text-red-600 hover:underline">Delete</button>
                </td>
              </tr>
              <tr v-if="paginatedUnits.length === 0">
                <td colspan="16" class="text-center text-gray-500 py-4">No units found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Layout from "../components/Layout.vue"
import api from "../services/api.js"

const units = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const pageSize = 40

const form = ref({
  id: null,
  unit_name: '',
  size_sqm: null,
  type: '',
  with_vat: true,
  monthly_rent: null,
  security_deposit: null,
  water_and_electric: null,
  water: null,
  electric: null,
  parking: null,
  other: '',
  lease_term: '',
  agreement: '',
  status: 'available'
})

// Calculate monthly rent dynamically
const calculatedMonthlyRent = computed(() => {
  const size = parseFloat(form.value.size_sqm) || 0
  const vat = form.value.with_vat ? 50 : 1 // example: 15% VAT
  const rent = size * vat
  form.value.monthly_rent = rent ? rent.toFixed(2) : null
  return rent ? rent.toFixed(2) : ''
})

const fetchUnits = async () => {
  loading.value = true
  try {
    const res = await api.get('/units')
    units.value = res.data
  } catch(err) {
    console.error('Error fetching units:', err)
  } finally {
    loading.value = false
  }
}

const submitForm = async () => {
  const payload = {
    ...form.value,
    size_sqm: form.value.size_sqm ? Number(form.value.size_sqm) : null,
    with_vat: !!form.value.with_vat,
    monthly_rent: form.value.monthly_rent ? Number(form.value.monthly_rent) : null,
    security_deposit: form.value.security_deposit ? Number(form.value.security_deposit) : null,
    water_and_electric: form.value.water_and_electric ? Number(form.value.water_and_electric) : null,
    water: form.value.water ? Number(form.value.water) : null,
    electric: form.value.electric ? Number(form.value.electric) : null,
    parking: form.value.parking ? Number(form.value.parking) : null,
    lease_term: form.value.lease_term ? String(form.value.lease_term) : null,
    agreement: form.value.agreement ? String(form.value.agreement) : null
  }

  try {
    if(form.value.id){
      await api.put(`/units/${form.value.id}`, payload)
    } else {
      await api.post('/units', payload)
    }
    resetForm()
    fetchUnits()
  } catch(err){
    if(err.response && err.response.status === 422){
      alert('Validation Error: ' + JSON.stringify(err.response.data.errors))
    } else {
      console.error('Error saving unit:', err)
    }
  }
}

const editUnit = (unit) => {
  form.value = { ...unit }
}

const deleteUnit = async (unit) => {
  if(!confirm('Are you sure you want to delete this unit?')) return
  try {
    await api.delete(`/units/${unit.id}`)
    fetchUnits()
  } catch(err){
    console.error('Error deleting unit:', err)
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    unit_name: '',
    size_sqm: null,
    type: '',
    with_vat: true,
    monthly_rent: null,
    security_deposit: null,
    water_and_electric: null,
    water: null,
    electric: null,
    parking: null,
    other: '',
    lease_term: '',
    agreement: '',
    status: 'available'
  }
}

const filteredUnits = computed(() => {
  const q = searchQuery.value.toLowerCase()
  return units.value.filter(u => u.unit_name.toLowerCase().includes(q))
})

const paginatedUnits = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  const end = start + pageSize
  return filteredUnits.value.slice(start, end)
})

onMounted(fetchUnits)
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
