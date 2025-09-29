<template>
  <Layout>
    <div class="p-6 max-w-6xl mx-auto bg-white rounded shadow">
      <h2 class="text-2xl font-semibold mb-4">Bill Management</h2>

      <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex items-center gap-2">
          <input v-model="searchQuery" placeholder="Search by tenant, unit or year/month"
                 class="border p-2 rounded w-64" type="text" />
          <button @click="fetchBills" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-500">
            Refresh
          </button>
        </div>

        <div class="flex items-center gap-2">
          <button @click="openGenerateModal(null)"
                  class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
            Generate Bill (choose lease)
          </button>
        </div>
      </div>

      <div v-if="loading" class="text-gray-600 mb-4">Loading bills...</div>
      <div v-if="error" class="text-red-600 mb-4">{{ error }}</div>

      <table v-if="!loading" class="table-auto w-full border border-gray-300 rounded text-sm">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-3 py-2">#</th>
            <th class="border px-3 py-2">Lease</th>
            <th class="border px-3 py-2">Unit</th>
            <th class="border px-3 py-2">Tenant</th>
            <th class="border px-3 py-2">Amount</th>
            <th class="border px-3 py-2">Period</th>
            <th class="border px-3 py-2">Type</th>
            <th class="border px-3 py-2">Status</th>
            <th class="border px-3 py-2">Paid</th>
            <th class="border px-3 py-2">Remaining</th>
            <th class="border px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(bill, i) in filteredBills" :key="bill.id" class="hover:bg-gray-50">
            <td class="border px-3 py-2">{{ i + 1 }}</td>
            <td class="border px-3 py-2">{{ bill.lease?.id ?? '-' }}</td>
            <td class="border px-3 py-2">{{ bill.lease?.unit?.unit_name ?? '-' }}</td>
            <td class="border px-3 py-2">{{ bill.lease?.tenant?.full_name ?? '-' }}</td>
            <td class="border px-3 py-2">{{ formatCurrency(bill.amount ?? 0) }}</td>
            <td class="border px-3 py-2">{{ formatPeriod(bill.billing_period_start, bill.billing_period_end) }}</td>
            <td class="border px-3 py-2">{{ bill.billing_type ?? '-' }}</td>
            <td class="border px-3 py-2">{{ bill.status ?? '-' }}</td>
            <td class="border px-3 py-2">{{ formatCurrency(bill.paid_amount ?? 0) }}</td>
            <td class="border px-3 py-2">{{ formatCurrency((bill.amount ?? 0) - (bill.paid_amount ?? 0)) }}</td>
            <td class="border px-3 py-2 whitespace-nowrap flex gap-2">
              <button v-if="bill.status !== 'paid'" @click="openPayModal(bill)"
                      class="text-blue-600 hover:underline">Pay</button>
              <button v-if="bill.status === 'paid'" @click="downloadPDF(bill)"
                      class="text-purple-600 hover:underline">Download PDF</button>
              <button @click="deleteBill(bill.id)" class="text-red-600 hover:underline">Delete</button>
            </td>
          </tr>

          <tr v-if="bills.length === 0">
            <td colspan="11" class="text-center text-gray-500 py-4">No bills found.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pay Bill Modal -->
      <div v-if="showPayModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded p-6 max-w-md w-full shadow-lg">
          <h3 class="text-xl font-semibold mb-3">Pay Bill</h3>

          <div class="mb-3 text-gray-700 font-medium">
            Wallet Balance: {{ formatCurrency(walletBalance) }}
          </div>

          <div class="mb-3 text-gray-700">
            <div>Bill Total: {{ formatCurrency(selectedBill?.amount ?? 0) }}</div>
            <div>Paid: {{ formatCurrency(selectedBill?.paid_amount ?? 0) }}</div>
            <div>Remaining: {{ formatCurrency(remainingAmount) }}</div>
            <div>Per-month amount: {{ formatCurrency(perMonthAmount) }}</div>
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Months to pay</label>

            <div v-if="maxPayableMonths > 0">
              <select v-model.number="monthsToPay" class="w-full border p-2 rounded">
                <option v-for="n in maxPayableMonths" :key="n" :value="n">
                  {{ n }} month(s) — {{ formatCurrency(n * perMonthAmount) }}
                </option>
              </select>
              <div class="text-sm text-gray-500 mt-1">
                Max months you can pay now: {{ maxPayableMonths }} (limited by wallet & remaining)
              </div>
            </div>

            <div v-else class="text-sm text-red-600">
              Insufficient wallet balance to pay even one full month.
            </div>
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Amount to be paid</label>
            <input :value="formatCurrency(payAmount)" readonly class="w-full border p-2 rounded bg-gray-50" />
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Payment Method</label>
            <input type="text" v-model="payForm.payment_method" class="w-full border p-2 rounded" disabled />
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Remarks</label>
            <select v-model="payForm.remarks" class="w-full border p-2 rounded">
              <option disabled value="">Select Type</option>
              <option value="rent">Rent</option>
              <option value="electricity">Electricity</option>
              <option value="water">Water</option>
              <option value="parking">Parking</option>
              <option value="water_and_electric">Water & Electric</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Reference Number</label>
            <input v-model="payForm.reference_number" type="text" class="w-full border p-2 rounded" />
          </div>

          <div class="flex justify-end gap-2">
            <button @click="closePayModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
            <button :disabled="paying || maxPayableMonths < 1" @click="payBill" class="px-4 py-2 bg-green-600 text-white rounded">
              {{ paying ? 'Paying...' : 'Pay' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Generate Bill Modal (unchanged) -->
      <div v-if="showGenerateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded p-6 max-w-md w-full shadow-lg">
          <h3 class="text-xl font-semibold mb-3">Generate Bill for Lease</h3>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Select Lease</label>
            <select v-model="generateLeaseId" class="w-full border p-2 rounded">
              <option disabled value="">Select lease</option>
              <option v-for="lease in leases" :key="lease.id" :value="lease.id">
                {{ lease.id }} — {{ lease.unit?.unit_name ?? '—' }} / {{ lease.tenant?.full_name ?? '—' }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label class="block mb-1 font-medium">Billing Type</label>
            <select v-model="generateForm.billing_type" class="w-full border p-2 rounded">
              <option disabled value="">Select Type</option>
              <option value="rent">Rent</option>
              <option value="water_and_electric">Water + Electric</option>
              <option value="water">Water</option>
              <option value="electric">Electric</option>
              <option value="parking">Parking</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
            <div>
              <label class="block mb-1 font-medium">Year</label>
              <select v-model.number="generateForm.year" class="w-full border p-2 rounded">
                <option disabled value="">Year</option>
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
            <div>
              <label class="block mb-1 font-medium">Month</label>
              <select v-model.number="generateForm.month" class="w-full border p-2 rounded">
                <option disabled value="">Month</option>
                <option v-for="m in 12" :key="m" :value="m">{{ m }}</option>
              </select>
            </div>
            <div>
              <label class="block mb-1 font-medium">Months Count</label>
              <input v-model.number="generateForm.months_count" type="number" min="1" max="12" class="w-full border p-2 rounded" />
            </div>
          </div>

          <div class="flex justify-end gap-2">
            <button @click="closeGenerateModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
            <button :disabled="generating" @click="generateForLease" class="px-4 py-2 bg-blue-600 text-white rounded">
              {{ generating ? 'Generating...' : 'Generate' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import Layout from '../components/Layout.vue'
import api from '../services/api.js'
import jsPDF from 'jspdf' // keep if installed

// state
const bills = ref([])
const leases = ref([])
const loading = ref(false)
const error = ref(null)

const showPayModal = ref(false)
const showGenerateModal = ref(false)
const selectedBill = ref(null)
const paying = ref(false)
const generating = ref(false)
const generateLeaseId = ref('')
const walletBalance = ref(0)

// payment-specific
const monthsToPay = ref(1)
const perMonthAmount = ref(0)
const payForm = ref({
  amount: 0,
  payment_method: 'wallet',
  reference_number: '',
  remarks: ''
})

// generate form
const generateForm = ref({
  year: new Date().getFullYear(),
  month: new Date().getMonth() + 1,
  months_count: 1,
  billing_type: ''
})

const searchQuery = ref('')
const years = []
const currentYear = new Date().getFullYear()
for (let y = currentYear - 1; y <= currentYear + 5; y++) years.push(y)

function extractData(res) {
  return Array.isArray(res?.data) ? res.data : (res?.data?.data ?? res?.data ?? [])
}

/* ---------- fetch data ---------- */
const fetchBills = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get('/tenant-bills')
    bills.value = extractData(res)
  } catch (err) {
    console.error('fetchBills error', err)
    error.value = 'Failed to load bills.'
  } finally {
    loading.value = false
  }
}

const fetchLeases = async () => {
  try {
    const res = await api.get('/leases')
    leases.value = extractData(res)
  } catch (err) {
    console.error('fetchLeases err', err)
  }
}

onMounted(() => {
  fetchBills()
  fetchLeases()
})

/* ---------- helpers ---------- */
const formatCurrency = (v) => {
  if (v === null || v === undefined) return '-'
  const num = Number(v)
  if (Number.isNaN(num)) return v
  return num.toFixed(2)
}

const formatPeriod = (start, end) => {
  if (!start && !end) return '-'
  if (start && end) {
    // show YYYY-MM → YYYY-MM for clarity
    const s = typeof start === 'string' ? start.substr(0,7) : start
    const e = typeof end === 'string' ? end.substr(0,7) : end
    return `${s} → ${e}`
  }
  return start || end
}

function inferMonthsCountFromPeriod(start, end) {
  if (!start || !end) return 1
  try {
    const s = new Date(start)
    const e = new Date(end)
    const months = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth()) + 1
    return Math.max(1, months)
  } catch {
    return 1
  }
}

/* ---------- search ---------- */
const filteredBills = computed(() => {
  if (!searchQuery.value) return bills.value
  const q = searchQuery.value.toLowerCase()
  return bills.value.filter(b => {
    const unit = b.lease?.unit?.unit_name?.toString().toLowerCase() ?? ''
    const tenant = b.lease?.tenant?.full_name?.toString().toLowerCase() ?? ''
    const year = (b.billing_year ?? '').toString()
    const month = (b.billing_month ?? '').toString()
    return unit.includes(q) || tenant.includes(q) || year.includes(q) || month.includes(q)
  })
})

/* ---------- Pay modal logic ---------- */

// compute remaining, per-month and max months payable
const remainingAmount = computed(() => {
  const total = Number(selectedBill.value?.amount ?? 0)
  const paid = Number(selectedBill.value?.paid_amount ?? 0)
  return Math.max(0, total - paid)
})
const computedMonthsCount = computed(() => {
  if (!selectedBill.value) return 1;
  const bc = selectedBill.value.months_count;
  if (bc && Number(bc) > 0) return Number(bc);
  return inferMonthsCountFromPeriod(selectedBill.value.billing_period_start, selectedBill.value.billing_period_end);
});
// const computedMonthsCount = computed(() => {
//   if (!selectedBill.value) return 1
//   const bc = selectedBill.value.months_count
//   if (bc && Number(bc) > 0) return Number(bc)
//   return inferMonthsCountFromPeriod(selectedBill.value.billing_period_start, selectedBill.value.billing_period_end)
// })
watch([() => selectedBill.value, () => walletBalance.value], () => {
  if (!selectedBill.value) {
    monthsToPay.value = 1;
    perMonthAmount.value = 0;
    payForm.value.amount = 0;
    return;
  }

  const monthsCount = computedMonthsCount.value; // Ensure this reflects the correct number of months
  perMonthAmount.value = Number(((selectedBill.value.amount ?? 0) / Math.max(1, monthsCount)).toFixed(2));
  const maxPay = maxPayableMonths.value;
  monthsToPay.value = maxPay > 0 ? Math.min(1, maxPay) : 0;
  payForm.value.amount = Number((monthsToPay.value * perMonthAmount.value).toFixed(2));
});
// watch([() => selectedBill.value, () => walletBalance.value], () => {
  
//   if (!selectedBill.value) {
//     monthsToPay.value = 1
//     perMonthAmount.value = 0
//     payForm.value.amount = 0
//     return
//   }
//   const monthsCount = computedMonthsCount.value
//   perMonthAmount.value = Number(((selectedBill.value.amount ?? 0) / Math.max(1, monthsCount)).toFixed(2))
//   // default monthsToPay = 1 or max if remaining smaller
//   const maxPay = maxPayableMonths.value
//   monthsToPay.value = maxPay > 0 ? Math.min(1, maxPay) : 0
//   payForm.value.amount = Number((monthsToPay.value * perMonthAmount.value).toFixed(2))
//   payForm.value.remarks = selectedBill.value.billing_type ?? ''
// })

// maximum months payable given wallet + remaining
const maxPayableMonths = computed(() => {
  const per = perMonthAmount.value || 0
  if (per <= 0) return 0
  const canByWallet = Math.floor((walletBalance.value ?? 0) / per)
  const canByRemaining = Math.floor((remainingAmount.value ?? 0) / per)
  return Math.max(0, Math.min(canByWallet, canByRemaining))
})

const payAmount = computed(() => {
  return Number(((monthsToPay.value || 0) * perMonthAmount.value).toFixed(2))
})

watch(monthsToPay, (n) => {
  payForm.value.amount = payAmount.value
})

/* ---------- open pay modal ---------- */
// const openPayModal = async (bill) => {
//   selectedBill.value = bill
//   // determine tenant id (support multiple shapes)
//   const tenantId = bill.tenant_id ?? bill.lease?.tenant_id ?? bill.lease?.tenant?.id
//   // compute per-month (watch will set perMonth when wallet fetched and selectedBill set)
//   perMonthAmount.value = 0
//   monthsToPay.value = 1
//   payForm.value = {
//     amount: 0,
//     payment_method: 'wallet',
//     reference_number: '',
//     remarks: bill.billing_type ?? ''
//   }

//   // fetch wallet by tenant route you have
//   try {
//     if (!tenantId) throw new Error('Tenant id not found for this bill')
//     const res = await api.get(`/wallets/tenant/${tenantId}`)
//     // response likely the wallet object
//     walletBalance.value = Number(res.data.balance ?? 0)
//   } catch (err) {
//     console.error('fetch walletBalance err', err)
//     walletBalance.value = 0
//   }
const openPayModal = async (bill) => {
  selectedBill.value = bill;
  const tenantId = bill.tenant_id ?? bill.lease?.tenant_id ?? bill.lease?.tenant?.id;

  perMonthAmount.value = 0;
  monthsToPay.value = 1;
  payForm.value = {
    amount: 0,
    payment_method: 'wallet',
    reference_number: '',
    remarks: bill.billing_type ?? ''
  };

  try {
    if (!tenantId) throw new Error('Tenant id not found for this bill');
    const res = await api.get(`/wallets/tenant/${tenantId}`);
    walletBalance.value = Number(res.data.balance ?? 0);
  } catch (err) {
    console.error('fetch walletBalance err', err);
    walletBalance.value = 0;
  }

  const monthsCount = computedMonthsCount.value;
  perMonthAmount.value = Number(((bill.amount ?? 0) / Math.max(1, monthsCount)).toFixed(2));
  monthsToPay.value = maxPayableMonths.value > 0 ? 1 : 0;
  payForm.value.amount = Number((monthsToPay.value * perMonthAmount.value).toFixed(2));

  showPayModal.value = true;
};
  // set perMonth and default monthsToPay (after walletBalance fetched watch will update)
  // const monthsCount = computedMonthsCount.value
  // perMonthAmount.value = Number(((bill.amount ?? 0) / Math.max(1, monthsCount)).toFixed(2))
  // // set monthsToPay to 1 or 0 depending on funds
  // monthsToPay.value = maxPayableMonths.value > 0 ? 1 : 0
  // payForm.value.amount = Number((monthsToPay.value * perMonthAmount.value).toFixed(2))

  // showPayModal.value = true
// }

/* ---------- close pay modal ---------- */
const closePayModal = () => {
  showPayModal.value = false
  selectedBill.value = null
  walletBalance.value = 0
  monthsToPay.value = 1
  perMonthAmount.value = 0
}

/* ---------- pay bill API ---------- */
const payBill = async () => {
  if (!selectedBill.value) return
  if (monthsToPay.value < 1) {
    alert('You must select at least 1 month to pay.')
    return
  }
  if (payForm.value.amount <= 0) {
    alert('Invalid pay amount.')
    return
  }

  // final safety checks
  if (payForm.value.amount > walletBalance.value + 0.001) {
    alert('Wallet balance changed — not enough funds.')
    return
  }

  paying.value = true
  try {
    const payload = {
      amount: payForm.value.amount,
      payment_method: payForm.value.payment_method,
      reference_number: payForm.value.reference_number,
      remarks: payForm.value.remarks
    }
    await api.post(`/tenant-bills/${selectedBill.value.id}/pay`, payload)
    // refresh
    await fetchBills()
    closePayModal()
    alert('Payment successful')
  } catch (err) {
    console.error('payBill err', err)
    alert(err?.response?.data?.message || 'Failed to pay bill')
  } finally {
    paying.value = false
  }
}

/* ---------- delete & download ---------- */
const deleteBill = async (id) => {
  if (!confirm('Delete this bill?')) return
  try {
    await api.delete(`/tenant-bills/${id}`)
    fetchBills()
  } catch (err) {
    console.error('deleteBill err', err)
    alert('Failed to delete bill.')
  }
}

const downloadPDF = (bill) => {
  const doc = new jsPDF()
  doc.setFontSize(18)
  doc.text('Tenant Bill', 14, 22)
  doc.setFontSize(12)
  doc.text(`Tenant: ${bill.lease?.tenant?.full_name ?? '-'}`, 14, 40)
  doc.text(`Unit: ${bill.lease?.unit?.unit_name ?? '-'}`, 14, 50)
  doc.text(`Lease ID: ${bill.lease?.id ?? '-'}`, 14, 60)
  doc.text(`Amount: ${formatCurrency(bill.amount)}`, 14, 70)
  doc.text(`Period: ${formatPeriod(bill.billing_period_start, bill.billing_period_end)}`, 14, 80)
  doc.text(`Type: ${bill.billing_type}`, 14, 90)
  doc.text(`Status: ${bill.status}`, 14, 100)
  doc.text(`Paid On: ${bill.updated_at}`, 14, 110)
  doc.save(`bill_${bill.id}.pdf`)
}

/* ---------- generate ---------- */
const openGenerateModal = (leaseOrNull) => {
  if (leaseOrNull && leaseOrNull.id) generateLeaseId.value = leaseOrNull.id
  else generateLeaseId.value = ''
  showGenerateModal.value = true
}

const closeGenerateModal = () => {
  showGenerateModal.value = false
  generateLeaseId.value = ''
  generateForm.value = {
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    months_count: 'null',
    billing_type: ''
  }
}

const generateForLease = async () => {
  if (!generateLeaseId.value) {
    alert('Please select a lease.')
    return
  }
  if (!generateForm.value.year || !generateForm.value.month || !generateForm.value.months_count || !generateForm.value.billing_type) {
    alert('Please fill year, month, months count and billing type.')
    return
  }

  generating.value = true
  try {
    const selectedLease = leases.value.find(l => l.id === generateLeaseId.value)
    if (!selectedLease || !selectedLease.tenant_id) {
      alert("Selected lease does not have a tenant_id. Check backend /leases API.")
      generating.value = false
      return
    }

    await api.post('/tenant-bills/generate-for-lease', {
      lease_id: generateLeaseId.value,
      tenant_id: selectedLease.tenant_id,
      year: generateForm.value.year,
      month: generateForm.value.month,
      months_count: generateForm.value.months_count,
      billing_type: generateForm.value.billing_type
    })
    alert('Bill(s) generated successfully.')
    closeGenerateModal()
    fetchBills()
  } catch (err) {
    console.error('generateForLease err', err)
    const msg = err?.response?.data?.message || 'Failed to generate bill(s). Check backend logs.'
    alert(msg)
  } finally {
    generating.value = false
  }
}
</script>

<style scoped>
.table-col { min-width: 120px; }
</style>
