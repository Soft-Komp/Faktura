<template>
  <div>
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Dashboard
        </h2>
      </div>
    </div>

    <div class="mt-8">
      <!-- Welcome section -->
      <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Witaj, {{ authStore.user?.name }}!
          </h3>
          <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>Jesteś zalogowany jako <strong>{{ authStore.user?.rola }}</strong></p>
            <p v-if="authStore.userFirma">Firma: <strong>{{ authStore.userFirma.nazwa }}</strong></p>
          </div>
        </div>
      </div>

      <!-- Stats grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <span class="text-white font-semibold">D</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Dokumenty
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ stats.dokumenty || 0 }}
                  </dd>
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
                  <span class="text-white font-semibold">O</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Odbiorcy
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ stats.odbiorcy || 0 }}
                  </dd>
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
                  <span class="text-white font-semibold">A</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Artykuły
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ stats.artykuly || 0 }}
                  </dd>
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
                  <span class="text-white font-semibold">F</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Firmy
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ stats.firmy || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            Szybkie akcje
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link 
              to="/dokumenty" 
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              Nowy dokument
            </router-link>
            <router-link 
              to="/odbiorcy" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Zarządzaj odbiorcami
            </router-link>
            <router-link 
              to="/artykuly" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Zarządzaj artykułami
            </router-link>
            <router-link 
              v-if="authStore.isKsiegowy || authStore.isAdmin"
              to="/firmy" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Zarządzaj firmami
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

export default {
  name: 'Dashboard',
  setup() {
    const authStore = useAuthStore();
    const stats = ref({});
    
    const fetchStats = async () => {
      try {
        const response = await axios.get('/stats');
        if (response.data.success) {
          stats.value = {
            dokumenty: response.data.data.dokumenty?.total || 0,
            odbiorcy: response.data.data.liczba_odbiorcow || 0,
            artykuly: response.data.data.liczba_artykulow || 0,
            firmy: response.data.data.liczba_firm || 0
          };
        }
      } catch (error) {
        console.error('Błąd pobierania statystyk:', error);
      }
    };
    
    onMounted(() => {
      fetchStats();
    });
    
    return {
      authStore,
      stats
    };
  }
}
</script>