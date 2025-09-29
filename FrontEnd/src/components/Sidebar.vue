<template>
  <div class="sidebar">
    <ul>
      <li><router-link to="/">Dashboard</router-link></li>
      <!-- <li><router-link to="/tenants">Tenants</router-link></li>
      <li><router-link to="/rooms">Rooms</router-link></li> -->
      <!-- <li><router-link to="/properties">Properties</router-link></li>
      <li><router-link to="/rentals">Rentals</router-link></li>
      <li><router-link to="/rent-types">Rent Types</router-link></li>
      <li><router-link to="/maintenance">Maintenance</router-link></li> -->
    </ul>
  </div>
</template>

<style>
.sidebar {
  width: 200px;
  background-color: #1a1a1a;
  min-height: 100vh;
  padding: 1rem;
  color: white;
}
.sidebar a {
  display: block;
  margin: 0.5rem 0;
  color: white;
  text-decoration: none;
}
</style>
