<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-5xl mx-auto">
      <h2 class="text-xl font-semibold mb-4">
        {{ form.id ? 'Edit Lease' : 'Register Lease' }}
      </h2>

      <!-- Lease Form -->
      <form @submit.prevent="submitForm" novalidate>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Unit -->
          <div>
            <label class="block mb-1 font-medium">Unit <span class="text-red-500">*</span></label>
            <select v-model="form.unit_id" class="form-input" required @change="updateMonthlyRent">
              <option disabled value="">Select Unit</option>
              <option v-for="unit in units" :key="unit.id" :value="unit.id">
                {{ unit.unit_name }}
              </option>
            </select>
            <p v-if="errors.unit_id" class="text-red-600 text-sm">{{ errors.unit_id[0] }}</p>
          </div>

          <!-- Tenant -->
          <div>
            <label class="block mb-1 font-medium">Tenant <span class="text-red-500">*</span></label>
            <select v-model="form.tenant_id" class="form-input" required>
              <option disabled value="">Select Tenant</option>
              <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                {{ tenant.full_name }}
              </option>
            </select>
            <p v-if="errors.tenant_id" class="text-red-600 text-sm">{{ errors.tenant_id[0] }}</p>
          </div>

          <!-- Lease Start Date -->
          <div>
            <label class="block mb-1 font-medium">Lease Start Date <span class="text-red-500">*</span></label>
            <input v-model="form.lease_start_date" type="date" class="form-input" />
            <p v-if="errors.lease_start_date" class="text-red-600 text-sm">{{ errors.lease_start_date[0] }}</p>
          </div>

          <!-- Lease End Date -->
          <div>
            <label class="block mb-1 font-medium">Lease End Date <span class="text-red-500">*</span></label>
            <input v-model="form.lease_end_date" type="date" class="form-input" />
            <p v-if="errors.lease_end_date" class="text-red-600 text-sm">{{ errors.lease_end_date[0] }}</p>
          </div>

          <!-- Monthly Rent -->
          <div>
            <label class="block mb-1 font-medium">Monthly Rent</label>
            <input v-model="form.monthly_rent" type="number" step="0.01" class="form-input bg-gray-100" disabled />
            <p v-if="errors.monthly_rent" class="text-red-600 text-sm">{{ errors.monthly_rent[0] }}</p>
          </div>

          <!-- Grace Period -->
          <div>
            <label class="block mb-1 font-medium">Grace Period (days)</label>
            <input v-model="form.grace_period" type="number" min="0" class="form-input" />
            <p v-if="errors.grace_period_days" class="text-red-600 text-sm">{{ errors.grace_period_days[0] }}</p>
          </div>

          <!-- Late Fee -->
          <div>
            <label class="block mb-1 font-medium">Late Fee Penalty</label>
            <input v-model="form.late_fee_penalty" type="number" step="0.01" class="form-input" />
            <p v-if="errors.late_fee_penalty_percent" class="text-red-600 text-sm">{{ errors.late_fee_penalty_percent[0] }}</p>
          </div>

          <!-- Notes -->
          <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Notes</label>
            <textarea v-model="form.notes" class="form-input"></textarea>
            <p v-if="errors.notes" class="text-red-600 text-sm">{{ errors.notes[0] }}</p>
          </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex items-center space-x-3">
          <button type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                  :disabled="submitting">
            {{ form.id ? 'Update Lease' : 'Register Lease' }}
          </button>
          <button @click="closeForm" type="button" class="text-gray-600 hover:underline">Cancel</button>
        </div>
      </form>

      <!-- Success Message -->
      <div v-if="successMessage" class="mt-4 p-3 bg-green-100 text-green-800 rounded">
        {{ successMessage }}
      </div>

      <!-- Leases Table -->
      <div class="mt-8">
        <h3 class="text-lg font-semibold mb-2">Existing Leases</h3>
        <table class="table-auto w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">#</th>
              <th class="border px-2 py-1">Unit</th>
              <th class="border px-2 py-1">Tenant</th>
              <th class="border px-2 py-1">Start Date</th>
              <th class="border px-2 py-1">End Date</th>
              <th class="border px-2 py-1">Monthly Rent</th>
              <th class="border px-2 py-1">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(lease, index) in leases" :key="lease.id">
              <td class="border px-2 py-1">{{ index + 1 }}</td>
              <td class="border px-2 py-1">{{ lease.unit.unit_name }}</td>
              <td class="border px-2 py-1">{{ lease.tenant.full_name }}</td>
              <td class="border px-2 py-1">{{ lease.lease_start_date }}</td>
              <td class="border px-2 py-1">{{ lease.lease_end_date }}</td>
              <td class="border px-2 py-1">{{ lease.monthly_rent }}</td>
              <td class="border px-2 py-1">
                <button @click="editLease(lease)" class="text-blue-600 hover:underline mr-2">Edit</button>
              </td>
            </tr>
            <tr v-if="leases.length === 0">
              <td colspan="7" class="text-center text-gray-500 py-4">No leases found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import Layout from "../components/Layout.vue"
import api from "../services/api.js"

const form = reactive({
  id: null,
  unit_id: '',
  tenant_id: '',
  lease_start_date: '',
  lease_end_date: '',
  monthly_rent: null,
  grace_period: 0,
  late_fee_penalty: 0,
  notes: ''
})

const units = ref([])
const tenants = ref([])
const leases = ref([])
const errors = reactive({})
const submitting = ref(false)
const successMessage = ref('')

const fetchData = async () => {
  try {
    units.value = (await api.get('/units')).data
    tenants.value = (await api.get('/tenantform')).data
    leases.value = (await api.get('/leases')).data
  } catch (err) {
    console.error("Error fetching data", err)
  }
}

onMounted(fetchData)

const updateMonthlyRent = () => {
  const selectedUnit = units.value.find(u => u.id === form.unit_id)
  if (selectedUnit) form.monthly_rent = selectedUnit.monthly_rent
}

const submitForm = async () => {
  submitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])
  successMessage.value = ''

  try {
    if (form.id) {
      await api.put(`/leases/${form.id}`, form)
      successMessage.value = 'Lease successfully updated!'
    } else {
      await api.post('/leases', form)
      successMessage.value = 'Lease successfully registered!'
    }
    resetForm(false)
    fetchData()
  } catch (err) {
    if (err.response && err.response.status === 422) {
      Object.assign(errors, err.response.data.errors)
    } else {
      console.error(err)
      alert('Error saving lease.')
    }
  } finally {
    submitting.value = false
  }
}

const editLease = (lease) => {
  form.id = lease.id
  form.unit_id = lease.unit_id
  form.tenant_id = lease.tenant_id
  form.lease_start_date = lease.lease_start_date
  form.lease_end_date = lease.lease_end_date
  form.monthly_rent = lease.monthly_rent
  form.grace_period = lease.grace_period
  form.late_fee_penalty = lease.late_fee_penalty
  form.notes = lease.notes
}

const resetForm = (clearMessage = true) => {
  Object.assign(form, {
    id: null,
    unit_id: '',
    tenant_id: '',
    lease_start_date: '',
    lease_end_date: '',
    monthly_rent: null,
    grace_period: 0,
    late_fee_penalty: 0,
    notes: ''
  })
  Object.keys(errors).forEach(k => delete errors[k])
  if (clearMessage) successMessage.value = ''
}
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
