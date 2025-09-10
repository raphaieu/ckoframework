<template>
  <aside class="w-64 bg-card border-r min-h-screen">
    <div class="p-4">
      <!-- Navigation -->
      <nav class="space-y-2">
        <!-- Dashboard -->
        <router-link 
          to="/" 
          class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="isActive('/') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
        >
          <BarChart3 class="h-5 w-5" />
          <span>Dashboard</span>
        </router-link>

        <!-- Cashflow -->
        <router-link 
          to="/cashflow" 
          class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="isActive('/cashflow') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
        >
          <Wallet class="h-5 w-5" />
          <span>Cashflow</span>
        </router-link>

        <!-- Trading Section -->
        <div class="pt-4">
          <h3 class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
            Trading
          </h3>
          <div class="mt-2 space-y-1">
            <router-link 
              to="/trades/day" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/trades/day') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <TrendingUp class="h-5 w-5" />
              <span>Day Trade</span>
            </router-link>
            
            <router-link 
              to="/trades/forex" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/trades/forex') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <Globe class="h-5 w-5" />
              <span>Forex</span>
            </router-link>
          </div>
        </div>

        <!-- Investments Section -->
        <div class="pt-4">
          <h3 class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
            Investimentos
          </h3>
          <div class="mt-2 space-y-1">
            <router-link 
              to="/investments/swing" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/investments/swing') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <TrendingUp class="h-5 w-5" />
              <span>Swing Trading</span>
            </router-link>
            
            <router-link 
              to="/crypto" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/crypto') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <Bitcoin class="h-5 w-5" />
              <span>Criptomoedas</span>
            </router-link>
          </div>
        </div>

        <!-- Tools Section -->
        <div class="pt-4">
          <h3 class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
            Ferramentas
          </h3>
          <div class="mt-2 space-y-1">
            <router-link 
              to="/intake" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/intake') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <Upload class="h-5 w-5" />
              <span>Intake</span>
              <Badge v-if="pendingIntakeItems > 0" variant="destructive" class="ml-auto text-xs">
                {{ pendingIntakeItems }}
              </Badge>
            </router-link>
            
            <router-link 
              to="/settings" 
              class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="isActive('/settings') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            >
              <Settings class="h-5 w-5" />
              <span>Configurações</span>
            </router-link>
          </div>
        </div>
      </nav>

      <!-- Quick Stats -->
      <div class="mt-8 p-4 bg-muted rounded-lg">
        <h4 class="text-sm font-medium mb-3">Resumo Rápido</h4>
        <div class="space-y-2 text-xs">
          <div class="flex justify-between">
            <span class="text-muted-foreground">Saldo:</span>
            <span class="font-medium" :class="quickBalance >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(quickBalance) }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">PnL Hoje:</span>
            <span class="font-medium" :class="todayPnL >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(todayPnL) }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">Patrimônio:</span>
            <span class="font-medium">
              {{ formatCurrency(totalEquity) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { Badge } from '@/components/ui/badge'
import { 
  BarChart3, 
  Wallet, 
  TrendingUp, 
  Globe, 
  Bitcoin, 
  Upload, 
  Settings 
} from 'lucide-vue-next'
import { useCashflowStore } from '@/stores/cashflow'
import { useTradesStore } from '@/stores/trades'
import { useHoldingsStore } from '@/stores/holdings'

const route = useRoute()
const cashflowStore = useCashflowStore()
const tradesStore = useTradesStore()
const holdingsStore = useHoldingsStore()

// Computed
const isActive = (path) => {
  if (path === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(path)
}

const pendingIntakeItems = computed(() => {
  // Simular itens pendentes no intake
  return 2
})

const quickBalance = computed(() => {
  return cashflowStore.balance
})

const todayPnL = computed(() => {
  // Simular PnL do dia
  return 150.50
})

const totalEquity = computed(() => {
  return holdingsStore.swingEquity + holdingsStore.cryptoEquity
})

// Métodos
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(amount)
}
</script>
