import { defineStore } from 'pinia';
import axios from 'axios';

export const useFirmyStore = defineStore('firmy', {
    state: () => ({
        firmy: [],
        klienci: [],
        loading: false,
        error: null,
        currentFirma: null
    }),

    getters: {
        getFirmaById: (state) => (id) => {
            return state.firmy.find(firma => firma.id === id);
        },
        firmyKsiegowe: (state) => {
            return state.firmy.filter(f => f.typ === 'ksiegowa');
        },
        firmyKlienci: (state) => {
            return state.firmy.filter(f => f.typ === 'klient');
        }
    },

    actions: {
        async fetchFirmy() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/firmy');
                if (response.data.success) {
                    this.firmy = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania firm';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchKlienci() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/firmy/klienci');
                if (response.data.success) {
                    this.klienci = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania klientów';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createFirma(firma) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/firmy', firma);
                if (response.data.success) {
                    this.firmy.push(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd tworzenia firmy';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async createKlient(klient) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/firmy/klienci', klient);
                if (response.data.success) {
                    this.klienci.push(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd dodawania klienta';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async updateFirma(id, firma) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.put(`/firmy/${id}`, firma);
                if (response.data.success) {
                    const index = this.firmy.findIndex(f => f.id === id);
                    if (index !== -1) {
                        this.firmy[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd aktualizacji firmy';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteFirma(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.delete(`/firmy/${id}`);
                if (response.data.success) {
                    this.firmy = this.firmy.filter(f => f.id !== id);
                    this.klienci = this.klienci.filter(k => k.id !== id);
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd usuwania firmy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchFirma(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/firmy/${id}`);
                if (response.data.success) {
                    this.currentFirma = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania firmy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearCurrentFirma() {
            this.currentFirma = null;
        },

        clearError() {
            this.error = null;
        }
    }
});
