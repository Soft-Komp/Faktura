<template>
  <div>
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Zarządzanie artykułami
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Zarządzaj katalogiem produktów i usług
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="showCreateModal = true"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Dodaj artykuł
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
            <input
              id="search"
              v-model="filters.search"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nazwa, kod..."
            />
          </div>
          <div>
            <label for="stawka_vat" class="block text-sm font-medium text-gray-700 mb-2">Stawka VAT</label>
            <select
              id="stawka_vat"
              v-model="filters.stawka_vat"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="0">0%</option>
              <option value="5">5%</option>
              <option value="8">8%</option>
              <option value="23">23%</option>
            </select>
          </div>
          <div>
            <label for="aktywny" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              id="aktywny"
              v-model="filters.aktywny"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="true">Aktywne</option>
              <option value="false">Nieaktywne</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
            >
              Wyczyść filtry
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="artykulyStore.loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Ładowanie artykułów...</p>
    </div>

    <!-- Error -->
    <div v-else-if="artykulyStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Błąd</h3>
          <p class="mt-1 text-sm text-red-700">{{ artykulyStore.error }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="filteredArtykuly.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak artykułów</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od dodania pierwszego artykułu.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="artykul in filteredArtykuly" :key="artykul.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ artykul.nazwa }}</h3>
                    <span 
                      v-if="!artykul.aktywny" 
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                    >
                      Nieaktywny
                    </span>
                  </div>
                  <p class="text-sm text-gray-500">Kod: {{ artykul.kod_artykulu }}</p>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>{{ formatPrice(artykul.cena_netto) }} PLN netto</span>
                    <span>{{ formatPrice(artykul.cena_brutto) }} PLN brutto</span>
                    <span>VAT: {{ artykul.stawka_vat }}%</span>
                    <span>{{ artykul.jednostka }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="editArtykul(artykul)"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Edytuj
              </button>
              <button
                @click="confirmDelete(artykul)"
                class="text-red-600 hover:text-red-800 text-sm font-medium"
              >
                Usuń
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Create/Edit Modal -->
    <ArtykulModal
      v-if="showCreateModal || showEditModal"
      :artykul="selectedArtykul"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onArtykulSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmDeleteModal
      v-if="showDeleteModal"
      :title="`Usuń artykuł: ${selectedArtykul?.nazwa}`"
      :message="'Czy na pewno chcesz usunąć ten artykuł? Ta operacja jest nieodwracalna.'"
      @confirm="deleteArtykul"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useArtykulyStore } from '../../stores/artykuly';
import ArtykulModal from '../../components/Artykuly/ArtykulModal.vue';
import ConfirmDeleteModal from '../../components/Shared/ConfirmDeleteModal.vue';

export default {
  name: 'ArtykulyIndex',
  components: {
    ArtykulModal,
    ConfirmDeleteModal
  },
  setup() {
    const artykulyStore = useArtykulyStore();
    
    // State
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedArtykul = ref(null);
    
    // Filters
    const filters = ref({
      search: '',
      stawka_vat: '',
      aktywny: ''
    });
    
    // Computed
    const filteredArtykuly = computed(() => {
      let result = artykulyStore.artykuly;
      
      if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(a => 
          a.nazwa.toLowerCase().includes(search) ||
          a.kod_artykulu?.toLowerCase().includes(search)
        );
      }
      
      if (filters.value.stawka_vat) {
        result = result.filter(a => a.stawka_vat == filters.value.stawka_vat);
      }
      
      if (filters.value.aktywny) {
        const isActive = filters.value.aktywny === 'true';
        result = result.filter(a => a.aktywny === isActive);
      }
      
      return result;
    });
    
    // Methods
    const fetchArtykuly = async () => {
      await artykulyStore.fetchArtykuly();
    };
    
    const clearFilters = () => {
      filters.value.search = '';
      filters.value.stawka_vat = '';
      filters.value.aktywny = '';
    };
    
    const editArtykul = (artykul) => {
      selectedArtykul.value = { ...artykul };
      showEditModal.value = true;
    };
    
    const confirmDelete = (artykul) => {
      selectedArtykul.value = artykul;
      showDeleteModal.value = true;
    };
    
    const deleteArtykul = async () => {
      const result = await artykulyStore.deleteArtykul(selectedArtykul.value.id);
      if (result.success) {
        showDeleteModal.value = false;
        selectedArtykul.value = null;
      }
    };
    
    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedArtykul.value = null;
    };
    
    const onArtykulSaved = () => {
      closeModal();
      fetchArtykuly();
    };
    
    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2);
    };
    
    // Lifecycle
    onMounted(() => {
      fetchArtykuly();
    });
    
    return {
      artykulyStore,
      showCreateModal,
      showEditModal,
      showDeleteModal,
      selectedArtykul,
      filters,
      filteredArtykuly,
      clearFilters,
      editArtykul,
      confirmDelete,
      deleteArtykul,
      closeModal,
      onArtykulSaved,
      formatPrice
    };
  }
}
</script>