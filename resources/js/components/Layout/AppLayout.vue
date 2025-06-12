<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-900">System Fakturowania</h1>
            </div>
            
            <!-- Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link 
                to="/dashboard" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Dashboard
              </router-link>
              
              <router-link 
                to="/dokumenty" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Dokumenty
              </router-link>
              
              <router-link 
                to="/odbiorcy" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Odbiorcy
              </router-link>
              
              <router-link 
                to="/artykuly" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Artykuły
              </router-link>
              
              <router-link 
                v-if="authStore.isKsiegowy || authStore.isAdmin"
                to="/firmy" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Firmy
              </router-link>
              
              <router-link 
                v-if="authStore.isAdmin"
                to="/users" 
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                active-class="border-blue-500 text-gray-900"
              >
                Użytkownicy
              </router-link>
            </div>
          </div>
          
          <!-- User menu -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <span class="text-sm text-gray-700 mr-4">
                {{ authStore.user?.name }} ({{ authStore.user?.rola }})
              </span>
              <button 
                @click="handleLogout"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              >
                Wyloguj
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <router-view />
    </main>
  </div>
</template>

<script>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

export default {
  name: 'AppLayout',
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    
    const handleLogout = async () => {
      await authStore.logout();
      router.push('/login');
    };
    
    return {
      authStore,
      handleLogout
    };
  }
}
</script>