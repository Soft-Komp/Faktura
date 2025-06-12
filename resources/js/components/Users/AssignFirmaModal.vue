<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          Przypisz do firmy
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

      <!-- Content -->
      <div class="mb-4">
        <p class="text-sm text-gray-600 mb-4">
          Przypisz użytkownika <strong>{{ user?.name }}</strong> do firmy:
        </p>
        
        <div>
          <label for="firma_id" class="block text-sm font-medium text-gray-700 mb-2">
            Wybierz firmę *
          </label>
          <select
            id="firma_id"
            v-model="selectedFirmaId"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Wybierz firmę</option>
            <option 
              v-for="firma in dostepneFirmy" 
              :key="firma.id" 
              :value="firma.id"
            >
              {{ firma.nazwa }}
            </option>
          </select>
        </div>
      </div>

      <!-- Error -->
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ error }}
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-3">
        <button
          @click="$emit('close')"
          type="button"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Anuluj
        </button>
        <button
          @click="handleAssign"
          :disabled="loading || !selectedFirmaId"
          type="button"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Przypisywanie...</span>
          <span v-else>Przypisz</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useUsersStore } from '../../stores/users';
import { useFirmyStore } from '../../stores/firmy';

export default {
  name: 'AssignFirmaModal',
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  emits: ['close', 'assigned'],
  setup(props, { emit }) {
    const usersStore = useUsersStore();
    const firmyStore = useFirmyStore();
    
    const selectedFirmaId = ref('');
    const dostepneFirmy = ref([]);
    const loading = ref(false);
    const error = ref(null);
    
    const loadFirmy = async () => {
      const result = await firmyStore.fetchFirmy();
      if (result.success) {
        dostepneFirmy.value = firmyStore.firmy;
      }
    };
    
    const handleAssign = async () => {
      if (!selectedFirmaId.value) return;
      
      loading.value = true;
      error.value = null;
      
      try {
        const result = await usersStore.assignToFirma(props.user.id, selectedFirmaId.value);
        
        if (result.success) {
          emit('assigned');
        } else {
          error.value = result.error;
        }
      } catch (err) {
        error.value = 'Wystąpił nieoczekiwany błąd';
      } finally {
        loading.value = false;
      }
    };
    
    onMounted(() => {
      loadFirmy();
    });
    
    return {
      selectedFirmaId,
      dostepneFirmy,
      loading,
      error,
      handleAssign
    };
  }
}
</script>