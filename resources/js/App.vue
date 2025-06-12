<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <AppLayout v-if="authStore.isAuthenticated" />
    <router-view v-else />
  </div>
</template>

<script>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import AppLayout from './components/Layout/AppLayout.vue';

export default {
  name: 'App',
  components: {
    AppLayout
  },
  setup() {
    const authStore = useAuthStore();
    
    // Inicjalizuj auth przy starcie aplikacji
    onMounted(async () => {
      if (authStore.token) {
        await authStore.initializeAuth();
      }
    });
    
    return {
      authStore
    };
  }
}
</script>