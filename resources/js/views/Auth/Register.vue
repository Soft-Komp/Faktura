<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Utwórz nowe konto
        </h2>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
            <input
              id="name"
              v-model="form.name"
              name="name"
              type="text"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Jan Kowalski"
            />
            <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</div>
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="jan@example.com"
            />
            <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</div>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Minimum 8 znaków"
            />
            <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</div>
          </div>
          
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potwierdź hasło</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Powtórz hasło"
            />
          </div>
          
          <div>
            <label for="rola" class="block text-sm font-medium text-gray-700">Rola</label>
            <select
              id="rola"
              v-model="form.rola"
              name="rola"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option value="">Wybierz rolę</option>
              <option value="admin">Administrator</option>
              <option value="ksiegowy">Księgowy</option>
              <option value="klient">Klient</option>
            </select>
            <div v-if="errors.rola" class="text-red-500 text-xs mt-1">{{ errors.rola[0] }}</div>
          </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            <span v-if="loading">Rejestracja...</span>
            <span v-else>Zarejestruj się</span>
          </button>
        </div>

        <div class="text-center">
          <router-link to="/login" class="text-blue-600 hover:text-blue-500">
            Masz już konto? Zaloguj się
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

export default {
  name: 'Register',
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    
    const form = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      rola: ''
    });
    
    const errors = ref({});
    
    const loading = computed(() => authStore.loading);
    const error = computed(() => authStore.error);
    
    const handleRegister = async () => {
      errors.value = {};
      
      const result = await authStore.register(form.value);
      
      if (result.success) {
        router.push('/dashboard');
      } else if (result.errors) {
        errors.value = result.errors;
      }
    };
    
    return {
      form,
      errors,
      loading,
      error,
      handleRegister
    };
  }
}
</script>