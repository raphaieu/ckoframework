<template>
  <div class="flex items-center space-x-2">
    <!-- Seletor de período rápido -->
    <Select v-model="selectedPeriod" @update:model-value="handlePeriodChange">
      <SelectTrigger class="w-32">
        <SelectValue placeholder="Período" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="7d">7 dias</SelectItem>
        <SelectItem value="30d">30 dias</SelectItem>
        <SelectItem value="90d">90 dias</SelectItem>
        <SelectItem value="1y">1 ano</SelectItem>
        <SelectItem value="custom">Personalizado</SelectItem>
      </SelectContent>
    </Select>

    <!-- Seletor de data customizado -->
    <div v-if="selectedPeriod === 'custom'" class="flex items-center space-x-2">
      <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
          <Button variant="outline" class="w-64 justify-start text-left font-normal">
            <Calendar class="mr-2 h-4 w-4" />
            <span v-if="dateRange.from && dateRange.to">
              {{ formatDate(dateRange.from) }} - {{ formatDate(dateRange.to) }}
            </span>
            <span v-else>Selecionar período</span>
          </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
          <Calendar
            v-model:from="dateRange.from"
            v-model:to="dateRange.to"
            :number-of-months="2"
            initial-focus
          />
          <div class="p-3 border-t">
            <Button @click="applyCustomRange" class="w-full">
              Aplicar
            </Button>
          </div>
        </PopoverContent>
      </Popover>
    </div>

    <!-- Indicador de período atual -->
    <div v-if="selectedPeriod !== 'custom'" class="text-sm text-muted-foreground">
      {{ getPeriodLabel(selectedPeriod) }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from '@/components/ui/select'
import { 
  Popover, 
  PopoverContent, 
  PopoverTrigger 
} from '@/components/ui/popover'
import { Calendar } from '@/components/ui/calendar'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import dayjs from 'dayjs'
import 'dayjs/locale/pt-br'
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'

// Configurar dayjs
dayjs.locale('pt-br')
dayjs.extend(utc)
dayjs.extend(timezone)

const props = defineProps({
  modelValue: {
    type: String,
    default: '30d'
  },
  timezone: {
    type: String,
    default: 'America/Bahia'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Estado local
const selectedPeriod = ref(props.modelValue)
const isOpen = ref(false)
const dateRange = ref({
  from: null,
  to: null
})

// Computed
const currentPeriod = computed(() => {
  const now = dayjs().tz(props.timezone)
  
  switch (selectedPeriod.value) {
    case '7d':
      return {
        from: now.subtract(7, 'day').startOf('day'),
        to: now.endOf('day')
      }
    case '30d':
      return {
        from: now.subtract(30, 'day').startOf('day'),
        to: now.endOf('day')
      }
    case '90d':
      return {
        from: now.subtract(90, 'day').startOf('day'),
        to: now.endOf('day')
      }
    case '1y':
      return {
        from: now.subtract(1, 'year').startOf('day'),
        to: now.endOf('day')
      }
    case 'custom':
      return {
        from: dateRange.value.from ? dayjs(dateRange.value.from) : null,
        to: dateRange.value.to ? dayjs(dateRange.value.to) : null
      }
    default:
      return {
        from: now.subtract(30, 'day').startOf('day'),
        to: now.endOf('day')
      }
  }
})

// Métodos
const handlePeriodChange = (period) => {
  selectedPeriod.value = period
  emit('update:modelValue', period)
  
  if (period !== 'custom') {
    emit('change', {
      period,
      from: currentPeriod.value.from?.toISOString(),
      to: currentPeriod.value.to?.toISOString()
    })
  }
}

const applyCustomRange = () => {
  if (dateRange.value.from && dateRange.value.to) {
    isOpen.value = false
    emit('change', {
      period: 'custom',
      from: dayjs(dateRange.value.from).startOf('day').toISOString(),
      to: dayjs(dateRange.value.to).endOf('day').toISOString()
    })
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return dayjs(date).tz(props.timezone).format('DD/MM/YYYY')
}

const getPeriodLabel = (period) => {
  const now = dayjs().tz(props.timezone)
  
  switch (period) {
    case '7d':
      return `${now.subtract(7, 'day').format('DD/MM')} - ${now.format('DD/MM')}`
    case '30d':
      return `${now.subtract(30, 'day').format('DD/MM')} - ${now.format('DD/MM')}`
    case '90d':
      return `${now.subtract(90, 'day').format('DD/MM')} - ${now.format('DD/MM')}`
    case '1y':
      return `${now.subtract(1, 'year').format('DD/MM/YYYY')} - ${now.format('DD/MM/YYYY')}`
    default:
      return ''
  }
}

const setCustomRange = (from, to) => {
  dateRange.value = {
    from: from ? new Date(from) : null,
    to: to ? new Date(to) : null
  }
  selectedPeriod.value = 'custom'
  emit('update:modelValue', 'custom')
}

// Watchers
watch(() => props.modelValue, (newValue) => {
  selectedPeriod.value = newValue
})

// Expor métodos para uso externo
defineExpose({
  setCustomRange,
  currentPeriod
})
</script>
