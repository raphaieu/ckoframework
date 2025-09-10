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

export const useTradesStore = defineStore('trades', () => {
  // Estado
  const dayTrades = ref([])
  const forexTrades = ref([])
  const filters = ref({
    period: '30d',
    broker: null,
    market: null,
    symbol: null,
    strategy: null,
    status: null
  })
  const isLoading = ref(false)

  // Configurações de contratos
  const contractFactors = ref({
    'WIN': 0.2,
    'WDO': 10,
    'IND': 0.2,
    'WSP': 0.2,
    'DOL': 10
  })

  // Getters
  const pnlDayTotal = computed(() => 
    dayTrades.value.reduce((sum, trade) => sum + (trade.pnl || 0), 0)
  )

  const pnlForexTotal = computed(() => 
    forexTrades.value.reduce((sum, trade) => sum + (trade.pnl || 0), 0)
  )

  const totalPnL = computed(() => pnlDayTotal.value + pnlForexTotal.value)

  const filteredDayTrades = computed(() => {
    let filtered = dayTrades.value

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
      
      filtered = filtered.filter(trade => 
        dayjs(trade.opened_at).isAfter(startDate)
      )
    }

    // Filtro por corretora
    if (filters.value.broker) {
      filtered = filtered.filter(trade => trade.broker === filters.value.broker)
    }

    // Filtro por mercado
    if (filters.value.market) {
      filtered = filtered.filter(trade => trade.market === filters.value.market)
    }

    // Filtro por símbolo
    if (filters.value.symbol) {
      filtered = filtered.filter(trade => trade.symbol === filters.value.symbol)
    }

    // Filtro por estratégia
    if (filters.value.strategy) {
      filtered = filtered.filter(trade => trade.strategy === filters.value.strategy)
    }

    // Filtro por status
    if (filters.value.status) {
      if (filters.value.status === 'open') {
        filtered = filtered.filter(trade => !trade.closed_at)
      } else if (filters.value.status === 'closed') {
        filtered = filtered.filter(trade => trade.closed_at)
      }
    }

    return filtered.sort((a, b) => 
      dayjs(b.opened_at).diff(dayjs(a.opened_at))
    )
  })

  const filteredForexTrades = computed(() => {
    let filtered = forexTrades.value

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
      
      filtered = filtered.filter(trade => 
        dayjs(trade.opened_at).isAfter(startDate)
      )
    }

    return filtered.sort((a, b) => 
      dayjs(b.opened_at).diff(dayjs(a.opened_at))
    )
  })

  // Actions
  const fetchDayTrades = async (period = '30d', customFilters = {}) => {
    isLoading.value = true
    try {
      await new Promise(resolve => setTimeout(resolve, 500))
      
      if (dayTrades.value.length === 0) {
        loadMockDayTrades()
      }
      
      filters.value = { ...filters.value, period, ...customFilters }
    } catch (error) {
      console.error('Erro ao buscar day trades:', error)
    } finally {
      isLoading.value = false
    }
  }

  const fetchForexTrades = async (period = '30d', customFilters = {}) => {
    isLoading.value = true
    try {
      await new Promise(resolve => setTimeout(resolve, 500))
      
      if (forexTrades.value.length === 0) {
        loadMockForexTrades()
      }
      
      filters.value = { ...filters.value, period, ...customFilters }
    } catch (error) {
      console.error('Erro ao buscar forex trades:', error)
    } finally {
      isLoading.value = false
    }
  }

  const createDayTrade = async (tradeData) => {
    try {
      const newTrade = {
        id: crypto.randomUUID(),
        ...tradeData,
        created_at: dayjs().toISOString()
      }
      
      // Calcular PnL se tiver preço de saída
      if (newTrade.exit_price) {
        newTrade.pnl = calculateDayTradePnL(newTrade)
      }
      
      dayTrades.value.unshift(newTrade)
      return newTrade
    } catch (error) {
      console.error('Erro ao criar day trade:', error)
      throw error
    }
  }

  const createForexTrade = async (tradeData) => {
    try {
      const newTrade = {
        id: crypto.randomUUID(),
        ...tradeData,
        created_at: dayjs().toISOString()
      }
      
      // Calcular PnL se tiver preço de saída
      if (newTrade.exit_price) {
        newTrade.pnl = calculateForexPnL(newTrade)
      }
      
      forexTrades.value.unshift(newTrade)
      return newTrade
    } catch (error) {
      console.error('Erro ao criar forex trade:', error)
      throw error
    }
  }

  const calculateDayTradePnL = (trade) => {
    if (!trade.exit_price || !trade.entry_price) return 0
    
    const factor = contractFactors.value[trade.symbol] || 1
    const priceDiff = trade.side === 'buy' 
      ? trade.exit_price - trade.entry_price
      : trade.entry_price - trade.exit_price
    
    return (priceDiff * trade.qty * factor) - (trade.fees || 0)
  }

  const calculateForexPnL = (trade) => {
    if (!trade.exit_price || !trade.entry_price) return 0
    
    const priceDiff = trade.side === 'buy' 
      ? trade.exit_price - trade.entry_price
      : trade.entry_price - trade.exit_price
    
    // Cálculo simplificado - em produção, considerar conversão de moeda
    const pips = priceDiff * 10000 // Para pares principais
    const pnl = (pips * trade.lot_size * 10) - (trade.commissions || 0) - (trade.swap || 0)
    
    return pnl
  }

  const calculatePips = (trade) => {
    if (!trade.exit_price || !trade.entry_price) return 0
    
    const priceDiff = trade.side === 'buy' 
      ? trade.exit_price - trade.entry_price
      : trade.entry_price - trade.exit_price
    
    // Para pares com JPY, multiplicar por 100, senão por 10000
    const isJPY = trade.pair.includes('JPY')
    return priceDiff * (isJPY ? 100 : 10000)
  }

  const getDuration = (trade) => {
    if (!trade.closed_at) return 'Em aberto'
    
    const start = dayjs(trade.opened_at)
    const end = dayjs(trade.closed_at)
    const duration = end.diff(start)
    
    if (duration < 60000) { // Menos de 1 minuto
      return `${Math.round(duration / 1000)}s`
    } else if (duration < 3600000) { // Menos de 1 hora
      return `${Math.round(duration / 60000)}min`
    } else {
      return `${Math.round(duration / 3600000)}h`
    }
  }

  const loadMockDayTrades = () => {
    const mockTrades = [
      {
        id: '1',
        broker: 'Clear',
        market: 'B3',
        symbol: 'WINV25',
        side: 'buy',
        qty: 1,
        entry_price: 125000,
        exit_price: 125200,
        fees: 2.50,
        pnl: 40.00,
        risk_tag: 'A',
        opened_at: dayjs().subtract(1, 'day').hour(9).minute(30).toISOString(),
        closed_at: dayjs().subtract(1, 'day').hour(9).minute(35).toISOString(),
        strategy: 'Scalp'
      },
      {
        id: '2',
        broker: 'Clear',
        market: 'B3',
        symbol: 'WDOF25',
        side: 'sell',
        qty: 1,
        entry_price: 5250.00,
        exit_price: 5240.00,
        fees: 2.50,
        pnl: 100.00,
        risk_tag: 'B',
        opened_at: dayjs().subtract(2, 'day').hour(14).minute(15).toISOString(),
        closed_at: dayjs().subtract(2, 'day').hour(14).minute(25).toISOString(),
        strategy: 'Momentum'
      }
    ]
    
    dayTrades.value = mockTrades
  }

  const loadMockForexTrades = () => {
    const mockTrades = [
      {
        id: '1',
        pair: 'EURUSD',
        lot_size: 0.1,
        side: 'buy',
        entry_price: 1.0850,
        exit_price: 1.0870,
        sl: 1.0820,
        tp: 1.0880,
        swap: -0.50,
        commissions: 1.00,
        pnl: 20.00,
        opened_at: dayjs().subtract(1, 'day').hour(4).minute(20).toISOString(),
        closed_at: dayjs().subtract(1, 'day').hour(6).minute(5).toISOString()
      }
    ]
    
    forexTrades.value = mockTrades
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
    dayTrades,
    forexTrades,
    filters,
    isLoading,
    contractFactors,
    
    // Getters
    pnlDayTotal,
    pnlForexTotal,
    totalPnL,
    filteredDayTrades,
    filteredForexTrades,
    
    // Actions
    fetchDayTrades,
    fetchForexTrades,
    createDayTrade,
    createForexTrade,
    calculateDayTradePnL,
    calculateForexPnL,
    calculatePips,
    getDuration,
    loadMockDayTrades,
    loadMockForexTrades,
    formatCurrency,
    formatDate
  }
})
