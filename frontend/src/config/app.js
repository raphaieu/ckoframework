// Configurações da aplicação Finance Command Center

export const config = {
  // Informações da aplicação
  app: {
    name: 'Finance Command Center',
    version: '1.0.0',
    description: 'Dashboard financeiro para controle de investimentos e trades'
  },

  // Configurações de desenvolvimento
  dev: {
    mode: import.meta.env.DEV,
    apiUrl: import.meta.env.VITE_API_URL || 'http://localhost:8000',
    mockData: true // Usar dados mock em desenvolvimento
  },

  // Configurações financeiras
  finance: {
    defaultCurrency: 'BRL',
    defaultTimezone: 'America/Bahia',
    
    // Fatores de contrato para day trade
    contractFactors: {
      'WIN': 0.2,    // Mini Índice
      'WDO': 10,     // Mini Dólar
      'IND': 0.2,    // Índice
      'WSP': 0.2,    // Mini S&P
      'DOL': 10,     // Dólar
      'BGI': 0.2,    // Mini Ibovespa
      'WTI': 0.2,    // Mini WTI
      'GOL': 0.2     // Mini Ouro
    },

    // Configurações de Forex
    forex: {
      pipFactors: {
        'EURUSD': 10000,
        'GBPUSD': 10000,
        'USDJPY': 100,
        'USDCHF': 10000,
        'AUDUSD': 10000,
        'USDCAD': 10000,
        'NZDUSD': 10000,
        'EURJPY': 100,
        'GBPJPY': 100,
        'AUDJPY': 100,
        'CADJPY': 100,
        'CHFJPY': 100,
        'NZDJPY': 100,
        'EURGBP': 10000,
        'EURAUD': 10000,
        'EURCHF': 10000,
        'EURNZD': 10000,
        'EURCAD': 10000,
        'GBPAUD': 10000,
        'GBPCHF': 10000,
        'GBPNZD': 10000,
        'GBPCAD': 10000,
        'AUDCHF': 10000,
        'AUDNZD': 10000,
        'AUDCAD': 10000,
        'CHFNZD': 10000,
        'CHFCAD': 10000,
        'NZDCAD': 10000
      }
    },

    // Categorias padrão
    defaultCategories: [
      { id: '1', name: 'Salário', color: '#10b981' },
      { id: '2', name: 'Freelance', color: '#3b82f6' },
      { id: '3', name: 'Investimentos', color: '#8b5cf6' },
      { id: '4', name: 'Alimentação', color: '#f59e0b' },
      { id: '5', name: 'Transporte', color: '#ef4444' },
      { id: '6', name: 'Moradia', color: '#6b7280' },
      { id: '7', name: 'Saúde', color: '#ec4899' },
      { id: '8', name: 'Lazer', color: '#14b8a6' },
      { id: '9', name: 'Educação', color: '#8b5cf6' },
      { id: '10', name: 'Vestuário', color: '#f97316' }
    ],

    // Estratégias de trading
    tradingStrategies: [
      'Scalp',
      'Momentum',
      'Breakout',
      'Reversão',
      'Trend Following',
      'Mean Reversion',
      'Arbitragem',
      'News Trading'
    ],

    // Corretoras
    brokers: [
      'Clear',
      'Rico',
      'XP',
      'BTG Pactual',
      'Itaú',
      'Bradesco',
      'Santander',
      'NuInvest',
      'Inter',
      'C6 Bank'
    ],

    // Mercados
    markets: [
      'B3',
      'Forex',
      'Crypto',
      'Commodities',
      'Bonds'
    ]
  },

  // Configurações de UI
  ui: {
    // Tema
    theme: {
      default: 'dark',
      primary: 'blue',
      accent: 'emerald'
    },

    // Paginação
    pagination: {
      defaultPageSize: 10,
      pageSizeOptions: [10, 25, 50, 100]
    },

    // Tabelas
    table: {
      defaultSort: 'desc',
      showFilters: true,
      showActions: true,
      showPagination: true
    },

    // Gráficos
    charts: {
      colors: [
        '#3b82f6', // blue
        '#10b981', // emerald
        '#f59e0b', // amber
        '#ef4444', // red
        '#8b5cf6', // violet
        '#ec4899', // pink
        '#14b8a6', // teal
        '#f97316'  // orange
      ],
      animation: true,
      responsive: true
    }
  },

  // Configurações de upload
  upload: {
    maxSize: 10 * 1024 * 1024, // 10MB
    allowedTypes: [
      'image/jpeg',
      'image/png',
      'image/gif',
      'application/pdf',
      'audio/mpeg',
      'audio/wav',
      'text/csv',
      'application/json'
    ],
    maxFiles: 5
  },

  // Configurações de API
  api: {
    timeout: 30000, // 30 segundos
    retries: 3,
    endpoints: {
      cashflow: '/api/cashflow',
      trades: '/api/trades',
      holdings: '/api/holdings',
      categories: '/api/categories',
      upload: '/api/upload'
    }
  },

  // Configurações de cache
  cache: {
    enabled: true,
    ttl: 5 * 60 * 1000, // 5 minutos
    keys: {
      categories: 'finance_categories',
      user: 'finance_user',
      settings: 'finance_settings'
    }
  }
}

// Utilitários
export const utils = {
  // Formatação de moeda
  formatCurrency: (amount, currency = 'BRL') => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: currency
    }).format(amount)
  },

  // Formatação de número
  formatNumber: (number, decimals = 2) => {
    return new Intl.NumberFormat('pt-BR', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    }).format(number)
  },

  // Formatação de percentual
  formatPercent: (value, decimals = 2) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'percent',
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    }).format(value / 100)
  },

  // Formatação de data
  formatDate: (date, format = 'DD/MM/YYYY') => {
    const d = new Date(date)
    return d.toLocaleDateString('pt-BR')
  },

  // Formatação de data e hora
  formatDateTime: (date) => {
    const d = new Date(date)
    return d.toLocaleString('pt-BR')
  },

  // Gerar cor aleatória
  generateColor: () => {
    const colors = config.ui.charts.colors
    return colors[Math.floor(Math.random() * colors.length)]
  },

  // Validar email
  isValidEmail: (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
  },

  // Debounce
  debounce: (func, wait) => {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  },

  // Throttle
  throttle: (func, limit) => {
    let inThrottle
    return function() {
      const args = arguments
      const context = this
      if (!inThrottle) {
        func.apply(context, args)
        inThrottle = true
        setTimeout(() => inThrottle = false, limit)
      }
    }
  }
}

export default config
