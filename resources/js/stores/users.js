import { defineStore } from 'pinia';
import axios from 'axios';

export const useUsersStore = defineStore('users', {
    state: () => ({
        users: [],
        loading: false,
        error: null,
        currentUser: null
    }),

    getters: {
        getUserById: (state) => (id) => {
            return state.users.find(user => user.id === id);
        },
        usersByRole: (state) => (role) => {
            return state.users.filter(u => u.rola === role);
        },
        usersWithoutFirma: (state) => {
            return state.users.filter(u => !u.firma_id);
        },
        usersByFirma: (state) => (firmaId) => {
            return state.users.filter(u => u.firma_id === firmaId);
        }
    },

    actions: {
        async fetchUsers() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/users');
                if (response.data.success) {
                    this.users = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania użytkowników';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createUser(userData) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/users', userData);
                if (response.data.success) {
                    this.users.push(response.data.data);
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd tworzenia użytkownika';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async updateUser(id, userData) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.put(`/users/${id}`, userData);
                if (response.data.success) {
                    const index = this.users.findIndex(u => u.id === id);
                    if (index !== -1) {
                        this.users[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd aktualizacji użytkownika';
                return { 
                    success: false, 
                    error: this.error,
                    errors: error.response?.data?.data 
                };
            } finally {
                this.loading = false;
            }
        },

        async deleteUser(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.delete(`/users/${id}`);
                if (response.data.success) {
                    this.users = this.users.filter(u => u.id !== id);
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd usuwania użytkownika';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async assignToFirma(userId, firmaId) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post(`/users/${userId}/assign-firma`, { firma_id: firmaId });
                if (response.data.success) {
                    const index = this.users.findIndex(u => u.id === userId);
                    if (index !== -1) {
                        this.users[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd przypisywania użytkownika do firmy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async detachFromFirma(userId) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post(`/users/${userId}/detach-firma`);
                if (response.data.success) {
                    const index = this.users.findIndex(u => u.id === userId);
                    if (index !== -1) {
                        this.users[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd odpinania użytkownika od firmy';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async changeRole(userId, role) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post(`/users/${userId}/change-role`, { rola: role });
                if (response.data.success) {
                    const index = this.users.findIndex(u => u.id === userId);
                    if (index !== -1) {
                        this.users[index] = response.data.data;
                    }
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd zmiany roli użytkownika';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchUser(id) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get(`/users/${id}`);
                if (response.data.success) {
                    this.currentUser = response.data.data;
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Błąd pobierania użytkownika';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        clearCurrentUser() {
            this.currentUser = null;
        },

        clearError() {
            this.error = null;
        }
    }
});