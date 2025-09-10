<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Criptomoedas</h1>
        <p class="text-muted-foreground">
          Controle de holdings de criptomoedas
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <DialogForm
          title="Novo Holding"
          description="Registre um novo holding de criptomoeda"
          trigger-text="Novo Holding"
          @submit="handleCreateHolding"
        >
          <template #default="{ form, errors }">
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Carteira</label>
                  <Select v-model="form.wallet">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a carteira" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Binance">Binance</SelectItem>
                      <SelectItem value="MetaMask">MetaMask</SelectItem>
                      <SelectItem value="Coinbase">Coinbase</SelectItem>
                      <SelectItem value="Kraken">Kraken</SelectItem>
                      <SelectItem value="Outras">Outras</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <label class="text-sm font-medium">Blockchain</label>
                  <Select v-model="form.chain">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a blockchain" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="BSC">BSC (Binance Smart Chain)</SelectItem>
                      <SelectItem value="ETH">Ethereum</SelectItem>
                      <SelectItem value="BTC">Bitcoin</SelectItem>
                      <SelectItem value="POLYGON">Polygon</SelectItem>
                      <SelectItem value="AVALANCHE">Avalanche</SelectItem>
                      <SelectItem value="SOLANA">Solana</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Ativo</label>
                  <Input v-model="form.asset" placeholder="Ex: BTC, ETH, ADA" />
                </div>
                <div>
                  <label class="text-sm font-medium">Moeda</label>
                  <Select v-model="form.currency">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a moeda" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="BRL">BRL</SelectItem>
                      <SelectItem value="USD">USD</SelectItem>
                      <SelectItem value="EUR">EUR</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div>
                <label class="text-sm font-medium">Preço Atual</label>
                <Input v-model.number="form.current_price" type="number" step="0.01" placeholder="0.00" />
              </div>
            </div>
          </template>
        </DialogForm>
      </div>
    </div>

    <!-- Resumo -->
    <div class="grid gap-4 md:grid-cols-4">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Total de Ativos</CardTitle>
          <BarChart3 class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ holdingsStore.cryptoHoldings.length }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Patrimônio Total</CardTitle>
          <TrendingUp class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ formatCurrency(holdingsStore.cryptoEquity) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">P/L Não Realizado</CardTitle>
          <Target class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div 
            class="text-2xl font-bold"
            :class="totalPnL >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ totalPnL >= 0 ? '+' : '' }}{{ formatCurrency(totalPnL) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">P/L %</CardTitle>
          <Percent class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div 
            class="text-2xl font-bold"
            :class="totalPnLPercent >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ totalPnLPercent >= 0 ? '+' : '' }}{{ totalPnLPercent.toFixed(2) }}%
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Filtros -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <Select v-model="selectedWallet">
          <SelectTrigger class="w-[200px]">
            <SelectValue placeholder="Filtrar por carteira" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todas as carteiras</SelectItem>
            <SelectItem value="Binance">Binance</SelectItem>
            <SelectItem value="MetaMask">MetaMask</SelectItem>
            <SelectItem value="Coinbase">Coinbase</SelectItem>
            <SelectItem value="Kraken">Kraken</SelectItem>
          </SelectContent>
        </Select>
        
        <Input
          v-model="searchQuery"
          placeholder="Buscar por ativo..."
          class="w-[200px]"
        />
      </div>
    </div>

    <!-- Lista de Holdings -->
    <div class="space-y-4">
      <Card v-for="holding in filteredHoldings" :key="holding.id">
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center">
                <span class="text-white font-bold text-sm">{{ holding.asset.charAt(0) }}</span>
              </div>
              <div>
                <CardTitle class="text-lg">{{ holding.asset }}</CardTitle>
                <p class="text-sm text-muted-foreground">{{ holding.wallet }} • {{ holding.chain }}</p>
              </div>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <Badge :variant="holding.unrealized_pnl_percent >= 0 ? 'default' : 'destructive'">
              {{ holding.unrealized_pnl_percent >= 0 ? '+' : '' }}{{ holding?.unrealized_pnl_percent?.toFixed(2) }}%
            </Badge>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                  <span class="sr-only">Abrir menu</span>
                  <MoreHorizontal class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem @click="openLotsModal(holding)">
                  <Eye class="mr-2 h-4 w-4" />
                  Ver Lotes
                </DropdownMenuItem>
                <DropdownMenuItem @click="editHolding(holding)">
                  <Edit class="mr-2 h-4 w-4" />
                  Editar
                </DropdownMenuItem>
                <DropdownMenuItem @click="deleteHolding(holding.id)" class="text-red-600">
                  <Trash2 class="mr-2 h-4 w-4" />
                  Excluir
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </CardHeader>
        
        <CardContent class="space-y-4">
          <!-- Informações do Holding -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <p class="text-sm text-muted-foreground">Quantidade Total</p>
              <p class="text-lg font-semibold">{{ holding?.total_qty?.toFixed(8) }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Preço Médio</p>
              <p class="text-lg font-semibold">{{ formatCurrency(holding?.pm, '', holding?.currency) }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Preço Atual</p>
              <p class="text-lg font-semibold">{{ formatCurrency(holding?.current_price, '', holding?.currency) }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">P/L Não Realizado</p>
              <p 
                class="text-lg font-semibold"
                :class="holding?.unrealized_pnl_value >= 0 ? 'text-green-600' : 'text-red-600'"
              >
                {{ holding?.unrealized_pnl_value >= 0 ? '+' : '' }}{{ formatCurrency(holding?.unrealized_pnl_value) }}
              </p>
            </div>
          </div>
          
          <!-- Tabela de Lotes -->
          <div v-if="holding?.lots && holding?.lots?.length > 0">
            <h4 class="text-sm font-semibold mb-2">Lotes</h4>
            <DataTable
              :data="holding?.lots"
              :columns="lotsColumns"
              :items-per-page="5"
            >
              <template #price="{ item }">
                {{ formatCurrency((item as any)?.price, '', (item as any)?.currency) }}
              </template>
              <template #bought_at="{ item }">
                {{ formatDate((item as any)?.bought_at) }}
              </template>
            </DataTable>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useHoldingsStore } from '@/stores/holdings'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { BarChart3, TrendingUp, Target, Percent, MoreHorizontal, Eye, Edit, Trash2 } from 'lucide-vue-next'
import DataTable from '@/components/finance/DataTable.vue'
import DialogForm from '@/components/finance/DialogForm.vue'
import { formatCurrency, formatDate } from '@/lib/utils/formatters.js'

const holdingsStore = useHoldingsStore()

const selectedWallet = ref('all')
const searchQuery = ref('')

const filteredHoldings = computed(() => {
  let filtered = holdingsStore.cryptoHoldings
  
  if (selectedWallet.value !== 'all') {
    filtered = filtered.filter(holding => holding?.wallet === selectedWallet.value)
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(holding => 
      holding?.asset?.toLowerCase().includes(query) ||
      holding?.wallet?.toLowerCase().includes(query) ||
      holding?.chain?.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

const totalPnL = computed(() => {
  return filteredHoldings.value.reduce((sum, holding) => {
    return sum + holding?.unrealized_pnl_value
  }, 0)
})

const totalPnLPercent = computed(() => {
  const totalCost = filteredHoldings.value.reduce((sum, holding) => {
    return sum + (holding?.total_qty * holding?.pm)
  }, 0)
  
  if (totalCost === 0) return 0
  
  return (totalPnL.value / totalCost) * 100
})

const handleCreateHolding = async (data: any) => {
  console.log('Criar holding:', data)
  // Implementar criação de holding
}

const openLotsModal = (holding: any) => {
  console.log('Abrir modal de lotes:', holding)
}

const editHolding = (holding: any) => {
  console.log('Editar holding:', holding)
}

const deleteHolding = async (id: string) => {
  if (confirm('Tem certeza que deseja excluir este holding?')) {
    console.log('Excluir holding:', id)
    await holdingsStore.fetchCryptoHoldings()
  }
}

const lotsColumns = [
  { key: 'qty', label: 'Quantidade', sortable: true, class: 'text-center' },
  { key: 'price', label: 'Preço', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'currency', label: 'Moeda', sortable: true },
  { key: 'bought_at', label: 'Data Compra', sortable: true, format: 'date' }
]

onMounted(() => {
  holdingsStore.fetchCryptoHoldings()
})
</script>