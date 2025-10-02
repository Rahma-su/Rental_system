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
          <h2 class="text-2xl font-bold">Welcome Back</h2>
          <p class="text-sm">Glad to see you here!</p>
        </div>
      </div>

      <!-- Right side (login form) -->
      <div class="w-1/2 flex items-center justify-center p-8" style="background-color: #D1D5D8;">
        <div class="w-full max-w-md">
          <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">LOGIN</h2>
          <p class="text-gray-600 text-center mb-6">Enter your credentials to access your account</p>

          <form @submit.prevent="login" class="space-y-4">
            <!-- Email -->
            <div>
              <label class="block mb-1 font-medium text-gray-700">Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
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
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:outline-none transition"
                required
              />
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="w-full py-3 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 transition"
            >
              Login
            </button>
          </form>

          <p class="mt-6 text-center text-gray-600">
            Don't have an account?
            <router-link to="/register" class="text-gray-700 font-medium hover:underline">
              Register
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
  email: "",
  password: "",
});

const login = async () => {
  try {
    const res = await axios.post("http://127.0.0.1:8000/api/login", form.value);
    localStorage.setItem("token", res.data.token);
    router.push("/dashboard");
  } catch (err) {
    console.error(err);
    alert("Invalid credentials!");
  }
};
</script>
