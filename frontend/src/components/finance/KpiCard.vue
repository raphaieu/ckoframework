<template>
  <Card class="relative overflow-hidden">
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-sm font-medium text-muted-foreground">
        {{ title }}
      </CardTitle>
      <component :is="icon" class="h-4 w-4 text-muted-foreground" />
    </CardHeader>
    <CardContent>
      <div class="text-2xl font-bold">{{ formattedValue }}</div>
      <div v-if="change !== undefined" class="flex items-center text-xs">
        <component 
          :is="changeIcon" 
          class="h-3 w-3 mr-1"
          :class="changeClass"
        />
        <span :class="changeClass">
          {{ Math.abs(change) }}% em relação ao período anterior
        </span>
      </div>
      <div v-if="subtitle" class="text-xs text-muted-foreground mt-1">
        {{ subtitle }}
      </div>
    </CardContent>
    <div 
      v-if="trend"
      class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r"
      :class="trendClass"
    />
  </Card>
</template>

<script setup>
import { computed } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { TrendingUp, TrendingDown, Minus } from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  change: {
    type: Number,
    default: undefined
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: [String, Object],
    default: 'Minus'
  },
  format: {
    type: String,
    default: 'currency', // currency, number, percent
    validator: (value) => ['currency', 'number', 'percent'].includes(value)
  },
  currency: {
    type: String,
    default: 'BRL'
  },
  trend: {
    type: String,
    default: 'neutral', // positive, negative, neutral
    validator: (value) => ['positive', 'negative', 'neutral'].includes(value)
  }
})

const formattedValue = computed(() => {
  if (typeof props.value === 'string') return props.value
  
  switch (props.format) {
    case 'currency':
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: props.currency
      }).format(props.value)
    case 'percent':
      return new Intl.NumberFormat('pt-BR', {
        style: 'percent',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(props.value / 100)
    case 'number':
      return new Intl.NumberFormat('pt-BR').format(props.value)
    default:
      return props.value.toString()
  }
})

const changeIcon = computed(() => {
  if (props.change === undefined) return null
  return props.change > 0 ? TrendingUp : props.change < 0 ? TrendingDown : Minus
})

const changeClass = computed(() => {
  if (props.change === undefined) return ''
  return props.change > 0 
    ? 'text-green-600' 
    : props.change < 0 
      ? 'text-red-600' 
      : 'text-muted-foreground'
})

const trendClass = computed(() => {
  switch (props.trend) {
    case 'positive':
      return 'from-green-500 to-green-600'
    case 'negative':
      return 'from-red-500 to-red-600'
    default:
      return 'from-gray-400 to-gray-500'
  }
})
</script>
