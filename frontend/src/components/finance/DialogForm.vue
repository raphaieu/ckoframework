<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <slot name="trigger">
        <Button>{{ triggerText }}</Button>
      </slot>
    </DialogTrigger>
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <DialogDescription v-if="description">
          {{ description }}
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <slot 
          :form="form" 
          :errors="errors" 
          :isSubmitting="isSubmitting"
        />
        
        <DialogFooter class="flex flex-col sm:flex-row gap-2">
          <Button 
            type="button" 
            variant="outline" 
            @click="handleCancel"
            :disabled="isSubmitting"
          >
            Cancelar
          </Button>
          <Button 
            type="submit" 
            :disabled="isSubmitting"
          >
            <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
            {{ submitText }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from '@/components/ui/dialog'
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  triggerText: {
    type: String,
    default: 'Abrir'
  },
  submitText: {
    type: String,
    default: 'Salvar'
  },
  initialData: {
    type: Object,
    default: () => ({})
  },
  validationSchema: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:open', 'submit', 'cancel'])

// Estado local
const isOpen = ref(props.open)
const isSubmitting = ref(false)
const form = ref({ ...props.initialData })
const errors = ref({})

// Métodos
const handleSubmit = async () => {
  try {
    isSubmitting.value = true
    errors.value = {}
    
    // Validação básica (pode ser expandida com vee-validate + zod)
    if (props.validationSchema) {
      // Implementar validação com schema
    }
    
    await emit('submit', form.value)
    handleClose()
  } catch (error) {
    console.error('Erro ao submeter formulário:', error)
    if (error.errors) {
      errors.value = error.errors
    }
  } finally {
    isSubmitting.value = false
  }
}

const handleCancel = () => {
  emit('cancel')
  handleClose()
}

const handleClose = () => {
  isOpen.value = false
  emit('update:open', false)
  resetForm()
}

const resetForm = () => {
  form.value = { ...props.initialData }
  errors.value = {}
}

// Watchers
watch(() => props.open, (newValue) => {
  isOpen.value = newValue
})

watch(() => props.initialData, (newData) => {
  form.value = { ...newData }
}, { deep: true })

watch(isOpen, (newValue) => {
  emit('update:open', newValue)
})
</script>
