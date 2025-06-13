<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-5 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-md bg-white" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isEditMode ? 'Edytuj dokument' : 'Nowy dokument handlowy' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column -->
          <div class="space-y-4">
            <h4 class="text-md font-medium text-gray-900">Podstawowe informacje</h4>
            
            <div>
              <label for="typ_dokumentu" class="block text-sm font-medium text-gray-700 mb-2">
                Typ dokumentu *
              </label>
              <select
                id="typ_dokumentu"
                v-model="form.typ_dokumentu"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.typ_dokumentu }"
              >
                <option value="">Wybierz typ</option>
                <option value="FV">Faktura VAT</option>
                <option value="FK">Faktura korygująca</option>
                <option value="FMP">Faktura VAT MP</option>
                <option value="PA">Paragon</option>
                <option value="WZ">WZ</option>
                <option value="PZ">PZ</option>
              </select>
              <div v-if="errors.typ_dokumentu" class="text-red-500 text-xs mt-1">{{ errors.typ_dokumentu[0] }}</div>
            </div>

            <div>
              <label for="data_wystawienia" class="block text-sm font-medium text-gray-700 mb-2">
                Data wystawienia *
              </label>
              <input
                id="data_wystawienia"
                v-model="form.data_wystawienia"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.data_wystawienia }"
              />
              <div v-if="errors.data_wystawienia" class="text-red-500 text-xs mt-1">{{ errors.data_wystawienia[0] }}</div>
            </div>

            <div>
              <label for="data_sprzedazy" class="block text-sm font-medium text-gray-700 mb-2">
                Data sprzedaży *
              </label>
              <input
                id="data_sprzedazy"
                v-model="form.data_sprzedazy"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.data_sprzedazy }"
              />
              <div v-if="errors.data_sprzedazy" class="text-red-500 text-xs mt-1">{{ errors.data_sprzedazy[0] }}</div>
            </div>

            <div>
              <label for="termin_platnosci" class="block text-sm font-medium text-gray-700 mb-2">
                Termin płatności *
              </label>
              <input
                id="termin_platnosci"
                v-model="form.termin_platnosci"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.termin_platnosci }"
              />
              <div v-if="errors.termin_platnosci" class="text-red-500 text-xs mt-1">{{ errors.termin_platnosci[0] }}</div>
            </div>
          </div>

          <!-- Middle Column -->
          <div class="space-y-4">
            <h4 class="text-md font-medium text-gray-900">Firma i odbiorca</h4>
            
            <div v-if="authStore.isKsiegowy">
              <label for="id_firma" class="block text-sm font-medium text-gray-700 mb-2">
                Wystawiaj jako *
              </label>
              <select
                id="id_firma"
                v-model="form.id_firma"
                required
                @change="onFirmaChange"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.id_firma }"
              >
                <option value="">Wybierz firmę</option>
                <option v-if="authStore.userFirma" :value="authStore.userFirma.id">{{ authStore.userFirma.nazwa }} (Moja firma)</option>
                <option 
                  v-for="klient in dostepneKlienci" 
                  :key="klient.id" 
                  :value="klient.id"
                >
                  {{ klient.nazwa }} (Klient)
                </option>
              </select>
              <div v-if="errors.id_firma" class="text-red-500 text-xs mt-1">{{ errors.id_firma[0] }}</div>
            </div>
            
            <div v-else>
              <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">Wystawiasz jako:</label>
                <button
                  type="button"
                  @click="refreshUserData"
                  class="text-xs text-blue-600 hover:text-blue-800"
                >
                  Odśwież dane
                </button>
              </div>
              <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                {{ firmaNazwa }}
              </div>
            </div>

            <div>
              <label for="id_odbiorca" class="block text-sm font-medium text-gray-700 mb-2">
                Odbiorca *
              </label>
              <select
                id="id_odbiorca"
                v-model="form.id_odbiorca"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.id_odbiorca }"
              >
                <option value="">Wybierz odbiorcę</option>
                <option 
                  v-for="odbiorca in dostepniOdbiorcy" 
                  :key="odbiorca.id" 
                  :value="odbiorca.id"
                >
                  {{ odbiorca.nazwa }}
                </option>
              </select>
              <div v-if="errors.id_odbiorca" class="text-red-500 text-xs mt-1">{{ errors.id_odbiorca[0] }}</div>
            </div>

            <div>
              <label for="miejsce_wystawienia" class="block text-sm font-medium text-gray-700 mb-2">
                Miejsce wystawienia *
              </label>
              <input
                id="miejsce_wystawienia"
                v-model="form.miejsce_wystawienia"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.miejsce_wystawienia }"
              />
              <div v-if="errors.miejsce_wystawienia" class="text-red-500 text-xs mt-1">{{ errors.miejsce_wystawienia[0] }}</div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-4">
            <h4 class="text-md font-medium text-gray-900">Waluta i uwagi</h4>
            
            <div>
              <label for="waluta" class="block text-sm font-medium text-gray-700 mb-2">
                Waluta *
              </label>
              <select
                id="waluta"
                v-model="form.waluta"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.waluta }"
              >
                <option value="PLN">PLN</option>
                <option value="EUR">EUR</option>
                <option value="USD">USD</option>
              </select>
              <div v-if="errors.waluta" class="text-red-500 text-xs mt-1">{{ errors.waluta[0] }}</div>
            </div>

            <div v-if="form.waluta !== 'PLN'">
              <label for="kurs_waluty" class="block text-sm font-medium text-gray-700 mb-2">
                Kurs waluty *
              </label>
              <input
                id="kurs_waluty"
                v-model="form.kurs_waluty"
                type="number"
                step="0.0001"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.kurs_waluty }"
              />
              <div v-if="errors.kurs_waluty" class="text-red-500 text-xs mt-1">{{ errors.kurs_waluty[0] }}</div>
            </div>

            <div>
              <label for="uwagi" class="block text-sm font-medium text-gray-700 mb-2">
                Uwagi
              </label>
              <textarea
                id="uwagi"
                v-model="form.uwagi"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': errors.uwagi }"
              ></textarea>
              <div v-if="errors.uwagi" class="text-red-500 text-xs mt-1">{{ errors.uwagi[0] }}</div>
            </div>
          </div>
        </div>

        <!-- Pozycje dokumentu -->
        <div class="border-t pt-6">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-medium text-gray-900">Pozycje dokumentu</h4>
            <button
              type="button"
              @click="addPozycja"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Dodaj pozycję
            </button>
          </div>

          <div class="space-y-4">
            <div 
              v-for="(pozycja, index) in form.pozycje" 
              :key="index" 
              class="border rounded-lg p-4 bg-gray-50"
            >
              <div class="flex justify-between items-center mb-3">
                <h5 class="text-sm font-medium text-gray-700">Pozycja {{ index + 1 }}</h5>
                <button
                  type="button"
                  @click="removePozycja(index)"
                  class="text-red-600 hover:text-red-800 text-sm"
                >
                  Usuń
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-2">
                  <label class="block text-xs font-medium text-gray-700 mb-1">Artykuł *</label>
                  <select
                    v-model="pozycja.id_artykulu"
                    @change="selectArtykul(index, pozycja.id_artykulu)"
                    required
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                  >
                    <option value="">Wybierz artykuł</option>
                    <option 
                      v-for="artykul in dostepneArtykuly" 
                      :key="artykul.id" 
                      :value="artykul.id"
                    >
                      {{ artykul.nazwa }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Ilość *</label>
                  <input
                    v-model="pozycja.ilosc"
                    type="number"
                    step="0.001"
                    min="0"
                    required
                    @input="calculatePozycja(index)"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Jedn.</label>
                  <input
                    v-model="pozycja.jednostka"
                    type="text"
                    readonly
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Cena netto</label>
                  <input
                    v-model="pozycja.cena_netto"
                    type="number"
                    step="0.01"
                    min="0"
                    @input="calculatePozycja(index)"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">VAT %</label>
                  <input
                    v-model="pozycja.stawka_vat"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    @input="calculatePozycja(index)"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>
              </div>

              <div class="mt-2 text-right text-sm text-gray-600">
                Wartość netto: {{ formatPrice(pozycja.wartosc_netto_pozycji) }} {{ form.waluta }} | 
                Wartość brutto: {{ formatPrice(pozycja.wartosc_brutto_pozycji) }} {{ form.waluta }}
              </div>
            </div>
          </div>

          <div v-if="form.pozycje.length === 0" class="text-center py-8 text-gray-500">
            Dodaj pierwszą pozycję do dokumentu
          </div>
        </div>

        <!-- Summary -->
        <div v-if="form.pozycje.length > 0" class="border-t pt-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <h4 class="text-md font-medium text-gray-900 mb-2">Podsumowanie</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Wartość netto:</span>
                <span class="font-medium ml-2">{{ formatPrice(totalNetto) }} {{ form.waluta }}</span>
              </div>
              <div>
                <span class="text-gray-600">Wartość VAT:</span>
                <span class="font-medium ml-2">{{ formatPrice(totalVat) }} {{ form.waluta }}</span>
              </div>
              <div>
                <span class="text-gray-600">Wartość brutto:</span>
                <span class="font-medium ml-2">{{ formatPrice(totalBrutto) }} {{ form.waluta }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Anuluj
          </button>
          <button
            type="submit"
            :disabled="loading || form.pozycje.length === 0"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">Zapisywanie...</span>
            <span v-else>{{ isEditMode ? 'Aktualizuj' : 'Utwórz dokument' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import { useDokumentyStore } from '../../stores/dokumenty';
import { useOdbiorcyStore } from '../../stores/odbiorcy';
import { useArtykulyStore } from '../../stores/artykuly';
import { useFirmyStore } from '../../stores/firmy';
import { useAuthStore } from '../../stores/auth';

export default {
  name: 'DokumentModal',
  props: {
    dokument: {
      type: Object,
      default: null
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const dokumentyStore = useDokumentyStore();
    const odbiorcyStore = useOdbiorcyStore();
    const artykulyStore = useArtykulyStore();
    const firmyStore = useFirmyStore();
    const authStore = useAuthStore();
    
    // Form state
    const form = ref({
      typ_dokumentu: '',
      data_sprzedazy: new Date().toISOString().split('T')[0],
      data_wystawienia: new Date().toISOString().split('T')[0],
      termin_platnosci: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      miejsce_wystawienia: '',
      id_firma: null,
      id_odbiorca: '',
      waluta: 'PLN',
      kurs_waluty: 1,
      uwagi: '',
      pozycje: []
    });
    
    const errors = ref({});
    const loading = ref(false);
    const error = ref(null);
    
    // Computed
    const isEditMode = computed(() => props.isEdit && props.dokument);
    
    const dostepniOdbiorcy = computed(() => odbiorcyStore.odbiorcy);
    const dostepneArtykuly = computed(() => artykulyStore.artykulyAktywne);
    const dostepneKlienci = computed(() => firmyStore.klienci);
    
    const firmaNazwa = computed(() => {
      if (authStore.userFirma) {
        return authStore.userFirma.nazwa;
      }
      return 'Ładowanie danych firmy...';
    });
    
    const totalNetto = computed(() => {
      return form.value.pozycje.reduce((sum, p) => sum + parseFloat(p.wartosc_netto_pozycji || 0), 0);
    });
    
    const totalVat = computed(() => {
      return form.value.pozycje.reduce((sum, p) => sum + parseFloat(p.wartosc_vat || 0), 0);
    });
    
    const totalBrutto = computed(() => {
      return form.value.pozycje.reduce((sum, p) => sum + parseFloat(p.wartosc_brutto_pozycji || 0), 0);
    });
    
    // Methods
    const addPozycja = () => {
      form.value.pozycje.push({
        id_artykulu: '',
        nazwa_pozycji: '',
        ilosc: 1,
        jednostka: 'szt.',
        cena_netto: 0,
        stawka_vat: 23,
        wartosc_vat: 0,
        wartosc_netto_pozycji: 0,
        wartosc_brutto_pozycji: 0,
        uwagi: ''
      });
    };
    
    const removePozycja = (index) => {
      form.value.pozycje.splice(index, 1);
    };
    
    const selectArtykul = (index, artykulId) => {
      const artykul = artykulyStore.getArtykulById(parseInt(artykulId));
      
      if (artykul) {
        form.value.pozycje[index] = {
          ...form.value.pozycje[index],
          id_artykulu: artykul.id,
          nazwa_pozycji: artykul.nazwa,
          jednostka: artykul.jednostka,
          cena_netto: artykul.cena_netto,
          stawka_vat: artykul.stawka_vat
        };
        calculatePozycja(index);
      }
    };
    
    const calculatePozycja = (index) => {
      const pozycja = form.value.pozycje[index];
      const ilosc = parseFloat(pozycja.ilosc) || 0;
      const cenaNetto = parseFloat(pozycja.cena_netto) || 0;
      const stawkaVat = parseFloat(pozycja.stawka_vat) || 0;
      
      const wartoscNetto = ilosc * cenaNetto;
      const wartoscVat = wartoscNetto * (stawkaVat / 100);
      const wartoscBrutto = wartoscNetto + wartoscVat;
      
      pozycja.wartosc_netto_pozycji = wartoscNetto.toFixed(2);
      pozycja.wartosc_vat = wartoscVat.toFixed(2);
      pozycja.wartosc_brutto_pozycji = wartoscBrutto.toFixed(2);
    };
    
    const formatPrice = (price) => {
      return parseFloat(price || 0).toFixed(2);
    };
    
    const onFirmaChange = async () => {
      await loadArtykuly();
    };
    
    const loadArtykuly = async () => {
      await artykulyStore.fetchArtykuly();
    };
    
    const refreshUserData = async () => {
      await authStore.fetchProfile();
      await loadData();
    };
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      error.value = null;
      
      try {
        // Ustaw domyślną firmę jeśli nie wybrano
        if (!form.value.id_firma && authStore.userFirma?.id) {
          form.value.id_firma = authStore.userFirma.id;
        }
        
        let result;
        
        if (isEditMode.value) {
          result = await dokumentyStore.updateDokument(props.dokument.id, form.value);
        } else {
          result = await dokumentyStore.createDokument(form.value);
        }
        
        if (result.success) {
          emit('saved');
        } else {
          error.value = result.error;
          if (result.errors) {
            errors.value = result.errors;
          }
        }
      } catch (err) {
        error.value = 'Wystąpił nieoczekiwany błąd';
      } finally {
        loading.value = false;
      }
    };
    
    // Load data
    const loadData = async () => {
      // Najpierw sprawdź dane użytkownika i odśwież je jeśli potrzeba
      if (!authStore.userFirma && authStore.user?.firma_id) {
        await authStore.fetchProfile();
      }
      
      await Promise.all([
        odbiorcyStore.fetchOdbiorcy(),
        loadArtykuly(),
        authStore.isKsiegowy ? firmyStore.fetchKlienci() : Promise.resolve()
      ]);
      
      // Set default values tylko jeśli nie jesteśmy w trybie edycji
      if (!isEditMode.value) {
        if (authStore.userFirma?.miejsce_wystawienia && !form.value.miejsce_wystawienia) {
          form.value.miejsce_wystawienia = authStore.userFirma.miejsce_wystawienia;
        }
        
        if (authStore.userFirma?.id && !form.value.id_firma) {
          form.value.id_firma = authStore.userFirma.id;
        }
      }
    };
    
    // Watch
    watch(() => props.dokument, async (newDokument) => {
      if (newDokument) {
        // Najpierw załaduj artykuły jeśli jeszcze nie są załadowane
        if (artykulyStore.artykuly.length === 0) {
          await loadArtykuly();
        }
        
        form.value = {
          ...newDokument,
          pozycje: newDokument.pozycje || [],
          // Konwertuj daty do formatu YYYY-MM-DD
          data_sprzedazy: newDokument.data_sprzedazy ? new Date(newDokument.data_sprzedazy).toISOString().split('T')[0] : '',
          data_wystawienia: newDokument.data_wystawienia ? new Date(newDokument.data_wystawienia).toISOString().split('T')[0] : '',
          termin_platnosci: newDokument.termin_platnosci ? new Date(newDokument.termin_platnosci).toISOString().split('T')[0] : ''
        };
      }
    }, { immediate: true });
    
    // Watch dla danych firmy użytkownika
    watch(() => authStore.userFirma, (newFirma) => {
      if (newFirma && !form.value.id_firma && !isEditMode.value) {
        form.value.id_firma = newFirma.id;
        if (newFirma.miejsce_wystawienia && !form.value.miejsce_wystawienia) {
          form.value.miejsce_wystawienia = newFirma.miejsce_wystawienia;
        }
      }
    }, { immediate: true });
    
    // Watch zmiany użytkownika - odśwież profil jeśli potrzeba
    watch(() => authStore.user?.firma_id, async (newFirmaId, oldFirmaId) => {
      if (newFirmaId && newFirmaId !== oldFirmaId) {
        await authStore.fetchProfile();
        await loadArtykuly(); // Przeładuj artykuły dla nowej firmy
        
        // Aktualizuj formularz
        if (authStore.userFirma?.id && !isEditMode.value) {
          form.value.id_firma = authStore.userFirma.id;
          if (authStore.userFirma.miejsce_wystawienia) {
            form.value.miejsce_wystawienia = authStore.userFirma.miejsce_wystawienia;
          }
        }
      }
    });
    
    // Lifecycle
    onMounted(async () => {
      await loadData();
      
      // Jeśli tryb edycji, poczekaj na załadowanie dokumentu
      if (isEditMode.value && props.dokument) {
        // Dokument już jest załadowany przez watcher
      } else if (!isEditMode.value) {
        addPozycja(); // Start with one position for new documents
      }
    });
    
    return {
      form,
      errors,
      loading,
      error,
      isEditMode,
      authStore,
      dostepniOdbiorcy,
      dostepneArtykuly,
      dostepneKlienci,
      firmaNazwa,
      totalNetto,
      totalVat,
      totalBrutto,
      addPozycja,
      removePozycja,
      selectArtykul,
      calculatePozycja,
      formatPrice,
      onFirmaChange,
      refreshUserData,
      handleSubmit
    };
  }
}
</script>