<script>
import { ref, computed, watch } from 'vue';
import { useOdbiorcyStore } from '../../stores/odbiorcy';

export default {
  name: 'OdbiorcaModal',
  props: {
    odbiorca: {
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
    const odbiorcyStore = useOdbiorcyStore();
    
    // Form state
    const form = ref({
      nazwa: '',
      nazwa_pelna: '',
      kod_pocztowy: '',
      miejscowosc: '',
      adres: '',
      nip: '',
      telefon: '',
      email: '',
      kraj: 'Polska',
      typ_odbiorcy: ''
    });
    
    const errors = ref({});
    const loading = ref(false);
    const error = ref(null);
    
    // Computed
    const isEditMode = computed(() => props.isEdit && props.odbiorca);
    
    // Methods - DEFINICJE NAJPIERW
    const resetForm = () => {
      form.value = {
        nazwa: '',
        nazwa_pelna: '',
        kod_pocztowy: '',
        miejscowosc: '',
        adres: '',
        nip: '',
        telefon: '',
        email: '',
        kraj: 'Polska',
        typ_odbiorcy: ''
      };
      errors.value = {};
      error.value = null;
    };
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      error.value = null;
      
      try {
        let result;
        
        if (isEditMode.value) {
          result = await odbiorcyStore.updateOdbiorca(props.odbiorca.id, form.value);
        } else {
          result = await odbiorcyStore.createOdbiorca(form.value);
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
    
    // Watch UŻYWA resetForm DOPIERO PO DEFINICJI
    watch(() => props.odbiorca, (newOdbiorca) => {
      if (newOdbiorca) {
        form.value = { ...newOdbiorca };
      } else {
        resetForm();
      }
    }, { immediate: true });
    
    return {
      form,
      errors,
      loading,
      error,
      isEditMode,
      handleSubmit
    };
  }
}
</script>