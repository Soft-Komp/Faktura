<template>
  <div>
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Zarządzanie dokumentami
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Faktury, rachunki i inne dokumenty handlowe
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
          Nowy dokument
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
            <input
              id="search"
              v-model="filters.search"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Numer, odbiorca..."
            />
          </div>
          <div>
            <label for="typ_dokumentu" class="block text-sm font-medium text-gray-700 mb-2">Typ dokumentu</label>
            <select
              id="typ_dokumentu"
              v-model="filters.typ_dokumentu"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="FV">Faktura VAT</option>
              <option value="FK">Faktura korygująca</option>
              <option value="FMP">Faktura VAT MP</option>
              <option value="PA">Paragon</option>
              <option value="WZ">WZ</option>
              <option value="PZ">PZ</option>
            </select>
          </div>
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              id="status"
              v-model="filters.status"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="robocza">Robocza</option>
              <option value="wystawiona">Wystawiona</option>
              <option value="wysłana">Wysłana</option>
              <option value="zapłacona">Zapłacona</option>
            </select>
          </div>
          <div>
            <label for="data_od" class="block text-sm font-medium text-gray-700 mb-2">Data od</label>
            <input
              id="data_od"
              v-model="filters.data_od"
              type="date"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label for="data_do" class="block text-sm font-medium text-gray-700 mb-2">Data do</label>
            <input
              id="data_do"
              v-model="filters.data_do"
              type="date"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 mr-2"
            >
              Wyczyść
            </button>
            <button
              @click="applyFilters"
              class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md"
            >
              Filtruj
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie dokumenty</dt>
                <dd class="text-lg font-medium text-gray-900">{{ dokumentyStore.dokumenty.length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wystawione</dt>
                <dd class="text-lg font-medium text-gray-900">{{ dokumentyWystawione.length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Robocze</dt>
                <dd class="text-lg font-medium text-gray-900">{{ dokumentyRobocze.length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                <span class="text-white font-semibold text-xs">PLN</span>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wartość w miesiącu</dt>
                <dd class="text-lg font-medium text-gray-900">{{ formatPrice(dokumentyStore.sumaWartosciMiesiaca) }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="dokumentyStore.loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Ładowanie dokumentów...</p>
    </div>

    <!-- Error -->
    <div v-else-if="dokumentyStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Błąd</h3>
          <p class="mt-1 text-sm text-red-700">{{ dokumentyStore.error }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="dokumentyStore.dokumenty.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak dokumentów</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od wystawienia pierwszego dokumentu.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="dokument in dokumentyStore.dokumenty" :key="dokument.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ dokument.numer }}</h3>
                    <span 
                      :class="getStatusBadgeClass(dokument.status)"
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ getStatusLabel(dokument.status) }}
                    </span>
                    <span 
                      :class="getTypeBadgeClass(dokument.typ_dokumentu)"
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ dokument.typ_dokumentu }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-500">{{ dokument.odbiorca?.nazwa }}</p>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>{{ formatDate(dokument.data_wystawienia) }}</span>
                    <span>{{ formatPrice(dokument.wartosc_brutto) }} {{ dokument.waluta }}</span>
                    <span v-if="dokument.firma">{{ dokument.firma.nazwa }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="generatePdf(dokument.id)"
                class="text-green-600 hover:text-green-800 text-sm font-medium"
                title="Pobierz PDF"
              >
                PDF
              </button>
              <button
                @click="editDokument(dokument)"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Edytuj
              </button>
              <button
                @click="confirmDelete(dokument)"
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
    <DokumentModal
      v-if="showCreateModal || showEditModal"
      :dokument="selectedDokument"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onDokumentSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmDeleteModal
      v-if="showDeleteModal"
      :title="`Usuń dokument: ${selectedDokument?.numer}`"
      :message="'Czy na pewno chcesz usunąć ten dokument? Ta operacja jest nieodwracalna.'"
      @confirm="deleteDokument"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useDokumentyStore } from '../../stores/dokumenty';
import DokumentModal from '../../components/Dokumenty/DokumentModal.vue';
import ConfirmDeleteModal from '../../components/Shared/ConfirmDeleteModal.vue';

export default {
  name: 'DokumentyIndex',
  components: {
    DokumentModal,
    ConfirmDeleteModal
  },
  setup() {
    const dokumentyStore = useDokumentyStore();
    
    // State
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedDokument = ref(null);
    
    // Filters
    const filters = ref({
      search: '',
      typ_dokumentu: '',
      status: '',
      data_od: '',
      data_do: ''
    });
    
    // Computed
    const dokumentyWystawione = computed(() => 
      dokumentyStore.dokumentyWedlugStatusu('wystawiona')
    );
    
    const dokumentyRobocze = computed(() => 
      dokumentyStore.dokumentyWedlugStatusu('robocza')
    );
    
    // Methods
    const fetchDokumenty = async () => {
      const filterParams = {};
      
      if (filters.value.typ_dokumentu) filterParams.typ_dokumentu = filters.value.typ_dokumentu;
      if (filters.value.status) filterParams.status = filters.value.status;
      if (filters.value.data_od) filterParams.data_od = filters.value.data_od;
      if (filters.value.data_do) filterParams.data_do = filters.value.data_do;
      
      await dokumentyStore.fetchDokumenty(filterParams);
    };
    
    const applyFilters = () => {
      fetchDokumenty();
    };
    
    const clearFilters = () => {
      filters.value = {
        search: '',
        typ_dokumentu: '',
        status: '',
        data_od: '',
        data_do: ''
      };
      fetchDokumenty();
    };
    
    const editDokument = async (dokument) => {
  // Pobierz pełne dane dokumentu z pozycjami
  const result = await dokumentyStore.fetchDokument(dokument.id);
  if (result.success) {
    selectedDokument.value = result.data;
    showEditModal.value = true;
  }
};
    
    const confirmDelete = (dokument) => {
      selectedDokument.value = dokument;
      showDeleteModal.value = true;
    };
    
    const deleteDokument = async () => {
      const result = await dokumentyStore.deleteDokument(selectedDokument.value.id);
      if (result.success) {
        showDeleteModal.value = false;
        selectedDokument.value = null;
      }
    };
    
    const generatePdf = async (id) => {
      await dokumentyStore.generatePdf(id);
    };
    
    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedDokument.value = null;
    };
    
    const onDokumentSaved = () => {
      closeModal();
      fetchDokumenty();
    };
    
    const formatPrice = (price) => {
      return parseFloat(price || 0).toFixed(2);
    };
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('pl-PL');
    };
    
    const getStatusLabel = (status) => {
      const labels = {
        robocza: 'Robocza',
        wystawiona: 'Wystawiona',
        wysłana: 'Wysłana',
        zapłacona: 'Zapłacona'
      };
      return labels[status] || status;
    };
    
    const getStatusBadgeClass = (status) => {
      const classes = {
        robocza: 'bg-yellow-100 text-yellow-800',
        wystawiona: 'bg-blue-100 text-blue-800',
        wysłana: 'bg-purple-100 text-purple-800',
        zapłacona: 'bg-green-100 text-green-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    };
    
    const getTypeBadgeClass = (typ) => {
      const classes = {
        FV: 'bg-blue-100 text-blue-800',
        FK: 'bg-red-100 text-red-800',
        FMP: 'bg-purple-100 text-purple-800',
        PA: 'bg-green-100 text-green-800',
        WZ: 'bg-orange-100 text-orange-800',
        PZ: 'bg-gray-100 text-gray-800'
      };
      return classes[typ] || 'bg-gray-100 text-gray-800';
    };
    
    // Lifecycle
    onMounted(() => {
      fetchDokumenty();
    });
    
    return {
      dokumentyStore,
      showCreateModal,
      showEditModal,
      showDeleteModal,
      selectedDokument,
      filters,
      dokumentyWystawione,
      dokumentyRobocze,
      applyFilters,
      clearFilters,
      editDokument,
      confirmDelete,
      deleteDokument,
      generatePdf,
      closeModal,
      onDokumentSaved,
      formatPrice,
      formatDate,
      getStatusLabel,
      getStatusBadgeClass,
      getTypeBadgeClass
    };
  }
}
</script>