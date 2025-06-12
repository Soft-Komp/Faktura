import { defineStore } from 'pinia';
import axios from 'axios';

export const useDokumentyStore = defineStore('dokumenty', {
    state: () => ({
        dokumenty: [],
        loading: false,
        error: null,
        currentDokument: null
    }),

    getters: {
        getDokumentById: (state) => (id) => {
            return state.dokumenty.find(dokument => dokument.id === id);
        },
        dokumentyWedlugStatusu: (state) => (status) => {
            return state.dokumenty.filter(d => d.status === status);
        },
        dokumentyWedlugTypu: (state) => (typ) => {
            return state.dokumenty.filter(d => d.typ_dokumentu === typ);
        },
        sumaWartosciMiesiaca: (state) => {
            const dzisiaj = new Date();
            const pierwszyDzienMiesiaca = new Date(dzisiaj.getFullYear(), dzisiaj.getMonth(), 1);
            
            return state.dokumenty
                .filter(d => new Date(d.data_wystawienia) >= pierwszyDzienMiesiaca)
                .reduce((suma, d) => suma + parseFloat(d.wartosc_brutto || 0), 0);
        }
    },

    actions: {
        async fetchDokumenty(filters = {}) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/dokumenty', { params: filters });
                if (response.data.success) {
                    this.dokumenty = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania dokumentów';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createDokument(dokument) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/dokumenty', dokument);
                if (response.data.success) {
                    this.dokumenty.unshift(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd tworzenia dokumentu';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async updateDokument(id, dokument) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.put(`/dokumenty/${id}`, dokument);
                if (response.data.success) {
                    const index = this.dokumenty.findIndex(d => d.id === id);
                    if (index !== -1) {
                        this.dokumenty[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd aktualizacji dokumentu';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteDokument(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.delete(`/dokumenty/${id}`);
                if (response.data.success) {
                    this.dokumenty = this.dokumenty.filter(d => d.id !== id);
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd usuwania dokumentu';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchDokument(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/dokumenty/${id}`);
                if (response.data.success) {
                    this.currentDokument = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania dokumentu';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async generatePdf(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post(`/dokumenty/${id}/pdf`, {}, {
                    responseType: 'blob'
                });
                
                // Utwórz link do pobrania pliku
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `dokument_${id}.pdf`);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
                
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd generowania PDF';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async sendEmail(id, emailData) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post(`/dokumenty/${id}/email`, emailData);
                if (response.data.success) {
                    return { success: true, data: response.data.data };
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd wysyłania emaila';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearCurrentDokument() {
            this.currentDokument = null;
        },

        clearError() {
            this.error = null;
        }
    }
});