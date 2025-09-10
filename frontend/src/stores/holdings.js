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

export const useHoldingsStore = defineStore('holdings', () => {
  // Estado
  const swingPositions = ref([])
  const cryptoHoldings = ref([])
  const isLoading = ref(false)

  // Getters
  const swingEquity = computed(() => 
    swingPositions.value.reduce((sum, position) => {
      const currentValue = position.current_price * position.qty
      const costBasis = position.lots.reduce((sum, lot) => sum + (lot.price * lot.qty), 0)
      return sum + (currentValue - costBasis)
    }, 0)
  )

  const cryptoEquity = computed(() => 
    cryptoHoldings.value.reduce((sum, holding) => {
      const currentValue = holding.current_price * holding.totalQty
      const costBasis = holding.lots.reduce((sum, lot) => sum + (lot.price * lot.qty), 0)
      return sum + (currentValue - costBasis)
    }, 0)
  )

  const totalEquity = computed(() => swingEquity.value + cryptoEquity.value)

  const swingPositionsWithPnL = computed(() => 
    swingPositions.value.map(position => {
      const currentValue = position.current_price * position.qty
      const costBasis = position.lots.reduce((sum, lot) => sum + (lot.price * lot.qty), 0)
      const pnl = currentValue - costBasis
      const pnlPercent = costBasis > 0 ? (pnl / costBasis) * 100 : 0
      
      return {
        ...position,
        currentValue,
        costBasis,
        pnl,
        pnlPercent
      }
    })
  )

  const cryptoHoldingsWithPnL = computed(() => 
    cryptoHoldings.value.map(holding => {
      const currentValue = holding.current_price * holding.totalQty
      const costBasis = holding.lots.reduce((sum, lot) => sum + (lot.price * lot.qty), 0)
      const pnl = currentValue - costBasis
      const pnlPercent = costBasis > 0 ? (pnl / costBasis) * 100 : 0
      
      return {
        ...holding,
        currentValue,
        costBasis,
        pnl,
        pnlPercent
      }
    })
  )

  // Actions
  const fetchSwingPositions = async () => {
    isLoading.value = true
    try {
      await new Promise(resolve => setTimeout(resolve, 500))
      
      if (swingPositions.value.length === 0) {
        loadMockSwingPositions()
      }
    } catch (error) {
      console.error('Erro ao buscar posições swing:', error)
    } finally {
      isLoading.value = false
    }
  }

  const fetchCryptoHoldings = async () => {
    isLoading.value = true
    try {
      await new Promise(resolve => setTimeout(resolve, 500))
      
      if (cryptoHoldings.value.length === 0) {
        loadMockCryptoHoldings()
      }
    } catch (error) {
      console.error('Erro ao buscar holdings de cripto:', error)
    } finally {
      isLoading.value = false
    }
  }

  const addDividend = async (positionId, dividendData) => {
    try {
      const position = swingPositions.value.find(p => p.id === positionId)
      if (position) {
        const newDividend = {
          id: crypto.randomUUID(),
          ...dividendData,
          received_at: dividendData.received_at || dayjs().toISOString()
        }
        
        position.dividends.push(newDividend)
        return newDividend
      }
      throw new Error('Posição não encontrada')
    } catch (error) {
      console.error('Erro ao adicionar dividendo:', error)
      throw error
    }
  }

  const addLot = async (positionId, lotData) => {
    try {
      const position = swingPositions.value.find(p => p.id === positionId)
      if (position) {
        const newLot = {
          id: crypto.randomUUID(),
          ...lotData,
          bought_at: lotData.bought_at || dayjs().toISOString()
        }
        
        position.lots.push(newLot)
        
        // Recalcular quantidade total
        position.qty = position.lots.reduce((sum, lot) => sum + lot.qty, 0)
        
        return newLot
      }
      throw new Error('Posição não encontrada')
    } catch (error) {
      console.error('Erro ao adicionar lote:', error)
      throw error
    }
  }

  const addCryptoLot = async (holdingId, lotData) => {
    try {
      const holding = cryptoHoldings.value.find(h => h.id === holdingId)
      if (holding) {
        const newLot = {
          id: crypto.randomUUID(),
          ...lotData,
          bought_at: lotData.bought_at || dayjs().toISOString()
        }
        
        holding.lots.push(newLot)
        
        // Recalcular quantidade total
        holding.totalQty = holding.lots.reduce((sum, lot) => sum + lot.qty, 0)
        
        return newLot
      }
      throw new Error('Holding não encontrado')
    } catch (error) {
      console.error('Erro ao adicionar lote de cripto:', error)
      throw error
    }
  }

  const updateCurrentPrice = async (positionId, newPrice) => {
    try {
      const position = swingPositions.value.find(p => p.id === positionId)
      if (position) {
        position.current_price = newPrice
        return position
      }
      throw new Error('Posição não encontrada')
    } catch (error) {
      console.error('Erro ao atualizar preço:', error)
      throw error
    }
  }

  const updateCryptoPrice = async (holdingId, newPrice) => {
    try {
      const holding = cryptoHoldings.value.find(h => h.id === holdingId)
      if (holding) {
        holding.current_price = newPrice
        return holding
      }
      throw new Error('Holding não encontrado')
    } catch (error) {
      console.error('Erro ao atualizar preço de cripto:', error)
      throw error
    }
  }

  const loadMockSwingPositions = () => {
    const mockPositions = [
      {
        id: '1',
        broker: 'NuInvest',
        symbol: 'VALE3',
        qty: 100,
        lots: [
          {
            id: '1',
            qty: 50,
            price: 65.50,
            bought_at: dayjs().subtract(30, 'day').toISOString()
          },
          {
            id: '2',
            qty: 50,
            price: 68.20,
            bought_at: dayjs().subtract(15, 'day').toISOString()
          }
        ],
        current_price: 72.30,
        dividends: [
          {
            id: '1',
            amount: 1.50,
            currency: 'BRL',
            received_at: dayjs().subtract(10, 'day').toISOString(),
            note: 'Dividendo trimestral'
          }
        ]
      },
      {
        id: '2',
        broker: 'Clear',
        symbol: 'PETR4',
        qty: 200,
        lots: [
          {
            id: '3',
            qty: 200,
            price: 28.90,
            bought_at: dayjs().subtract(45, 'day').toISOString()
          }
        ],
        current_price: 32.15,
        dividends: []
      }
    ]
    
    swingPositions.value = mockPositions
  }

  const loadMockCryptoHoldings = () => {
    const mockHoldings = [
      {
        id: '1',
        wallet: 'Binance',
        chain: 'BSC',
        asset: 'BTC',
        totalQty: 0.05,
        lots: [
          {
            id: '1',
            qty: 0.03,
            price: 180000.00,
            currency: 'BRL',
            bought_at: dayjs().subtract(60, 'day').toISOString()
          },
          {
            id: '2',
            qty: 0.02,
            price: 195000.00,
            currency: 'BRL',
            bought_at: dayjs().subtract(30, 'day').toISOString()
          }
        ],
        current_price: 210000.00,
        currency: 'BRL'
      },
      {
        id: '2',
        wallet: 'MetaMask',
        chain: 'ETH',
        asset: 'ETH',
        totalQty: 2.5,
        lots: [
          {
            id: '3',
            qty: 2.5,
            price: 12000.00,
            currency: 'BRL',
            bought_at: dayjs().subtract(90, 'day').toISOString()
          }
        ],
        current_price: 13500.00,
        currency: 'BRL'
      }
    ]
    
    cryptoHoldings.value = mockHoldings
  }

  const formatCurrency = (amount, currency = 'BRL') => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: currency
    }).format(amount)
  }

  const formatDate = (date) => {
    return dayjs(date).tz('America/Bahia').format('DD/MM/YYYY')
  }

  const formatPercent = (value) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'percent',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(value / 100)
  }

  return {
    // Estado
    swingPositions,
    cryptoHoldings,
    isLoading,
    
    // Getters
    swingEquity,
    cryptoEquity,
    totalEquity,
    swingPositionsWithPnL,
    cryptoHoldingsWithPnL,
    
    // Actions
    fetchSwingPositions,
    fetchCryptoHoldings,
    addDividend,
    addLot,
    addCryptoLot,
    updateCurrentPrice,
    updateCryptoPrice,
    loadMockSwingPositions,
    loadMockCryptoHoldings,
    formatCurrency,
    formatDate,
    formatPercent
  }
})
