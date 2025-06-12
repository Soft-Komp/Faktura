import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('auth_token') || null,
        loading: false,
        error: null
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.rola === 'admin',
        isKsiegowy: (state) => state.user?.rola === 'ksiegowy',
        isKlient: (state) => state.user?.rola === 'klient',
        userFirma: (state) => state.user?.firma || null
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/login', credentials);
                
                if (response.data.success) {
                    this.token = response.data.data.token;
                    this.user = response.data.data.user;
                    
                    localStorage.setItem('auth_token', this.token);
                    localStorage.setItem('user', JSON.stringify(this.user));
                    
                    return { success: true, data: response.data.data };
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd logowania';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/register', userData);
                
                if (response.data.success) {
                    this.token = response.data.data.token;
                    this.user = response.data.data.user;
                    
                    localStorage.setItem('auth_token', this.token);
                    localStorage.setItem('user', JSON.stringify(this.user));
                    
                    return { success: true, data: response.data.data };
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd rejestracji';
                return { success: false, error: this.error, errors: error.response?.data?.data };
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            if (this.token) {
                try {
                    await axios.post('/logout');
                } catch (error) {
                    console.error('Błąd wylogowania:', error);
                }
            }
            
            this.user = null;
            this.token = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
        },

        async fetchProfile() {
            if (!this.token) return { success: false, error: 'Brak tokenu' };
            
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/profile');
                if (response.data.success) {
                    this.user = response.data.data;
                    localStorage.setItem('user', JSON.stringify(this.user));
                    return { success: true, data: this.user };
                }
            } catch (error) {
                console.error('Błąd pobierania profilu:', error);
                this.error = error.response?.data?.message || 'Błąd pobierania profilu';
                if (error.response?.status === 401) {
                    this.logout();
                }
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        // Inicjalizacja danych użytkownika przy starcie aplikacji
        async initializeAuth() {
            if (this.token && this.user) {
                // Jeśli użytkownik ma firmę przypisaną ale brak danych firmy, pobierz profil
                if (this.user.firma_id && !this.user.firma) {
                    await this.fetchProfile();
                }
            }
        },

        clearError() {
            this.error = null;
        }
    }
});