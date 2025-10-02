<template>
  <Layout>
    <div class="p-6 space-y-6">
      <!-- Loading / Error -->
      <div v-if="loading" class="text-gray-600">Loading dashboard...</div>
      <div v-if="error" class="text-red-600">{{ error }}</div>

      <!-- Top Metrics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6" v-else>
        <div class="bg-white p-4 rounded-lg shadow flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">TOTAL UNITS</p>
            <p class="text-xl font-bold text-gray-900">{{ totalUnits }}</p>
          </div>
          <img src="../assets/icons/units.png" alt="Units" class="bg-gray-200 p-2 rounded-full w-10 h-10" />
        </div>

        <div class="bg-white p-4 rounded-lg shadow flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">TOTAL TENANTS</p>
            <p class="text-xl font-bold text-gray-900">{{ totalTenants }}</p>
          </div>
          <img src="../assets/icons/tenant.png" alt="Tenants" class="bg-gray-200 p-2 rounded-full w-10 h-10" />
        </div>

        <div class="bg-white p-4 rounded-lg shadow flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">TOTAL LEASE</p>
            <p class="text-xl font-bold text-gray-900">{{ totalLease }}</p>
          </div>
          <img src="../assets/icons/lease.png" alt="Lease" class="bg-gray-200 p-2 rounded-full w-10 h-10" />
        </div>

        <div class="bg-white p-4 rounded-lg shadow flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">TOTAL MAINTENANCE</p>
            <p class="text-xl font-bold text-gray-900">{{ totalMaintenance }}</p>
          </div>
          <img src="../assets/icons/maintenance.png" alt="Maintenance" class="bg-gray-200 p-2 rounded-full w-10 h-10" />
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Layout from '../components/Layout.vue'
import api from '../services/api.js' // your axios instance

const units = ref([])
const tenants = ref([])
const leases = ref([])
const maintenances = ref([])

const loading = ref(false)
const error = ref(null)

// helper: support res.data OR res.data.data OR direct array
function extractData(res) {
  if (!res) return []
  if (Array.isArray(res.data)) return res.data
  if (Array.isArray(res.data?.data)) return res.data.data
  return Array.isArray(res.data) ? res.data : (res.data ?? [])
}

const fetchData = async () => {
  loading.value = true
  error.value = null

  try {
    // use Promise.allSettled to tolerate missing endpoints (like maintenances)
    const results = await Promise.allSettled([
      api.get('/units'),
      api.get('/tenantform'),   // <-- correct endpoint for tenants in your backend
      api.get('/leases'),
      api.get('/maintenances')  // may 404 if not implemented; handled below
    ])

    // set each collection only if the call succeeded
    const [unitsRes, tenantsRes, leasesRes, maintRes] = results

    units.value = unitsRes.status === 'fulfilled' ? extractData(unitsRes.value) : []
    tenants.value = tenantsRes.status === 'fulfilled' ? extractData(tenantsRes.value) : []
    leases.value = leasesRes.status === 'fulfilled' ? extractData(leasesRes.value) : []
    maintenances.value = maintRes.status === 'fulfilled' ? extractData(maintRes.value) : []

    // log any failures so you can inspect in console
    results.forEach((r, i) => {
      if (r.status === 'rejected') {
        const endpoints = ['/units','/tenantform','/leases','/maintenances']
        console.warn(`Dashboard: failed to fetch ${endpoints[i]}`, r.reason)
      }
    })
  } catch (err) {
    console.error('fetchData error', err)
    error.value = 'Failed to load dashboard data. Check console for details.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)

// computed safe totals (guard against non-array responses)
const totalUnits = computed(() => Array.isArray(units.value) ? units.value.length : 0)
const totalTenants = computed(() => Array.isArray(tenants.value) ? tenants.value.length : 0)
const totalLease = computed(() => Array.isArray(leases.value) ? leases.value.length : 0)
const totalMaintenance = computed(() => Array.isArray(maintenances.value) ? maintenances.value.length : 0)
</script>

<style scoped>
/* keep your styles or tailwind utility classes */
</style>
