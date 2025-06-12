<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          Zmień rolę użytkownika
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
          Zmień rolę użytkownika <strong>{{ user?.name }}</strong>:
        </p>
        
        <div>
          <label for="rola" class="block text-sm font-medium text-gray-700 mb-2">
            Obecna rola: <span class="font-semibold">{{ getRoleLabel(user?.rola) }}</span>
          </label>
          <select
            id="rola"
            v-model="selectedRole"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Wybierz nową rolę</option>
            <option value="admin">Administrator</option>
            <option value="ksiegowy">Księgowy</option>
            <option value="klient">Klient</option>
          </select>
        </div>
      </div>

      <!-- Warning -->
      <div v-if="selectedRole" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
        <div class="flex">
          <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Uwaga</h3>
            <p class="mt-1 text-sm text-yellow-700">
              Zmiana roli użytkownika wpłynie na jego uprawnienia w systemie.
            </p>
          </div>
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
          @click="handleChangeRole"
          :disabled="loading || !selectedRole || selectedRole === user?.rola"
          type="button"
          class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Zmienianie...</span>
          <span v-else>Zmień rolę</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useUsersStore } from '../../stores/users';

export default {
  name: 'ChangeRoleModal',
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  emits: ['close', 'changed'],
  setup(props, { emit }) {
    const usersStore = useUsersStore();
    
    const selectedRole = ref('');
    const loading = ref(false);
    const error = ref(null);
    
    const getRoleLabel = (role) => {
      const labels = {
        admin: 'Administrator',
        ksiegowy: 'Księgowy',
        klient: 'Klient'
      };
      return labels[role] || role;
    };
    
    const handleChangeRole = async () => {
      if (!selectedRole.value || selectedRole.value === props.user.rola) return;
      
      loading.value = true;
      error.value = null;
      
      try {
        const result = await usersStore.changeRole(props.user.id, selectedRole.value);
        
        if (result.success) {
          emit('changed');
        } else {
          error.value = result.error;
        }
      } catch (err) {
        error.value = 'Wystąpił nieoczekiwany błąd';
      } finally {
        loading.value = false;
      }
    };
    
    return {
      selectedRole,
      loading,
      error,
      getRoleLabel,
      handleChangeRole
    };
  }
}
</script>