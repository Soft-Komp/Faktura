<template>
  <div>
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Zarządzanie użytkownikami
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Zarządzaj użytkownikami systemu i ich przypisaniami do firm
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="showCreateModal = true"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
          </svg>
          Dodaj użytkownika
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
              placeholder="Nazwa, email..."
            />
          </div>
          <div>
            <label for="rola" class="block text-sm font-medium text-gray-700 mb-2">Rola</label>
            <select
              id="rola"
              v-model="filters.rola"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="admin">Administrator</option>
              <option value="ksiegowy">Księgowy</option>
              <option value="klient">Klient</option>
            </select>
          </div>
          <div>
            <label for="firma_status" class="block text-sm font-medium text-gray-700 mb-2">Status firmy</label>
            <select
              id="firma_status"
              v-model="filters.firma_status"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Wszystkie</option>
              <option value="assigned">Przypisani</option>
              <option value="unassigned">Nieprzypisani</option>
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

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wszyscy użytkownicy</dt>
                <dd class="text-lg font-medium text-gray-900">{{ usersStore.users.length }}</dd>
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
                <dt class="text-sm font-medium text-gray-500 truncate">Przypisani do firm</dt>
                <dd class="text-lg font-medium text-gray-900">{{ usersWithFirma.length }}</dd>
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
                <dt class="text-sm font-medium text-gray-500 truncate">Nieprzypisani</dt>
                <dd class="text-lg font-medium text-gray-900">{{ usersStore.usersWithoutFirma.length }}</dd>
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
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Administratorzy</dt>
                <dd class="text-lg font-medium text-gray-900">{{ usersStore.usersByRole('admin').length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="usersStore.loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Ładowanie użytkowników...</p>
    </div>

    <!-- Error -->
    <div v-else-if="usersStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Błąd</h3>
          <p class="mt-1 text-sm text-red-700">{{ usersStore.error }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="filteredUsers.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak użytkowników</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od dodania pierwszego użytkownika.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="user in filteredUsers" :key="user.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                    <span 
                      :class="getRoleBadgeClass(user.rola)"
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ getRoleLabel(user.rola) }}
                    </span>
                    <span 
                      v-if="!user.firma_id" 
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                    >
                      Nieprzypisany
                    </span>
                  </div>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span v-if="user.firma">
                      Firma: {{ user.firma.nazwa }}
                    </span>
                    <span v-else class="text-red-600">
                      Brak przypisanej firmy
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <div class="relative inline-block text-left">
                <button
                  @click="toggleUserActions(user.id)"
                  class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                >
                  Akcje
                  <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>

                <div v-if="activeUserActions === user.id" 
                     class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                  <div class="py-1">
                    <button
                      @click="editUser(user)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Edytuj użytkownika
                    </button>
                    
                    <button
                      v-if="user.firma_id"
                      @click="detachFromFirma(user)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Odepnij od firmy
                    </button>
                    
                    <button
                      v-else
                      @click="showAssignModal(user)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Przypisz do firmy
                    </button>
                    
                    <button
                      @click="showRoleModal(user)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Zmień rolę
                    </button>
                    
                    <button
                      v-if="user.id !== authStore.user?.id"
                      @click="confirmDelete(user)"
                      class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100"
                    >
                      Usuń użytkownika
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Create/Edit User Modal -->
    <UserModal
      v-if="showCreateModal || showEditModal"
      :user="selectedUser"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onUserSaved"
    />

    <!-- Assign to Firma Modal -->
    <AssignFirmaModal
      v-if="showAssignFirmaModal"
      :user="selectedUser"
      @close="showAssignFirmaModal = false"
      @assigned="onFirmaAssigned"
    />

    <!-- Change Role Modal -->
    <ChangeRoleModal
      v-if="showChangeRoleModal"
      :user="selectedUser"
      @close="showChangeRoleModal = false"
      @changed="onRoleChanged"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmDeleteModal
      v-if="showDeleteModal"
      :title="`Usuń użytkownika: ${selectedUser?.name}`"
      :message="'Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna.'"
      @confirm="deleteUser"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useUsersStore } from '../../stores/users';
import { useAuthStore } from '../../stores/auth';
import UserModal from '../../components/Users/UserModal.vue';
import AssignFirmaModal from '../../components/Users/AssignFirmaModal.vue';
import ChangeRoleModal from '../../components/Users/ChangeRoleModal.vue';
import ConfirmDeleteModal from '../../components/Shared/ConfirmDeleteModal.vue';

export default {
  name: 'UsersIndex',
  components: {
    UserModal,
    AssignFirmaModal,
    ChangeRoleModal,
    ConfirmDeleteModal
  },
  setup() {
    const usersStore = useUsersStore();
    const authStore = useAuthStore();
    
    // State
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showAssignFirmaModal = ref(false);
    const showChangeRoleModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedUser = ref(null);
    const activeUserActions = ref(null);
    
    // Filters
    const filters = ref({
      search: '',
      rola: '',
      firma_status: ''
    });
    
    // Computed
    const filteredUsers = computed(() => {
      let result = usersStore.users;
      
      if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(u => 
          u.name.toLowerCase().includes(search) ||
          u.email.toLowerCase().includes(search) ||
          u.firma?.nazwa?.toLowerCase().includes(search)
        );
      }
      
      if (filters.value.rola) {
        result = result.filter(u => u.rola === filters.value.rola);
      }
      
      if (filters.value.firma_status === 'assigned') {
        result = result.filter(u => u.firma_id);
      } else if (filters.value.firma_status === 'unassigned') {
        result = result.filter(u => !u.firma_id);
      }
      
      return result;
    });
    
    const usersWithFirma = computed(() => 
      usersStore.users.filter(u => u.firma_id)
    );
    
    // Methods
    const fetchUsers = async () => {
      await usersStore.fetchUsers();
    };
    
    const clearFilters = () => {
      filters.value.search = '';
      filters.value.rola = '';
      filters.value.firma_status = '';
    };
    
    const toggleUserActions = (userId) => {
      activeUserActions.value = activeUserActions.value === userId ? null : userId;
    };
    
    const editUser = (user) => {
      selectedUser.value = { ...user };
      showEditModal.value = true;
      activeUserActions.value = null;
    };
    
    const showAssignModal = (user) => {
      selectedUser.value = user;
      showAssignFirmaModal.value = true;
      activeUserActions.value = null;
    };
    
    const showRoleModal = (user) => {
      selectedUser.value = user;
      showChangeRoleModal.value = true;
      activeUserActions.value = null;
    };
    
    const confirmDelete = (user) => {
      selectedUser.value = user;
      showDeleteModal.value = true;
      activeUserActions.value = null;
    };
    
    const deleteUser = async () => {
      const result = await usersStore.deleteUser(selectedUser.value.id);
      if (result.success) {
        showDeleteModal.value = false;
        selectedUser.value = null;
      }
    };
    
    const detachFromFirma = async (user) => {
      const result = await usersStore.detachFromFirma(user.id);
      if (result.success) {
        // Sukces - lista zostanie automatycznie zaktualizowana przez store
      }
      activeUserActions.value = null;
    };
    
    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedUser.value = null;
    };
    
    const onUserSaved = () => {
      closeModal();
      fetchUsers();
    };
    
    const onFirmaAssigned = () => {
      showAssignFirmaModal.value = false;
      selectedUser.value = null;
    };
    
    const onRoleChanged = () => {
      showChangeRoleModal.value = false;
      selectedUser.value = null;
    };
    
    const getRoleLabel = (role) => {
      const labels = {
        admin: 'Administrator',
        ksiegowy: 'Księgowy',
        klient: 'Klient'
      };
      return labels[role] || role;
    };
    
    const getRoleBadgeClass = (role) => {
      const classes = {
        admin: 'bg-purple-100 text-purple-800',
        ksiegowy: 'bg-blue-100 text-blue-800',
        klient: 'bg-green-100 text-green-800'
      };
      return classes[role] || 'bg-gray-100 text-gray-800';
    };
    
    // Click outside to close dropdown
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        activeUserActions.value = null;
      }
    };
    
    // Lifecycle
    onMounted(() => {
      fetchUsers();
      document.addEventListener('click', handleClickOutside);
    });
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });
    
    return {
      usersStore,
      authStore,
      showCreateModal,
      showEditModal,
      showAssignFirmaModal,
      showChangeRoleModal,
      showDeleteModal,
      selectedUser,
      activeUserActions,
      filters,
      filteredUsers,
      usersWithFirma,
      clearFilters,
      toggleUserActions,
      editUser,
      showAssignModal,
      showRoleModal,
      confirmDelete,
      deleteUser,
      detachFromFirma,
      closeModal,
      onUserSaved,
      onFirmaAssigned,
      onRoleChanged,
      getRoleLabel,
      getRoleBadgeClass
    };
  }
}
</script>