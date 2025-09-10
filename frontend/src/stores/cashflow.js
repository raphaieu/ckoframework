import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import dayjs from 'dayjs'
import 'dayjs/locale/pt-br'
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'

// Configurar dayjs
dayjs.locale('pt-br')
dayjs.extend(utc)
dayjs.extend(timezone)

export const useCashflowStore = defineStore('cashflow', () => {
  // Estado
  const items = ref([])
  const filters = ref({
    period: '30d',
    type: null,
    category: null,
    search: ''
  })
  const isLoading = ref(false)
  const categories = ref([
    { id: '1', name: 'Salário', color: '#10b981' },
    { id: '2', name: 'Freelance', color: '#3b82f6' },
    { id: '3', name: 'Investimentos', color: '#8b5cf6' },
    { id: '4', name: 'Alimentação', color: '#f59e0b' },
    { id: '5', name: 'Transporte', color: '#ef4444' },
    { id: '6', name: 'Moradia', color: '#6b7280' },
    { id: '7', name: 'Saúde', color: '#ec4899' },
    { id: '8', name: 'Lazer', color: '#14b8a6' }
  ])

  // Getters
  const incomeTotal = computed(() => 
    items.value
      .filter(item => item.type === 'income')
      .reduce((sum, item) => sum + item.amount, 0)
  )

  const expenseTotal = computed(() => 
    items.value
      .filter(item => item.type === 'expense')
      .reduce((sum, item) => sum + item.amount, 0)
  )

  const balance = computed(() => incomeTotal.value - expenseTotal.value)

  const filteredItems = computed(() => {
    let filtered = items.value

    // Filtro por período
    if (filters.value.period) {
      const now = dayjs().tz('America/Bahia')
      let startDate
      
      switch (filters.value.period) {
        case '7d':
          startDate = now.subtract(7, 'day')
          break
        case '30d':
          startDate = now.subtract(30, 'day')
          break
        case '90d':
          startDate = now.subtract(90, 'day')
          break
        case '1y':
          startDate = now.subtract(1, 'year')
          break
        default:
          startDate = now.subtract(30, 'day')
      }
      
      filtered = filtered.filter(item => 
        dayjs(item.occurred_at).isAfter(startDate)
      )
    }

    // Filtro por tipo
    if (filters.value.type) {
      filtered = filtered.filter(item => item.type === filters.value.type)
    }

    // Filtro por categoria
    if (filters.value.category) {
      filtered = filtered.filter(item => item.category_id === filters.value.category)
    }

    // Filtro por busca
    if (filters.value.search) {
      const search = filters.value.search.toLowerCase()
      filtered = filtered.filter(item => 
        item.source.toLowerCase().includes(search) ||
        item.notes?.toLowerCase().includes(search)
      )
    }

    return filtered.sort((a, b) => 
      dayjs(b.occurred_at).diff(dayjs(a.occurred_at))
    )
  })

  const groupByCategory = computed(() => {
    const grouped = {}
    filteredItems.value.forEach(item => {
      const category = categories.value.find(c => c.id === item.category_id)
      const categoryName = category?.name || 'Sem categoria'
      
      if (!grouped[categoryName]) {
        grouped[categoryName] = {
          name: categoryName,
          color: category?.color || '#6b7280',
          income: 0,
          expense: 0,
          balance: 0
        }
      }
      
      if (item.type === 'income') {
        grouped[categoryName].income += item.amount
      } else {
        grouped[categoryName].expense += item.amount
      }
      
      grouped[categoryName].balance = grouped[categoryName].income - grouped[categoryName].expense
    })
    
    return Object.values(grouped)
  })

  const recent = computed(() => (n = 5) => 
    filteredItems.value.slice(0, n)
  )

  // Actions
  const fetch = async (period = '30d', customFilters = {}) => {
    isLoading.value = true
    try {
      // Simular API call - em produção, isso seria uma chamada real
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Carregar dados mock se não houver dados
      if (items.value.length === 0) {
        loadMockData()
      }
      
      filters.value = { ...filters.value, period, ...customFilters }
    } catch (error) {
      console.error('Erro ao buscar transações:', error)
    } finally {
      isLoading.value = false
    }
  }

  const create = async (transactionData) => {
    try {
      const newTransaction = {
        id: crypto.randomUUID(),
        ...transactionData,
        created_at: dayjs().toISOString()
      }
      
      items.value.unshift(newTransaction)
      return newTransaction
    } catch (error) {
      console.error('Erro ao criar transação:', error)
      throw error
    }
  }

  const update = async (id, transactionData) => {
    try {
      const index = items.value.findIndex(item => item.id === id)
      if (index !== -1) {
        items.value[index] = { ...items.value[index], ...transactionData }
        return items.value[index]
      }
      throw new Error('Transação não encontrada')
    } catch (error) {
      console.error('Erro ao atualizar transação:', error)
      throw error
    }
  }

  const remove = async (id) => {
    try {
      const index = items.value.findIndex(item => item.id === id)
      if (index !== -1) {
        items.value.splice(index, 1)
        return true
      }
      throw new Error('Transação não encontrada')
    } catch (error) {
      console.error('Erro ao remover transação:', error)
      throw error
    }
  }

  const loadMockData = () => {
    // Dados mock para desenvolvimento
    const mockTransactions = [
      {
        id: '1',
        type: 'income',
        category_id: '1',
        source: 'Salário',
        amount: 5000.00,
        currency: 'BRL',
        occurred_at: dayjs().subtract(1, 'day').toISOString(),
        notes: 'Salário mensal'
      },
      {
        id: '2',
        type: 'expense',
        category_id: '4',
        source: 'Supermercado',
        amount: 250.50,
        currency: 'BRL',
        occurred_at: dayjs().subtract(2, 'day').toISOString(),
        notes: 'Compras do mês'
      },
      {
        id: '3',
        type: 'income',
        category_id: '2',
        source: 'Freelance',
        amount: 1200.00,
        currency: 'BRL',
        occurred_at: dayjs().subtract(3, 'day').toISOString(),
        notes: 'Projeto de desenvolvimento'
      },
      {
        id: '4',
        type: 'expense',
        category_id: '5',
        source: 'Uber',
        amount: 45.00,
        currency: 'BRL',
        occurred_at: dayjs().subtract(4, 'day').toISOString(),
        notes: 'Viagem para o trabalho'
      },
      {
        id: '5',
        type: 'income',
        category_id: '3',
        source: 'Dividendos',
        amount: 150.00,
        currency: 'BRL',
        occurred_at: dayjs().subtract(5, 'day').toISOString(),
        notes: 'Dividendos de ações'
      }
    ]
    
    items.value = mockTransactions
  }

  const formatCurrency = (amount, currency = 'BRL') => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: currency
    }).format(amount)
  }

  const formatDate = (date) => {
    return dayjs(date).tz('America/Bahia').format('DD/MM/YYYY HH:mm')
  }

  return {
    // Estado
    items,
    filters,
    isLoading,
    categories,
    
    // Getters
    incomeTotal,
    expenseTotal,
    balance,
    filteredItems,
    groupByCategory,
    recent,
    
    // Actions
    fetch,
    create,
    update,
    remove,
    loadMockData,
    formatCurrency,
    formatDate
  }
})
