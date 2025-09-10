<template>
  <div class="w-full">
    <!-- Header com filtros -->
    <div v-if="showFilters" class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-2">
        <Input
          v-model="searchQuery"
          placeholder="Buscar..."
          class="w-64"
        />
        <Select v-model="selectedFilter">
          <SelectTrigger class="w-48">
            <SelectValue placeholder="Filtrar por..." />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todos</SelectItem>
            <SelectItem 
              v-for="filter in filterOptions" 
              :key="filter.value" 
              :value="filter.value"
            >
              {{ filter.label }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex items-center space-x-2">
        <Button @click="$emit('refresh')" variant="outline" size="sm">
          <RefreshCw class="h-4 w-4 mr-2" />
          Atualizar
        </Button>
        <Button @click="$emit('export')" variant="outline" size="sm">
          <Download class="h-4 w-4 mr-2" />
          Exportar
        </Button>
      </div>
    </div>

    <!-- Tabela -->
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead 
              v-for="column in columns" 
              :key="column.key"
              :class="[column.class, { 'cursor-pointer hover:bg-muted/50': column.sortable }]"
              @click="column.sortable ? sort(column.key) : null"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <component 
                  v-if="column.sortable"
                  :is="getSortIcon(column.key)"
                  class="h-4 w-4"
                />
              </div>
            </TableHead>
            <TableHead v-if="showActions" class="w-24">Ações</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell :colspan="columns.length + (showActions ? 1 : 0)" class="text-center py-8">
              <div class="flex items-center justify-center space-x-2">
                <Loader2 class="h-4 w-4 animate-spin" />
                <span>Carregando...</span>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-else-if="filteredData.length === 0">
            <TableCell :colspan="columns.length + (showActions ? 1 : 0)" class="text-center py-8">
              <div class="flex flex-col items-center space-y-2">
                <FileX class="h-8 w-8 text-muted-foreground" />
                <span class="text-muted-foreground">Nenhum item encontrado</span>
              </div>
            </TableCell>
          </TableRow>
          <TableRow 
            v-else
            v-for="(item, index) in paginatedData" 
            :key="item.id || index"
            class="hover:bg-muted/50"
          >
            <TableCell 
              v-for="column in columns" 
              :key="column.key"
              :class="column.class"
            >
              <slot 
                :name="column.key" 
                :item="item" 
                :value="getNestedValue(item, column.key)"
              >
                {{ formatValue(getNestedValue(item, column.key), column.format) }}
              </slot>
            </TableCell>
            <TableCell v-if="showActions" class="text-right">
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="ghost" size="sm">
                    <MoreHorizontal class="h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem @click="$emit('edit', item)">
                    <Edit class="h-4 w-4 mr-2" />
                    Editar
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="$emit('view', item)">
                    <Eye class="h-4 w-4 mr-2" />
                    Visualizar
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem 
                    @click="$emit('delete', item)"
                    class="text-red-600"
                  >
                    <Trash2 class="h-4 w-4 mr-2" />
                    Excluir
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- Paginação -->
    <div v-if="showPagination && totalPages > 1" class="flex items-center justify-between mt-4">
      <div class="text-sm text-muted-foreground">
        Mostrando {{ (currentPage - 1) * pageSize + 1 }} a {{ Math.min(currentPage * pageSize, filteredData.length) }} de {{ filteredData.length }} itens
      </div>
      <div class="flex items-center space-x-2">
        <Button
          variant="outline"
          size="sm"
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
        >
          <ChevronLeft class="h-4 w-4" />
        </Button>
        <div class="flex items-center space-x-1">
          <Button
            v-for="page in visiblePages"
            :key="page"
            variant="outline"
            size="sm"
            @click="currentPage = page"
            :class="{ 'bg-primary text-primary-foreground': currentPage === page }"
          >
            {{ page }}
          </Button>
        </div>
        <Button
          variant="outline"
          size="sm"
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
        >
          <ChevronRight class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from '@/components/ui/select'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'
import { 
  RefreshCw, 
  Download, 
  MoreHorizontal, 
  Edit, 
  Eye, 
  Trash2, 
  FileX, 
  Loader2,
  ChevronLeft,
  ChevronRight,
  ArrowUpDown,
  ArrowUp,
  ArrowDown
} from 'lucide-vue-next'

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  showFilters: {
    type: Boolean,
    default: true
  },
  showActions: {
    type: Boolean,
    default: true
  },
  showPagination: {
    type: Boolean,
    default: true
  },
  pageSize: {
    type: Number,
    default: 10
  },
  filterOptions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['edit', 'view', 'delete', 'refresh', 'export'])

// Estado local
const searchQuery = ref('')
const selectedFilter = ref('all')
const currentPage = ref(1)
const sortField = ref('')
const sortDirection = ref('asc')

// Computed
const filteredData = computed(() => {
  let filtered = props.data

  // Filtro por busca
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => {
      return props.columns.some(column => {
        const value = getNestedValue(item, column.key)
        return value && value.toString().toLowerCase().includes(query)
      })
    })
  }

  // Filtro por seleção
  if (selectedFilter.value !== 'all') {
    // Implementar lógica de filtro específica conforme necessário
  }

  // Ordenação
  if (sortField.value) {
    filtered = [...filtered].sort((a, b) => {
      const aValue = getNestedValue(a, sortField.value)
      const bValue = getNestedValue(b, sortField.value)
      
      if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
      if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return filtered
})

const totalPages = computed(() => 
  Math.ceil(filteredData.value.length / props.pageSize)
)

const paginatedData = computed(() => {
  if (!props.showPagination) return filteredData.value
  
  const start = (currentPage.value - 1) * props.pageSize
  const end = start + props.pageSize
  return filteredData.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    } else if (current >= total - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = total - 4; i <= total; i++) pages.push(i)
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    }
  }
  
  return pages
})

// Métodos
const getNestedValue = (obj, path) => {
  return path.split('.').reduce((current, key) => current?.[key], obj)
}

const formatValue = (value, format) => {
  if (value === null || value === undefined) return '-'
  
  switch (format) {
    case 'currency':
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(value)
    case 'date':
      return new Date(value).toLocaleDateString('pt-BR')
    case 'datetime':
      return new Date(value).toLocaleString('pt-BR')
    case 'number':
      return new Intl.NumberFormat('pt-BR').format(value)
    case 'percent':
      return new Intl.NumberFormat('pt-BR', {
        style: 'percent',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value / 100)
    default:
      return value.toString()
  }
}

const sort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

const getSortIcon = (field) => {
  if (sortField.value !== field) return ArrowUpDown
  return sortDirection.value === 'asc' ? ArrowUp : ArrowDown
}

// Watchers
watch(() => props.data, () => {
  currentPage.value = 1
})

watch(searchQuery, () => {
  currentPage.value = 1
})

watch(selectedFilter, () => {
  currentPage.value = 1
})
</script>
