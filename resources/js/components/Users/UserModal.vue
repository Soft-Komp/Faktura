<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isEditMode ? 'Edytuj użytkownika' : 'Dodaj nowego użytkownika' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Nazwa i email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              Imię i nazwisko *
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.name }"
            />
            <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.email }"
            />
            <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</div>
          </div>
        </div>

        <!-- Hasło -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Hasło {{ isEditMode ? '(zostaw puste aby nie zmieniać)' : '*' }}
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            :required="!isEditMode"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-300': errors.password }"
          />
          <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</div>
        </div>

        <!-- Rola i firma -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="rola" class="block text-sm font-medium text-gray-700 mb-2">
              Rola *
            </label>
            <select
              id="rola"
              v-model="form.rola"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.rola }"
            >
              <option value="">Wybierz rolę</option>
              <option value="admin">Administrator</option>
              <option value="ksiegowy">Księgowy</option>
              <option value="klient">Klient</option>
            </select>
            <div v-if="errors.rola" class="text-red-500 text-xs mt-1">{{ errors.rola[0] }}</div>
          </div>

          <div>
            <label for="firma_id" class="block text-sm font-medium text-gray-700 mb-2">
              Firma
            </label>
            <select
              id="firma_id"
              v-model="form.firma_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.firma_id }"
            >
              <option value="">Brak przypisania</option>
              <option 
                v-for="firma in dostepneFirmy" 
                :key="firma.id" 
                :value="firma.id"
              >
                {{ firma.nazwa }}
              </option>
            </select>
            <div v-if="errors.firma_id" class="text-red-500 text-xs mt-1">{{ errors.firma_id[0] }}</div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Anuluj
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">Zapisywanie...</span>
            <span v-else>{{ isEditMode ? 'Aktualizuj' : 'Dodaj' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import { useUsersStore } from '../../stores/users';
import { useFirmyStore } from '../../stores/firmy';

export default {
  name: 'UserModal',
  props: {
    user: {
      type: Object,
      default: null
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const usersStore = useUsersStore();
    const firmyStore = useFirmyStore();
    
    // Form state
    const form = ref({
      name: '',
      email: '',
      password: '',
      rola: '',
      firma_id: ''
    });
    
    const errors = ref({});
    const loading = ref(false);
    const error = ref(null);
    const dostepneFirmy = ref([]);
    
    // Computed
    const isEditMode = computed(() => props.isEdit && props.user);
    
    // Methods
    const resetForm = () => {
      form.value = {
        name: '',
        email: '',
        password: '',
        rola: '',
        firma_id: ''
      };
      errors.value = {};
      error.value = null;
    };
    
    const loadFirmy = async () => {
      const result = await firmyStore.fetchFirmy();
      if (result.success) {
        dostepneFirmy.value = firmyStore.firmy;
      }
    };
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      error.value = null;
      
      try {
        let result;
        const userData = { ...form.value };
        
        // Usuń puste hasło przy edycji
        if (isEditMode.value && !userData.password) {
          delete userData.password;
        }
        
        // Przekonwertuj puste firma_id na null
        if (!userData.firma_id) {
          userData.firma_id = null;
        }
        
        if (isEditMode.value) {
          result = await usersStore.updateUser(props.user.id, userData);
        } else {
          result = await usersStore.createUser(userData);
        }
        
        if (result.success) {
          emit('saved');
        } else {
          error.value = result.error;
          if (result.errors) {
            errors.value = result.errors;
          }
        }
      } catch (err) {
        error.value = 'Wystąpił nieoczekiwany błąd';
      } finally {
        loading.value = false;
      }
    };
    
    // Watch
    watch(() => props.user, (newUser) => {
      if (newUser) {
        form.value = {
          name: newUser.name || '',
          email: newUser.email || '',
          password: '',
          rola: newUser.rola || '',
          firma_id: newUser.firma_id || ''
        };
      } else {
        resetForm();
      }
    }, { immediate: true });
    
    // Lifecycle
    onMounted(() => {
      loadFirmy();
    });
    
    return {
      form,
      errors,
      loading,
      error,
      dostepneFirmy,
      isEditMode,
      handleSubmit
    };
  }
}
</script>