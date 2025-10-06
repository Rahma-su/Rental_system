<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-6xl mx-auto space-y-6">
      <h2 class="text-2xl font-semibold mb-4">Wallet Management</h2>

      <!-- Wallets Table -->
      <div>
        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">#</th>
              <th class="border px-2 py-1">Tenant</th>
              <th class="border px-2 py-1">Balance</th>
              <th class="border px-2 py-1">Deposit Balance</th>
              <th class="border px-2 py-1">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(wallet, index) in wallets"
              :key="wallet.id"
              class="hover:bg-gray-50"
            >
              <td class="border px-2 py-1">{{ index + 1 }}</td>

              <td class="border px-2 py-1">
                {{ wallet.tenant?.full_name || wallet.tenant?.business_name || "N/A" }}
              </td>

              <td class="border px-2 py-1 text-green-600 font-semibold">
                ${{ formatNumber(wallet.balance) }}
              </td>

              <td class="border px-2 py-1 text-blue-600 font-semibold">
                ${{ formatNumber(wallet.deposit_balance) }}
              </td>

              <td class="border px-2 py-1 flex space-x-2">
                <button
                  @click="viewTransactions(wallet)"
                  class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                >
                  View Transactions
                </button>
              </td>
            </tr>

            <tr v-if="!loading && wallets.length === 0">
              <td colspan="5" class="text-center text-gray-500 py-4">
                No wallets found.
              </td>
            </tr>

            <tr v-if="loading">
              <td colspan="5" class="text-center text-gray-500 py-4">
                Loading wallets...
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Transactions Modal -->
      <div
        v-if="selectedWallet"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
          <h3 class="text-xl font-semibold mb-3">
            Transactions for
            {{ selectedWallet.tenant?.full_name || selectedWallet.tenant?.business_name || "N/A" }}
          </h3>

          <div v-if="loadingTransactions" class="text-gray-500">
            Loading transactions...
          </div>

          <div v-else-if="transactions.length === 0" class="text-gray-500">
            No transactions found.
          </div>

          <table
            v-else
            class="w-full border border-gray-300 text-sm mb-4 mt-2"
          >
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Type</th>
                <th class="border px-2 py-1">Amount</th>
                <th class="border px-2 py-1">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(tx, index) in transactions"
                :key="tx.id"
                class="hover:bg-gray-50"
              >
                <td class="border px-2 py-1">{{ index + 1 }}</td>
                <td class="border px-2 py-1 capitalize">{{ tx.type }}</td>
                <td
                  class="border px-2 py-1"
                  :class="tx.type === 'credit' ? 'text-green-600' : 'text-red-600'"
                >
                  {{ tx.type === 'credit' ? '+' : '-' }}${{ formatNumber(tx.amount) }}
                </td>
                <td class="border px-2 py-1">{{ formatDate(tx.created_at) }}</td>
              </tr>
            </tbody>
          </table>

          <div class="text-right">
            <button
              @click="selectedWallet = null"
              class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Layout from "../components/Layout.vue";
import api from "../services/api.js";

const wallets = ref([]);
const transactions = ref([]);
const selectedWallet = ref(null);
const loading = ref(false);
const loadingTransactions = ref(false);

// 🧮 Format helper (safe for strings or null)
const formatNumber = (num) => {
  const value = parseFloat(num);
  return isNaN(value) ? "0.00" : value.toFixed(2);
};

// 📅 Format date helper
const formatDate = (date) => {
  if (!date) return "—";
  return new Date(date).toLocaleDateString();
};

// 🧾 Fetch all wallets
const fetchWallets = async () => {
  loading.value = true;
  try {
    const res = await api.get("/wallets");
    wallets.value = res.data;
  } catch (err) {
    console.error("Error fetching wallets:", err);
  } finally {
    loading.value = false;
  }
};

// 💳 Fetch transactions for a wallet
const viewTransactions = async (wallet) => {
  selectedWallet.value = wallet;
  loadingTransactions.value = true;
  try {
    const res = await api.get(`/wallets/${wallet.id}/transactions`);
    transactions.value = res.data;
  } catch (err) {
    console.error("Error fetching transactions:", err);
    transactions.value = [];
  } finally {
    loadingTransactions.value = false;
  }
};

onMounted(fetchWallets);
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
