<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isEditMode ? 'Edytuj odbiorcę' : 'Dodaj nowego odbiorcę' }}
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
        <!-- Nazwa -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-2">
              Nazwa skrócona *
            </label>
            <input
              id="nazwa"
              v-model="form.nazwa"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.nazwa }"
            />
            <div v-if="errors.nazwa" class="text-red-500 text-xs mt-1">{{ errors.nazwa[0] }}</div>
          </div>

          <div>
            <label for="nazwa_pelna" class="block text-sm font-medium text-gray-700 mb-2">
              Nazwa pełna *
            </label>
            <input
              id="nazwa_pelna"
              v-model="form.nazwa_pelna"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.nazwa_pelna }"
            />
            <div v-if="errors.nazwa_pelna" class="text-red-500 text-xs mt-1">{{ errors.nazwa_pelna[0] }}</div>
          </div>
        </div>

        <!-- Adres -->
        <div>
          <label for="adres" class="block text-sm font-medium text-gray-700 mb-2">
            Adres *
          </label>
          <input
            id="adres"
            v-model="form.adres"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-300': errors.adres }"
          />
          <div v-if="errors.adres" class="text-red-500 text-xs mt-1">{{ errors.adres[0] }}</div>
        </div>

        <!-- Kod pocztowy i miejscowość -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="kod_pocztowy" class="block text-sm font-medium text-gray-700 mb-2">
              Kod pocztowy *
            </label>
            <input
              id="kod_pocztowy"
              v-model="form.kod_pocztowy"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.kod_pocztowy }"
            />
            <div v-if="errors.kod_pocztowy" class="text-red-500 text-xs mt-1">{{ errors.kod_pocztowy[0] }}</div>
          </div>

          <div>
            <label for="miejscowosc" class="block text-sm font-medium text-gray-700 mb-2">
              Miejscowość *
            </label>
            <input
              id="miejscowosc"
              v-model="form.miejscowosc"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.miejscowosc }"
            />
            <div v-if="errors.miejscowosc" class="text-red-500 text-xs mt-1">{{ errors.miejscowosc[0] }}</div>
          </div>
        </div>

        <!-- NIP i typ odbiorcy -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
              NIP
            </label>
            <input
              id="nip"
              v-model="form.nip"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.nip }"
            />
            <div v-if="errors.nip" class="text-red-500 text-xs mt-1">{{ errors.nip[0] }}</div>
          </div>

          <div>
            <label for="typ_odbiorcy" class="block text-sm font-medium text-gray-700 mb-2">
              Typ odbiorcy *
            </label>
            <select
              id="typ_odbiorcy"
              v-model="form.typ_odbiorcy"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.typ_odbiorcy }"
            >
              <option value="">Wybierz typ</option>
              <option value="krajowy">Krajowy</option>
              <option value="ue">UE</option>
              <option value="pozaue">Poza UE</option>
            </select>
            <div v-if="errors.typ_odbiorcy" class="text-red-500 text-xs mt-1">{{ errors.typ_odbiorcy[0] }}</div>
          </div>
        </div>

        <!-- Telefon i email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="telefon" class="block text-sm font-medium text-gray-700 mb-2">
              Telefon
            </label>
            <input
              id="telefon"
              v-model="form.telefon"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.telefon }"
            />
            <div v-if="errors.telefon" class="text-red-500 text-xs mt-1">{{ errors.telefon[0] }}</div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.email }"
            />
            <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</div>
          </div>
        </div>

        <!-- Kraj -->
        <div>
          <label for="kraj" class="block text-sm font-medium text-gray-700 mb-2">
            Kraj *
          </label>
          <input
            id="kraj"
            v-model="form.kraj"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-300': errors.kraj }"
          />
          <div v-if="errors.kraj" class="text-red-500 text-xs mt-1">{{ errors.kraj[0] }}</div>
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
import { ref, computed, watch } from 'vue';
import { useOdbiorcyStore } from '../../stores/odbiorcy';

export default {
  name: 'OdbiorcaModal',
  props: {
    odbiorca: {
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
    const odbiorcyStore = useOdbiorcyStore();
    
    // Form state
    const form = ref({
      nazwa: '',
      nazwa_pelna: '',
      kod_pocztowy: '',
      miejscowosc: '',
      adres: '',
      nip: '',
      telefon: '',
      email: '',
      kraj: 'Polska',
      typ_odbiorcy: ''
    });
    
    const errors = ref({});
    const loading = ref(false);
    const error = ref(null);
    
    // Computed
    const isEditMode = computed(() => props.isEdit && props.odbiorca);
    
    // Methods
    const resetForm = () => {
      form.value = {
        nazwa: '',
        nazwa_pelna: '',
        kod_pocztowy: '',
        miejscowosc: '',
        adres: '',
        nip: '',
        telefon: '',
        email: '',
        kraj: 'Polska',
        typ_odbiorcy: ''
      };
      errors.value = {};
      error.value = null;
    };
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      error.value = null;
      
      try {
        let result;
        
        if (isEditMode.value) {
          result = await odbiorcyStore.updateOdbiorca(props.odbiorca.id, form.value);
        } else {
          result = await odbiorcyStore.createOdbiorca(form.value);
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
    watch(() => props.odbiorca, (newOdbiorca) => {
      if (newOdbiorca) {
        form.value = { ...newOdbiorca };
      } else {
        resetForm();
      }
    }, { immediate: true });
    
    return {
      form,
      errors,
      loading,
      error,
      isEditMode,
      handleSubmit
    };
  }
}
</script>