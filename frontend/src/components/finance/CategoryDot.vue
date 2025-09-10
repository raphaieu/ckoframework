<template>
  <div class="flex items-center space-x-2">
    <div 
      class="w-3 h-3 rounded-full flex-shrink-0"
      :style="{ backgroundColor: color }"
    />
    <span class="text-sm font-medium">{{ name }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  category: {
    type: [Object, String],
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const name = computed(() => {
  if (typeof props.category === 'string') {
    const found = props.categories.find(c => c.id === props.category)
    return found?.name || 'Sem categoria'
  }
  return props.category?.name || 'Sem categoria'
})

const color = computed(() => {
  if (typeof props.category === 'string') {
    const found = props.categories.find(c => c.id === props.category)
    return found?.color || '#6b7280'
  }
  return props.category?.color || '#6b7280'
})
</script>
