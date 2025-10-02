<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <!-- Container -->
    <div class="flex w-3/4 max-w-4xl rounded-2xl shadow-lg overflow-hidden">
      
      <!-- Left side with image -->
      <div
        class="w-1/2 relative bg-cover bg-center"
        :style="{ backgroundImage: `url(${buildingImage})` }"
      >
        <div class="absolute bottom-6 right-6 text-white bg-black bg-opacity-40 p-3 rounded">
          <h2 class="text-2xl font-bold">Welcome</h2>
          <p class="text-sm">Create your account</p>
        </div>
      </div>

      <!-- Right side (register form) -->
      <div class="w-1/2 flex items-center justify-center p-6" style="background-color: #D1D5D8;">
        <div class="w-full max-w-md">
          <h2 class="text-3xl font-bold text-gray-800 mb-3 text-center">Register</h2>
          <p class="text-gray-600 text-center mb-5">Create a new account to get started</p>

          <form @submit.prevent="register" class="space-y-3">
            <!-- Name -->
            <div>
              <label class="block mb-1 font-medium text-gray-700">Name</label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Admin"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
                required
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block mb-1 font-medium text-gray-700">Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="admin@gmail.com"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
                required
              />
            </div>

            <!-- Password -->
            <div>
              <label class="block mb-1 font-medium text-gray-700">Password</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="********"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
                required
              />
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block mb-1 font-medium text-gray-700">Confirm Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="********"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
                required
              />
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="w-full py-2.5 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 transition"
            >
              Register
            </button>
          </form>

          <p class="mt-5 text-center text-gray-600">
            Already have an account?
            <router-link to="/login" class="text-gray-700 font-medium hover:underline">
              Login
            </router-link>
          </p>
        </div>
      </div>

    </div>
  </div>
</template>


<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import buildingImage from "../assets/images/building.jpg"; // adjust extension if needed

const router = useRouter();

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: ""
});

const register = async () => {
  try {
    const res = await axios.post("http://127.0.0.1:8000/api/register", form.value);
    localStorage.setItem("token", res.data.token);
    router.push("/dashboard");
  } catch (err) {
    if (err.response && err.response.status === 422) {
      console.error("Validation errors:", err.response.data.errors);
      alert("Validation failed: " + JSON.stringify(err.response.data.errors));
    } else {
      console.error(err);
      alert("Registration failed!");
    }
  }
};
</script>
