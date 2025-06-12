<template>
  <div>
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Zarządzanie firmami
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          {{ authStore.isKsiegowy ? 'Zarządzaj klientami i firmami' : 'Zarządzaj firmami w systemie' }}
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
        <button
          v-if="authStore.isKsiegowy"
          @click="showCreateKlientModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Dodaj klienta
        </button>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Dodaj firmę
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button
          @click="activeTab = 'firmy'"
          :class="[
            activeTab === 'firmy'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          Wszystkie firmy
        </button>
        <button
          v-if="authStore.isKsiegowy"
          @click="activeTab = 'klienci'"
          :class="[
            activeTab === 'klienci'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          Moi klienci
        </button>
      </nav>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
            <input
              id="search"
              v-model="filters.search"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nazwa, NIP..."
            />
          </div>
          <div v-if="activeTab === 'firmy'">
            <label for="typ" class="block text-sm font-medium text-gray-700 mb-2">Typ</label>
            <select
              id="typ"
              v-model="filters.typ"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="ksiegowa">Księgowa</option>
              <option value="klient">Klient</option>
              <option value="zwykla">Zwykła</option>
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
    <div v-if="firmyStore.loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Ładowanie...</p>
    </div>

    <!-- Error -->
    <div v-else-if="firmyStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Błąd</h3>
          <p class="mt-1 text-sm text-red-700">{{ firmyStore.error }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="currentList.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak firm</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od dodania pierwszej firmy.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="firma in currentList" :key="firma.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ firma.nazwa }}</h3>
                    <span 
                      v-if="firma.typ"
                      :class="getTypeBadgeClass(firma.typ)"
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ getTypeLabel(firma.typ) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-500">{{ firma.nazwa_pelna }}</p>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>NIP: {{ firma.nip }}</span>
                    <span>{{ firma.miejscowosc }}</span>
                    <span v-if="firma.email">{{ firma.email }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="editFirma(firma)"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Edytuj
              </button>
              <button
                @click="confirmDelete(firma)"
                class="text-red-600 hover:text-red-800 text-sm font-medium"
              >
                Usuń
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Create/Edit Firma Modal -->
    <FirmaModal
      v-if="showCreateModal || showEditModal"
      :firma="selectedFirma"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onFirmaSaved"
    />

    <!-- Create Klient Modal -->
    <KlientModal
      v-if="showCreateKlientModal"
      @close="showCreateKlientModal = false"
      @saved="onKlientSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmDeleteModal
      v-if="showDeleteModal"
      :title="`Usuń firmę: ${selectedFirma?.nazwa}`"
      :message="'Czy na pewno chcesz usunąć tę firmę? Ta operacja jest nieodwracalna.'"
      @confirm="deleteFirma"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import { useFirmyStore } from '../../stores/firmy';
import { useAuthStore } from '../../stores/auth';
import FirmaModal from '../../components/Firmy/FirmaModal.vue';
import KlientModal from '../../components/Firmy/KlientModal.vue';
import ConfirmDeleteModal from '../../components/Shared/ConfirmDeleteModal.vue';

export default {
  name: 'FirmyIndex',
  components: {
    FirmaModal,
    KlientModal,
    ConfirmDeleteModal
  },
  setup() {
    const firmyStore = useFirmyStore();
    const authStore = useAuthStore();
    
    // State
    const activeTab = ref('firmy');
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showCreateKlientModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedFirma = ref(null);
    
    // Filters
    const filters = ref({
      search: '',
      typ: ''
    });
    
    // Computed
    const currentList = computed(() => {
      let list = activeTab.value === 'klienci' ? firmyStore.klienci : firmyStore.firmy;
      
      if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        list = list.filter(f => 
          f.nazwa.toLowerCase().includes(search) ||
          f.nazwa_pelna.toLowerCase().includes(search) ||
          f.nip?.toLowerCase().includes(search)
        );
      }
      
      if (filters.value.typ && activeTab.value === 'firmy') {
        list = list.filter(f => f.typ === filters.value.typ);
      }
      
      return list;
    });
    
    // Methods
    const fetchData = async () => {
      if (activeTab.value === 'klienci' && authStore.isKsiegowy) {
        await firmyStore.fetchKlienci();
      } else {
        await firmyStore.fetchFirmy();
      }
    };
    
    const clearFilters = () => {
      filters.value.search = '';
      filters.value.typ = '';
    };
    
    const editFirma = (firma) => {
      selectedFirma.value = { ...firma };
      showEditModal.value = true;
    };
    
    const confirmDelete = (firma) => {
      selectedFirma.value = firma;
      showDeleteModal.value = true;
    };
    
    const deleteFirma = async () => {
      const result = await firmyStore.deleteFirma(selectedFirma.value.id);
      if (result.success) {
        showDeleteModal.value = false;
        selectedFirma.value = null;
        fetchData();
      }
    };
    
    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedFirma.value = null;
    };
    
    const onFirmaSaved = () => {
      closeModal();
      fetchData();
    };
    
    const onKlientSaved = () => {
      showCreateKlientModal.value = false;
      fetchData();
    };
    
    const getTypeLabel = (typ) => {
      const labels = {
        ksiegowa: 'Księgowa',
        klient: 'Klient',
        zwykla: 'Zwykła'
      };
      return labels[typ] || typ;
    };
    
    const getTypeBadgeClass = (typ) => {
      const classes = {
        ksiegowa: 'bg-purple-100 text-purple-800',
        klient: 'bg-green-100 text-green-800',
        zwykla: 'bg-gray-100 text-gray-800'
      };
      return classes[typ] || 'bg-gray-100 text-gray-800';
    };
    
    // Watch tab changes
    watch(activeTab, () => {
      fetchData();
    });
    
    // Lifecycle
    onMounted(() => {
      fetchData();
    });
    
    return {
      firmyStore,
      authStore,
      activeTab,
      showCreateModal,
      showEditModal,
      showCreateKlientModal,
      showDeleteModal,
      selectedFirma,
      filters,
      currentList,
      clearFilters,
      editFirma,
      confirmDelete,
      deleteFirma,
      closeModal,
      onFirmaSaved,
      onKlientSaved,
      getTypeLabel,
      getTypeBadgeClass
    };
  }
}
</script>