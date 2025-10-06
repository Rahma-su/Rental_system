<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto space-y-6">
      <h2 class="text-2xl font-semibold mb-4">Wallet Transactions</h2>

      <!-- Add/Edit Transaction Form -->
      <div class="border p-4 rounded bg-gray-50">
        <h3 class="text-lg font-semibold mb-2">
          {{ form.id ? 'Edit Transaction' : 'Add New Transaction' }}
        </h3>
        <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">

          <!-- Wallet Dropdown -->
          <select v-model.number="form.wallet_id" class="form-input" required>
            <option disabled value="">Select Wallet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">
              Tenant #{{ wallet.tenant_id }} — Balance: {{ wallet.balance }}
            </option>
          </select>

          <!-- Lease Dropdown -->
          <select v-model.number="form.lease_id" class="form-input">
            <option disabled value="">Select Lease (optional)</option>
            <option v-for="lease in leases" :key="lease.id" :value="lease.id">
              Lease #{{ lease.id }}
            </option>
          </select>

          <!-- Transaction Type -->
          <select v-model="form.type" class="form-input" required>
            <option value="credit">Credit</option>
            <option value="debit">Debit</option>
          </select>

          <!-- Amount -->
          <input v-model.number="form.amount" type="number" step="0.01" placeholder="Amount" class="form-input" required />

          <!-- Payment Method -->
          <input v-model="form.payment_method" type="text" placeholder="Payment Method" class="form-input" />

          <!-- Payment Date -->
          <input v-model="form.payment_date" type="date" class="form-input" required />

          <!-- Reference Number -->
          <input v-model="form.reference_number" type="text" placeholder="Reference Number" class="form-input" />

          <!-- Remarks / Payment Type -->
          <select v-model="form.remarks" class="form-input" required>
            <option disabled value="">Select Payment Type</option>
            <option v-for="type in paymentTypes" :key="type" :value="type">
              {{ typeLabel(type) }}
            </option>
          </select>

          <!-- Description -->
          <input v-model="form.description" type="text" placeholder="Description" class="form-input md:col-span-3" />

          <!-- Submit Button -->
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 md:col-span-3">
            {{ form.id ? 'Update' : 'Add' }} Transaction
          </button>
        </form>
      </div>

      <!-- Transactions Table -->
      <div>
        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">#</th>
              <th class="border px-2 py-1">Wallet</th>
              <th class="border px-2 py-1">Lease</th>
              <th class="border px-2 py-1">Type</th>
              <th class="border px-2 py-1">Amount</th>
              <th class="border px-2 py-1">Payment Method</th>
              <th class="border px-2 py-1">Date</th>
              <th class="border px-2 py-1">Reference</th>
              <th class="border px-2 py-1">Remarks</th> <!-- show payment type -->
              <th class="border px-2 py-1">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, index) in transactions" :key="tx.id" class="hover:bg-gray-50">
              <td class="border px-2 py-1">{{ index + 1 }}</td>
              <td class="border px-2 py-1">#{{ tx.wallet_id }}</td>
              <td class="border px-2 py-1">{{ tx.lease?.id || '-' }}</td>
              <td class="border px-2 py-1 capitalize">{{ tx.type }}</td>
              <td class="border px-2 py-1">{{ formatAmount(tx.amount) }}</td>
              <td class="border px-2 py-1">{{ tx.payment_method || '-' }}</td>
              <td class="border px-2 py-1">{{ tx.payment_date }}</td>
              <td class="border px-2 py-1">{{ tx.reference_number || '-' }}</td>
              <td class="border px-2 py-1 capitalize">{{ typeLabel(tx.remarks) }}</td>
              <td class="border px-2 py-1 flex space-x-2">
                <button @click="editTransaction(tx)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</button>
                <button @click="deleteTransaction(tx.id)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
              </td>
            </tr>
            <tr v-if="transactions.length === 0">
              <td colspan="10" class="text-center text-gray-500 py-4">No transactions found.</td>
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

const wallets = ref([]);
const leases = ref([]);
const transactions = ref([]);

// Payment type options
const paymentTypes = ['rent','waterandelectricity','water','electricity','parking','other'];

const form = ref({
  id: null,
  wallet_id: "",
  lease_id: "",
  type: "credit",
  amount: null,
  payment_method: "",
  payment_date: "",
  reference_number: "",
  description: "",
  remarks: "",
});

// Helper to display readable label
const typeLabel = (type) => {
  const labels = {
    rent: "Rent",
    waterandelectricity: "Water & Electricity",
    water: "Water",
    electricity: "Electricity",
    parking: "Parking",
    other: "Other"
  };
  return labels[type] || type;
};
// for amount
const formatAmount = (amount) => {
  const n = Number(amount);
  return isNaN(n) ? "0.00" : n.toFixed(2);
};

// Fetch wallets, leases, and transactions
const fetchData = async () => {
  try {
    const [walletRes, leaseRes] = await Promise.all([
      api.get("/wallets"),
      api.get("/leases"),
    ]);
    wallets.value = walletRes.data;
    leases.value = leaseRes.data;
  } catch (err) {
    console.error("Error fetching wallets or leases:", err);
  }
};

const fetchTransactions = async () => {
  try {
    const allTx = [];
    for (let wallet of wallets.value) {
      const res = await api.get(`/wallets/${wallet.id}/transactions`);
      allTx.push(...res.data);
    }
    transactions.value = allTx;
  } catch (err) {
    console.error("Error fetching transactions:", err);
    transactions.value = [];
  }
};

// Submit Add/Edit Transaction
const submitForm = async () => {
  try {
    if (form.value.id) {
      await api.put(`/wallet-transactions/${form.value.id}`, form.value);
    } else {
      await api.post(`/wallets/${form.value.wallet_id}/transactions`, form.value);
    }
    resetForm();
    fetchTransactions();
  } catch (err) {
    console.error("Error saving transaction:", err);
  }
};

// Edit transaction
const editTransaction = (tx) => {
  form.value = { ...tx };
};

// Delete transaction
const deleteTransaction = async (id) => {
  if (!confirm("Are you sure you want to delete this transaction?")) return;
  try {
    await api.delete(`/wallet-transactions/${id}`);
    fetchTransactions();
  } catch (err) {
    console.error("Failed to delete transaction:", err);
  }
};

// Reset form
const resetForm = () => {
  form.value = {
    id: null,
    wallet_id: "",
    lease_id: "",
    type: "credit",
    amount: null,
    payment_method: "",
    payment_date: "",
    reference_number: "",
    description: "",
    remarks: "",
  };
};

onMounted(async () => {
  await fetchData();
  await fetchTransactions();
});
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
