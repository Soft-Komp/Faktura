<template>
  <div>
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Zarządzanie odbiorcami
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Zarządzaj bazą klientów i odbiorców faktur
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
          Dodaj odbiorcę
        </button>
      </div>
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
              placeholder="Nazwa, NIP, email..."
            />
          </div>
          <div>
            <label for="typ" class="block text-sm font-medium text-gray-700 mb-2">Typ odbiorcy</label>
            <select
              id="typ"
              v-model="filters.typ_odbiorcy"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="krajowy">Krajowy</option>
              <option value="ue">UE</option>
              <option value="pozaue">Poza UE</option>
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
    <div v-if="odbiorcyStore.loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Ładowanie odbiorców...</p>
    </div>

    <!-- Error -->
    <div v-else-if="odbiorcyStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Błąd</h3>
          <p class="mt-1 text-sm text-red-700">{{ odbiorcyStore.error }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="filteredOdbiorcy.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak odbiorców</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od dodania pierwszego odbiorcy.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="odbiorca in filteredOdbiorcy" :key="odbiorca.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <h3 class="text-lg font-medium text-gray-900">{{ odbiorca.nazwa }}</h3>
                  <p class="text-sm text-gray-500">{{ odbiorca.nazwa_pelna }}</p>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>NIP: {{ odbiorca.nip || 'Brak' }}</span>
                    <span>{{ odbiorca.miejscowosc }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getTypeBadgeClass(odbiorca.typ_odbiorcy)">
                      {{ getTypeLabel(odbiorca.typ_odbiorcy) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="editOdbiorca(odbiorca)"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Edytuj
              </button>
              <button
                @click="confirmDelete(odbiorca)"
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
    <OdbiorcaModal
      v-if="showCreateModal || showEditModal"
      :odbiorca="selectedOdbiorca"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onOdbiorcaSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmDeleteModal
      v-if="showDeleteModal"
      :title="`Usuń odbiorcę: ${selectedOdbiorca?.nazwa}`"
      :message="'Czy na pewno chcesz usunąć tego odbiorcę? Ta operacja jest nieodwracalna.'"
      @confirm="deleteOdbiorca"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useOdbiorcyStore } from '../../stores/odbiorcy';
import OdbiorcaModal from '../../components/Odbiorcy/OdbiorcaModal.vue';
import ConfirmDeleteModal from '../../components/Shared/ConfirmDeleteModal.vue';

export default {
  name: 'OdbiorcyIndex',
  components: {
    OdbiorcaModal,
    ConfirmDeleteModal
  },
  setup() {
    const odbiorcyStore = useOdbiorcyStore();
    
    // State
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedOdbiorca = ref(null);
    
    // Filters
    const filters = ref({
      search: '',
      typ_odbiorcy: ''
    });
    
    // Computed
    const filteredOdbiorcy = computed(() => {
      let result = odbiorcyStore.odbiorcy;
      
      if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(o => 
          o.nazwa.toLowerCase().includes(search) ||
          o.nazwa_pelna.toLowerCase().includes(search) ||
          o.nip?.toLowerCase().includes(search) ||
          o.email?.toLowerCase().includes(search)
        );
      }
      
      if (filters.value.typ_odbiorcy) {
        result = result.filter(o => o.typ_odbiorcy === filters.value.typ_odbiorcy);
      }
      
      return result;
    });
    
    // Methods
    const fetchOdbiorcy = async () => {
      await odbiorcyStore.fetchOdbiorcy();
    };
    
    const clearFilters = () => {
      filters.value.search = '';
      filters.value.typ_odbiorcy = '';
    };
    
    const editOdbiorca = (odbiorca) => {
      selectedOdbiorca.value = { ...odbiorca };
      showEditModal.value = true;
    };
    
    const confirmDelete = (odbiorca) => {
      selectedOdbiorca.value = odbiorca;
      showDeleteModal.value = true;
    };
    
    const deleteOdbiorca = async () => {
      const result = await odbiorcyStore.deleteOdbiorca(selectedOdbiorca.value.id);
      if (result.success) {
        showDeleteModal.value = false;
        selectedOdbiorca.value = null;
      }
    };
    
    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedOdbiorca.value = null;
    };
    
    const onOdbiorcaSaved = () => {
      closeModal();
      fetchOdbiorcy(); // Refresh list
    };
    
    const getTypeLabel = (typ) => {
      const labels = {
        krajowy: 'Krajowy',
        ue: 'UE',
        pozaue: 'Poza UE'
      };
      return labels[typ] || typ;
    };
    
    const getTypeBadgeClass = (typ) => {
      const classes = {
        krajowy: 'bg-green-100 text-green-800',
        ue: 'bg-blue-100 text-blue-800',
        pozaue: 'bg-yellow-100 text-yellow-800'
      };
      return classes[typ] || 'bg-gray-100 text-gray-800';
    };
    
    // Lifecycle
    onMounted(() => {
      fetchOdbiorcy();
    });
    
    return {
      odbiorcyStore,
      showCreateModal,
      showEditModal,
      showDeleteModal,
      selectedOdbiorca,
      filters,
      filteredOdbiorcy,
      clearFilters,
      editOdbiorca,
      confirmDelete,
      deleteOdbiorca,
      closeModal,
      onOdbiorcaSaved,
      getTypeLabel,
      getTypeBadgeClass
    };
  }
}
</script>