# CKO Framework Frontend - Documentação Técnica

## 🎨 Visão Geral

O frontend do CKO Framework é construído com **Vue 3**, **Vite**, **TailwindCSS** e **shadcn-vue**, oferecendo uma interface moderna e responsiva para gestão financeira pessoal com integração de AI.

## 🏗️ Arquitetura do Frontend

```
┌─────────────────┐
│   Vue 3 App     │
│   (main.js)     │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│   Vue Router    │
│   (SPA Routes)  │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│   Pinia Store   │
│   (State Mgmt)  │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│   Components    │
│   (UI Library)  │
└─────────────────┘
```

## 🛠️ Tecnologias Utilizadas

### Core Framework
- **Vue 3** - Framework JavaScript reativo
- **Vite** - Build tool e dev server
- **Vue Router** - Roteamento SPA
- **Pinia** - Gerenciamento de estado

### UI & Styling
- **TailwindCSS** - Framework CSS utilitário
- **shadcn-vue** - Componentes UI modernos
- **Lucide Vue** - Ícones
- **VueUse** - Composables utilitários

### HTTP & API
- **Axios** - Cliente HTTP
- **Fetch API** - Requisições nativas

## 📁 Estrutura de Diretórios

```
frontend/
├── src/
│   ├── components/          # Componentes reutilizáveis
│   │   ├── ui/             # Componentes base (shadcn-vue)
│   │   ├── finance/        # Componentes específicos de finanças
│   │   └── layout/         # Componentes de layout
│   ├── views/              # Páginas da aplicação
│   │   ├── Dashboard.vue   # Dashboard principal
│   │   ├── Cashflow.vue    # Gestão de fluxo de caixa
│   │   ├── Trades.vue      # Gestão de trades
│   │   ├── Holdings.vue    # Gestão de holdings
│   │   └── AI.vue          # Interface de AI
│   ├── router/             # Configuração de rotas
│   │   └── index.js        # Definição de rotas
│   ├── stores/             # Estado global (Pinia)
│   │   ├── auth.js         # Estado de autenticação
│   │   ├── finance.js      # Estado financeiro
│   │   └── ai.js           # Estado de AI
│   ├── lib/                # Utilitários e helpers
│   │   ├── api.js          # Cliente API
│   │   ├── auth.js         # Utilitários de autenticação
│   │   └── utils/          # Funções utilitárias
│   ├── assets/             # Recursos estáticos
│   └── main.js             # Ponto de entrada
├── public/                 # Arquivos públicos
├── package.json            # Dependências
├── vite.config.js          # Configuração do Vite
└── tailwind.config.js      # Configuração do Tailwind
```

## 🎯 Componentes Principais

### Layout Components

#### AppSidebar.vue
```vue
<template>
  <aside class="w-64 bg-white border-r">
    <nav class="space-y-2 p-4">
      <router-link to="/dashboard" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
        <Home class="w-5 h-5" />
        <span>Dashboard</span>
      </router-link>
      <router-link to="/ai" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
        <Bot class="w-5 h-5" />
        <span>AI Financeiro</span>
      </router-link>
    </nav>
  </aside>
</template>
```

#### AppHeader.vue
```vue
<template>
  <header class="bg-white border-b px-6 py-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">{{ title }}</h1>
      <div class="flex items-center space-x-4">
        <Button @click="logout">Sair</Button>
      </div>
    </div>
  </header>
</template>
```

### Finance Components

#### FinancialCard.vue
```vue
<template>
  <Card class="p-6">
    <CardHeader>
      <CardTitle class="flex items-center justify-between">
        <span>{{ title }}</span>
        <Badge :variant="variant">{{ change }}</Badge>
      </CardTitle>
    </CardHeader>
    <CardContent>
      <div class="text-3xl font-bold">{{ value }}</div>
      <p class="text-sm text-muted-foreground">{{ description }}</p>
    </CardContent>
  </Card>
</template>
```

#### TransactionForm.vue
```vue
<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <Label for="type">Tipo</Label>
      <Select v-model="form.type">
        <SelectItem value="income">Receita</SelectItem>
        <SelectItem value="expense">Despesa</SelectItem>
      </Select>
    </div>
    
    <div>
      <Label for="amount">Valor</Label>
      <Input
        id="amount"
        v-model="form.amount"
        type="number"
        step="0.01"
        required
      />
    </div>
    
    <Button type="submit" :disabled="loading">
      {{ loading ? 'Salvando...' : 'Salvar' }}
    </Button>
  </form>
</template>
```

### AI Components

#### AIChat.vue
```vue
<template>
  <div class="flex flex-col h-full">
    <div class="flex-1 overflow-y-auto p-4 space-y-4">
      <div
        v-for="message in messages"
        :key="message.id"
        :class="[
          'flex',
          message.role === 'user' ? 'justify-end' : 'justify-start'
        ]"
      >
        <div
          :class="[
            'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
            message.role === 'user'
              ? 'bg-blue-500 text-white'
              : 'bg-gray-100 text-gray-900'
          ]"
        >
          {{ message.content }}
        </div>
      </div>
    </div>
    
    <div class="border-t p-4">
      <form @submit.prevent="sendMessage" class="flex space-x-2">
        <Input
          v-model="newMessage"
          placeholder="Digite sua pergunta..."
          class="flex-1"
        />
        <Button type="submit" :disabled="loading">
          <Send class="w-4 h-4" />
        </Button>
      </form>
    </div>
  </div>
</template>
```

## 🗂️ Gerenciamento de Estado (Pinia)

### Auth Store
```javascript
// stores/auth.js
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false
  }),

  actions: {
    async login(credentials) {
      try {
        const response = await api.post('/auth/login', credentials)
        this.token = response.data.token
        this.user = response.data.user
        this.isAuthenticated = true
        localStorage.setItem('token', this.token)
      } catch (error) {
        throw new Error('Login failed')
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
    }
  }
})
```

### Finance Store
```javascript
// stores/finance.js
import { defineStore } from 'pinia'

export const useFinanceStore = defineStore('finance', {
  state: () => ({
    cashflow: [],
    trades: [],
    holdings: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchCashflow() {
      this.loading = true
      try {
        const response = await api.get('/cashflow')
        this.cashflow = response.data.data
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async addTransaction(transaction) {
      try {
        const response = await api.post('/cashflow', transaction)
        this.cashflow.push(response.data.data)
      } catch (error) {
        this.error = error.message
      }
    }
  }
})
```

### AI Store
```javascript
// stores/ai.js
import { defineStore } from 'pinia'

export const useAIStore = defineStore('ai', {
  state: () => ({
    messages: [],
    loading: false,
    status: 'inactive'
  }),

  actions: {
    async sendMessage(message) {
      this.messages.push({
        id: Date.now(),
        role: 'user',
        content: message
      })

      this.loading = true
      try {
        const response = await api.post('/ai/analyze', {
          query: message
        })
        
        this.messages.push({
          id: Date.now() + 1,
          role: 'assistant',
          content: response.data.response
        })
      } catch (error) {
        this.messages.push({
          id: Date.now() + 1,
          role: 'assistant',
          content: 'Desculpe, ocorreu um erro. Tente novamente.'
        })
      } finally {
        this.loading = false
      }
    }
  }
})
```

## 🛣️ Roteamento

### Router Configuration
```javascript
// router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cashflow',
    name: 'Cashflow',
    component: () => import('@/views/Cashflow.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/trades',
    name: 'Trades',
    component: () => import('@/views/Trades.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/holdings',
    name: 'Holdings',
    component: () => import('@/views/Holdings.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/ai',
    name: 'AI',
    component: () => import('@/views/AI.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router
```

## 🌐 Cliente API

### API Client
```javascript
// lib/api.js
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 10000
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
```

## 🎨 Styling e UI

### TailwindCSS Configuration
```javascript
// tailwind.config.js
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        }
      }
    },
  },
  plugins: [],
}
```

### Component Styling
```vue
<template>
  <div class="bg-white rounded-lg shadow-sm border p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      {{ title }}
    </h2>
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <span class="text-sm text-gray-600">Total</span>
        <span class="text-lg font-medium text-gray-900">
          {{ formatCurrency(total) }}
        </span>
      </div>
    </div>
  </div>
</template>
```

## 🧪 Testes

### Testes de Componentes
```javascript
// tests/components/FinancialCard.test.js
import { mount } from '@vue/test-utils'
import FinancialCard from '@/components/FinancialCard.vue'

describe('FinancialCard', () => {
  it('renders title and value correctly', () => {
    const wrapper = mount(FinancialCard, {
      props: {
        title: 'Total Receitas',
        value: 'R$ 5.000,00',
        change: '+10%',
        variant: 'positive'
      }
    })

    expect(wrapper.text()).toContain('Total Receitas')
    expect(wrapper.text()).toContain('R$ 5.000,00')
    expect(wrapper.text()).toContain('+10%')
  })
})
```

### Testes de Stores
```javascript
// tests/stores/auth.test.js
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should login user successfully', async () => {
    const store = useAuthStore()
    
    await store.login({
      email: 'test@example.com',
      password: 'password123'
    })

    expect(store.isAuthenticated).toBe(true)
    expect(store.user).toBeDefined()
  })
})
```

## 🚀 Build e Deploy

### Scripts de Build
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "test": "vitest",
    "test:ui": "vitest --ui"
  }
}
```

### Configuração de Build
```javascript
// vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  build: {
    outDir: 'dist',
    sourcemap: true
  },
  server: {
    port: 3002,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    }
  }
})
```

### Deploy

**Desenvolvimento:**
```bash
npm run dev
# Acesse http://localhost:3002
```

**Produção:**
```bash
npm run build
# Arquivos gerados em dist/
```

## 📱 Responsividade

### Breakpoints
```css
/* Mobile First */
.container {
  @apply px-4;
}

/* Tablet */
@media (min-width: 768px) {
  .container {
    @apply px-6;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .container {
    @apply px-8;
  }
}
```

### Componentes Responsivos
```vue
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <FinancialCard
      v-for="card in cards"
      :key="card.id"
      :title="card.title"
      :value="card.value"
    />
  </div>
</template>
```

## 🔧 Utilitários

### Formatters
```javascript
// lib/utils/formatters.js
export function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

export function formatDate(date) {
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(date))
}

export function formatPercentage(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'percent',
    minimumFractionDigits: 2
  }).format(value / 100)
}
```

### Composables
```javascript
// composables/useApi.js
import { ref } from 'vue'
import api from '@/lib/api'

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  const request = async (config) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api(config)
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    request
  }
}
```

## 🎯 Performance

### Lazy Loading
```javascript
// Lazy loading de componentes
const Dashboard = () => import('@/views/Dashboard.vue')
const Cashflow = () => import('@/views/Cashflow.vue')
```

### Code Splitting
```javascript
// Divisão de código por rotas
const routes = [
  {
    path: '/dashboard',
    component: () => import('@/views/Dashboard.vue')
  }
]
```

### Caching
```javascript
// Cache de requisições
const cache = new Map()

export async function fetchWithCache(url) {
  if (cache.has(url)) {
    return cache.get(url)
  }
  
  const data = await api.get(url)
  cache.set(url, data)
  return data
}
```

## 🔒 Segurança

### Validação de Inputs
```vue
<template>
  <form @submit.prevent="handleSubmit">
    <Input
      v-model="form.email"
      type="email"
      :rules="emailRules"
      required
    />
  </form>
</template>

<script>
export default {
  data() {
    return {
      emailRules: [
        v => !!v || 'Email é obrigatório',
        v => /.+@.+\..+/.test(v) || 'Email deve ser válido'
      ]
    }
  }
}
</script>
```

### Sanitização
```javascript
// Sanitizar dados do usuário
import DOMPurify from 'dompurify'

export function sanitizeInput(input) {
  return DOMPurify.sanitize(input)
}
```

---

**Documentação do Frontend CKO Framework v1.0**
