import { defineStore } from 'pinia';
import axios from 'axios';

export const useOdbiorcyStore = defineStore('odbiorcy', {
    state: () => ({
        odbiorcy: [],
        loading: false,
        error: null,
        currentOdbiorca: null
    }),

    getters: {
        getOdbiorcaById: (state) => (id) => {
            return state.odbiorcy.find(odbiorca => odbiorca.id === id);
        },
        odbiorczyKrajowi: (state) => {
            return state.odbiorcy.filter(o => o.typ_odbiorcy === 'krajowy');
        },
        odbiorczyUE: (state) => {
            return state.odbiorcy.filter(o => o.typ_odbiorcy === 'ue');
        }
    },

    actions: {
        async fetchOdbiorcy() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/odbiorcy');
                if (response.data.success) {
                    this.odbiorcy = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania odbiorców';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createOdbiorca(odbiorcy) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/odbiorcy', odbiorcy);
                if (response.data.success) {
                    this.odbiorcy.push(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd tworzenia odbiorcy';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async updateOdbiorca(id, odbiorcy) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.put(`/odbiorcy/${id}`, odbiorcy);
                if (response.data.success) {
                    const index = this.odbiorcy.findIndex(o => o.id === id);
                    if (index !== -1) {
                        this.odbiorcy[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd aktualizacji odbiorcy';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteOdbiorca(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.delete(`/odbiorcy/${id}`);
                if (response.data.success) {
                    this.odbiorcy = this.odbiorcy.filter(o => o.id !== id);
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd usuwania odbiorcy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchOdbiorca(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/odbiorcy/${id}`);
                if (response.data.success) {
                    this.currentOdbiorca = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania odbiorcy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearCurrentOdbiorca() {
            this.currentOdbiorca = null;
        },

        clearError() {
            this.error = null;
        }
    }
});