<template>
  <div>
    <h1>Payments Page</h1>
    <ul>
      <li v-for="payment in payments" :key="payment.id">
        Tenant: {{ payment.tenant_name }} - Amount: ${{ payment.amount }} - Status: {{ payment.status }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "Payments",
  data() {
    return {
      payments: [],
    };
  },
  mounted() {
    // fetch payments from API later
  },
};
</script>

<style scoped>
h1 { color: #333; margin-bottom: 1rem; }
li { margin: 0.5rem 0; }
</style>
