<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isEditMode ? 'Edytuj artykuł' : 'Dodaj nowy artykuł' }}
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
        <!-- Nazwa i kod -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-2">
              Nazwa artykułu *
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
            <label for="kod_artykulu" class="block text-sm font-medium text-gray-700 mb-2">
              Kod artykułu *
            </label>
            <input
              id="kod_artykulu"
              v-model="form.kod_artykulu"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.kod_artykulu }"
            />
            <div v-if="errors.kod_artykulu" class="text-red-500 text-xs mt-1">{{ errors.kod_artykulu[0] }}</div>
          </div>
        </div>

        <!-- Jednostka i stawka VAT -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="jednostka" class="block text-sm font-medium text-gray-700 mb-2">
              Jednostka *
            </label>
            <select
              id="jednostka"
              v-model="form.jednostka"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.jednostka }"
            >
              <option value="szt.">szt.</option>
              <option value="kg">kg</option>
              <option value="m">m</option>
              <option value="m2">m²</option>
              <option value="m3">m³</option>
              <option value="l">l</option>
              <option value="godz.">godz.</option>
              <option value="usł.">usł.</option>
            </select>
            <div v-if="errors.jednostka" class="text-red-500 text-xs mt-1">{{ errors.jednostka[0] }}</div>
          </div>

          <div>
            <label for="stawka_vat" class="block text-sm font-medium text-gray-700 mb-2">
              Stawka VAT (%) *
            </label>
            <select
              id="stawka_vat"
              v-model="form.stawka_vat"
              required
              @change="calculateBrutto"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.stawka_vat }"
            >
              <option value="0">0%</option>
              <option value="5">5%</option>
              <option value="8">8%</option>
              <option value="23">23%</option>
            </select>
            <div v-if="errors.stawka_vat" class="text-red-500 text-xs mt-1">{{ errors.stawka_vat[0] }}</div>
          </div>
        </div>

        <!-- Ceny -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="cena_netto" class="block text-sm font-medium text-gray-700 mb-2">
              Cena netto (PLN) *
            </label>
            <input
              id="cena_netto"
              v-model="form.cena_netto"
              type="number"
              step="0.01"
              min="0"
              required
              @input="calculateBrutto"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.cena_netto }"
            />
            <div v-if="errors.cena_netto" class="text-red-500 text-xs mt-1">{{ errors.cena_netto[0] }}</div>
          </div>

          <div>
            <label for="cena_brutto" class="block text-sm font-medium text-gray-700 mb-2">
              Cena brutto (PLN) *
            </label>
            <input
              id="cena_brutto"
              v-model="form.cena_brutto"
              type="number"
              step="0.01"
              min="0"
              required
              @input="calculateNetto"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': errors.cena_brutto }"
            />
            <div v-if="errors.cena_brutto" class="text-red-500 text-xs mt-1">{{ errors.cena_brutto[0] }}</div>
          </div>
        </div>

        <!-- Opis -->
        <div>
          <label for="opis" class="block text-sm font-medium text-gray-700 mb-2">
            Opis
          </label>
          <textarea
            id="opis"
            v-model="form.opis"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-300': errors.opis }"
          ></textarea>
          <div v-if="errors.opis" class="text-red-500 text-xs mt-1">{{ errors.opis[0] }}</div>
        </div>

        <!-- Aktywny -->
        <div class="flex items-center">
          <input
            id="aktywny"
            v-model="form.aktywny"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label for="aktywny" class="ml-2 block text-sm text-gray-900">
            Artykuł aktywny
          </label>
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
import { useArtykulyStore } from '../../stores/artykuly';

export default {
  name: 'ArtykulModal',
  props: {
    artykul: {
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
    const artykulyStore = useArtykulyStore();
    
    // Form state
    const form = ref({
      nazwa: '',
      kod_artykulu: '',
      jednostka: 'szt.',
      stawka_vat: '23',
      cena_netto: '',
      cena_brutto: '',
      opis: '',
      aktywny: true
    });
    
    const errors = ref({});
    const loading = ref(false);
    const error = ref(null);
    
    // Computed
    const isEditMode = computed(() => props.isEdit && props.artykul);
    
    // Methods
    const resetForm = () => {
      form.value = {
        nazwa: '',
        kod_artykulu: '',
        jednostka: 'szt.',
        stawka_vat: '23',
        cena_netto: '',
        cena_brutto: '',
        opis: '',
        aktywny: true
      };
      errors.value = {};
      error.value = null;
    };
    
    const calculateBrutto = () => {
      if (form.value.cena_netto && form.value.stawka_vat) {
        const netto = parseFloat(form.value.cena_netto);
        const vat = parseFloat(form.value.stawka_vat);
        form.value.cena_brutto = (netto * (1 + vat / 100)).toFixed(2);
      }
    };
    
    const calculateNetto = () => {
      if (form.value.cena_brutto && form.value.stawka_vat) {
        const brutto = parseFloat(form.value.cena_brutto);
        const vat = parseFloat(form.value.stawka_vat);
        form.value.cena_netto = (brutto / (1 + vat / 100)).toFixed(2);
      }
    };
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      error.value = null;
      
      try {
        let result;
        
        if (isEditMode.value) {
          result = await artykulyStore.updateArtykul(props.artykul.id, form.value);
        } else {
          result = await artykulyStore.createArtykul(form.value);
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
    watch(() => props.artykul, (newArtykul) => {
      if (newArtykul) {
        form.value = { ...newArtykul };
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
      calculateBrutto,
      calculateNetto,
      handleSubmit
    };
  }
}
</script>