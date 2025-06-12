import { defineStore } from 'pinia';
import axios from 'axios';

export const useArtykulyStore = defineStore('artykuly', {
    state: () => ({
        artykuly: [],
        loading: false,
        error: null,
        currentArtykul: null
    }),

    getters: {
        getArtykulById: (state) => (id) => {
            return state.artykuly.find(artykul => artykul.id === id);
        },
        artykulyAktywne: (state) => {
            return state.artykuly.filter(a => a.aktywny);
        },
        artykulyWedlugStawkiVat: (state) => (stawka) => {
            return state.artykuly.filter(a => a.stawka_vat === stawka);
        }
    },

    actions: {
        async fetchArtykuly() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/artykuly');
                if (response.data.success) {
                    this.artykuly = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania artykułów';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createArtykul(artykul) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/artykuly', artykul);
                if (response.data.success) {
                    this.artykuly.push(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd tworzenia artykułu';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async updateArtykul(id, artykul) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.put(`/artykuly/${id}`, artykul);
                if (response.data.success) {
                    const index = this.artykuly.findIndex(a => a.id === id);
                    if (index !== -1) {
                        this.artykuly[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd aktualizacji artykułu';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteArtykul(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.delete(`/artykuly/${id}`);
                if (response.data.success) {
                    this.artykuly = this.artykuly.filter(a => a.id !== id);
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd usuwania artykułu';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchArtykul(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/artykuly/${id}`);
                if (response.data.success) {
                    this.currentArtykul = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania artykułu';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearCurrentArtykul() {
            this.currentArtykul = null;
        },

        clearError() {
            this.error = null;
        }
    }
});