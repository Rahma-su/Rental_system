<template>
  <Layout>
    <div class="p-6 bg-white rounded shadow-md max-w-3xl mx-auto">
      <h2 class="text-xl font-semibold mb-4">
        {{ form.id ? 'Edit Tenant' : 'Register Tenant' }}
      </h2>

      

      <form @submit.prevent="submitForm" novalidate>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label class="block mb-1 font-medium">Full Name <span class="text-red-500">*</span></label>
            <input v-model="form.full_name" type="text" class="form-input" required />
            <p v-if="errors.full_name" class="text-red-600 text-sm">{{ errors.full_name[0] }}</p>
          </div>

          <!-- Business Name -->
          <div>
            <label class="block mb-1 font-medium">Business Name <span class="text-red-500">*</span></label>
            <input v-model="form.business_name" type="text" class="form-input" required />
            <p v-if="errors.business_name" class="text-red-600 text-sm">{{ errors.business_name[0] }}</p>
          </div>

          <!-- Gender -->
          <div>
            <label class="block mb-1 font-medium">Gender</label>
            <select v-model="form.gender" class="form-input">
              <option disabled value="">Select</option>
              <option>Male</option>
              <option>Female</option>
            </select>
            <p v-if="errors.gender" class="text-red-600 text-sm">{{ errors.gender[0] }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label class="block mb-1 font-medium">Phone Number <span class="text-red-500">*</span></label>
            <input v-model="form.phone" type="tel" class="form-input" required />
            <p v-if="errors.phone" class="text-red-600 text-sm">{{ errors.phone[0] }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="block mb-1 font-medium">Email Address</label>
            <input v-model="form.email" type="email" class="form-input" />
            <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
          </div>

          <!-- National ID -->
          <div>
            <label class="block mb-1 font-medium">National ID</label>
            <input v-model="form.national_id" type="text" class="form-input" />
          </div>

          <!-- TIN No -->
          <div>
            <label class="block mb-1 font-medium">TIN No</label>
            <input v-model="form.tin_no" type="text" class="form-input" />
          </div>

          <!-- Nationality -->
          <div>
            <label class="block mb-1 font-medium">Nationality</label>
            <input v-model="form.nationality" type="text" class="form-input" />
          </div>

          <!-- Emergency Contact Name -->
          <div>
            <label class="block mb-1 font-medium">Representative Contact Name</label>
            <input v-model="form.emergency_contact_name" type="text" class="form-input" />
          </div>

          <!-- Emergency Contact Phone -->
          <div>
            <label class="block mb-1 font-medium">Representative Contact Phone</label>
            <input v-model="form.emergency_contact_phone" type="tel" class="form-input" />
          </div>

          <!-- Address -->
          <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Address</label>
            <textarea v-model="form.address" class="form-input"></textarea>
          </div>

          <!-- Notes -->
          <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Notes</label>
            <textarea v-model="form.notes" class="form-input"></textarea>
          </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex items-center space-x-3">
          <button type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                  :disabled="submitting">
            {{ form.id ? 'Update' : 'Register' }}
          </button>
          <button @click="closeForm" type="button" class="text-gray-600 hover:underline">Cancel</button>
        </div>
      </form>
      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ successMessage }}
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import Layout from "../components/Layout.vue";
import api from "../services/api.js"; // no change here

const props = defineProps({ editTenant: Object });
const emit = defineEmits(['created', 'updated', 'close']);

const form = reactive({
  id: null,
  full_name: '',
  business_name: '',
  gender: '',
  phone: '',
  email: '',
  national_id: '',
  tin_no: '',
  nationality: 'Ethiopian',
  emergency_contact_name: '',
  emergency_contact_phone: '',
  address: '',
  notes: ''
});

const errors = reactive({});
const submitting = ref(false);

// <-- Success message only added
const successMessage = ref('');

watch(() => props.editTenant, (val) => {
  if (val) {
    Object.assign(form, val);
    Object.keys(errors).forEach(key => delete errors[key]);
  }
}, { immediate: true });

const submitForm = async () => {
  submitting.value = true;
  Object.keys(errors).forEach(key => delete errors[key]);
  successMessage.value = ''; // reset previous message

  try {
    if (form.id) {
      await api.put(`/tenantform/${form.id}`, form);
      emit('updated');
      successMessage.value = 'Tenant successfully updated!'; // added success message
    } else {
      await api.post('/tenantform', form);
      emit('created');
      successMessage.value = 'Tenant successfully registered!'; // added success message
    }
    resetForm(false); // don't clear message
  } catch (err) {
    if (err.response && err.response.status === 422) {
      Object.assign(errors, err.response.data.errors);
    } else {
      alert('Error saving tenant. Check console.');
      console.error(err);
    }
  } finally {
    submitting.value = false;
  }
};

const resetForm = (clearMessage = true) => {
  Object.assign(form, {
    id: null,
    full_name: '',
    business_name: '',
    gender: '',
    phone: '',
    email: '',
    national_id: '',
    tin_no: '',
    nationality: 'Ethiopian',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    address: '',
    notes: ''
  });
  Object.keys(errors).forEach(key => delete errors[key]);
  if (clearMessage) successMessage.value = '';
  emit('close');
};

const closeForm = () => resetForm();
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
