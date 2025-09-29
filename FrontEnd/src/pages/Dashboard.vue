<template>
  <Layout>
    
    <div class="stats">
      <div class="card">
        <h2>Total Properties</h2>
        <p>{{ propertiesCount }}</p>
      </div>
      <div class="card">
        <h2>Total Rooms</h2>
        <p>{{ roomsCount }}</p>
      </div>
      <div class="card">
        <h2>Total Tenants</h2>
        <p>{{ tenantsCount }}</p>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from "../components/Layout.vue";
import api from "../services/api.js";

export default {
  components: { Layout },
  data() {
    return {
      propertiesCount: 0,
      roomsCount: 0,
      tenantsCount: 0,
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        const properties = await api.get("properties");
        const rooms = await api.get("rooms");
        const tenants = await api.get("tenants");

        this.propertiesCount = properties.data.length;
        this.roomsCount = rooms.data.length;
        this.tenantsCount = tenants.data.length;
      } catch (error) {
        console.error("Error fetching stats:", error);
      }
    },
  },
};
</script>

<style>
.stats {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}
.card {
  flex: 1;
  background-color: #1a1a1a;
  color: white;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
}
</style>
