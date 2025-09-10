<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Day Trade</h1>
        <p class="text-muted-foreground">
          Controle de operações de day trade
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <DateRange 
          v-model="selectedPeriod" 
          @change="handlePeriodChange"
          class="w-64"
        />
        <DialogForm
          title="Nova Operação"
          description="Registre uma nova operação de day trade"
          trigger-text="Nova Operação"
          @submit="handleCreateTrade"
        >
          <template #default="{ form, errors }">
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Corretora</label>
                  <Select v-model="form.broker">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a corretora" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="broker in brokers" 
                        :key="broker" 
                        :value="broker"
                      >
                        {{ broker }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <label class="text-sm font-medium">Mercado</label>
                  <Select v-model="form.market">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o mercado" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="market in markets" 
                        :key="market" 
                        :value="market"
                      >
                        {{ market }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Símbolo</label>
                  <Input v-model="form.symbol" placeholder="Ex: WINV25, WDOF25" />
                </div>
                <div>
                  <label class="text-sm font-medium">Lado</label>
                  <Select v-model="form.side">
                    <SelectTrigger>
                      <SelectValue placeholder="Buy/Sell" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="buy">Buy (Compra)</SelectItem>
                      <SelectItem value="sell">Sell (Venda)</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="text-sm font-medium">Quantidade</label>
                  <Input v-model="form.qty" type="number" placeholder="1" />
                </div>
                <div>
                  <label class="text-sm font-medium">Preço Entrada</label>
                  <Input v-model="form.entry_price" type="number" step="0.01" placeholder="0,00" />
                </div>
                <div>
                  <label class="text-sm font-medium">Preço Saída</label>
                  <Input v-model="form.exit_price" type="number" step="0.01" placeholder="0,00" />
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Taxas</label>
                  <Input v-model="form.fees" type="number" step="0.01" placeholder="0,00" />
                </div>
                <div>
                  <label class="text-sm font-medium">Estratégia</label>
                  <Select v-model="form.strategy">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a estratégia" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="strategy in strategies" 
                        :key="strategy" 
                        :value="strategy"
                      >
                        {{ strategy }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Data/Hora Abertura</label>
                  <Input v-model="form.opened_at" type="datetime-local" />
                </div>
                <div>
                  <label class="text-sm font-medium">Data/Hora Fechamento</label>
                  <Input v-model="form.closed_at" type="datetime-local" />
                </div>
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
          <CardTitle class="text-sm font-medium">Total de Operações</CardTitle>
          <BarChart3 class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ tradesStore.filteredDayTrades.length }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">PnL Total</CardTitle>
          <TrendingUp class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div 
            class="text-2xl font-bold"
            :class="tradesStore.pnlDayTotal >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ tradesStore.formatCurrency(tradesStore.pnlDayTotal) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Taxa de Acerto</CardTitle>
          <Target class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ winRate }}%
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Operações Abertas</CardTitle>
          <Clock class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ openTrades }}
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Tabela de Operações -->
    <Card>
      <CardHeader>
        <CardTitle>Operações</CardTitle>
        <CardDescription>
          Lista de todas as operações de day trade
        </CardDescription>
      </CardHeader>
      <CardContent>
        <DataTable
          :data="tradesStore.filteredDayTrades"
          :columns="tradeColumns"
          :is-loading="tradesStore.isLoading"
          :filter-options="filterOptions"
          @edit="handleEditTrade"
          @delete="handleDeleteTrade"
          @refresh="loadData"
        >
          <template #opened_at="{ item }">
            {{ tradesStore.formatDate(item.opened_at) }}
          </template>
          
          <template #side="{ item }">
            <Badge :variant="item.side === 'buy' ? 'default' : 'secondary'">
              {{ item.side === 'buy' ? 'Buy' : 'Sell' }}
            </Badge>
          </template>
          
          <template #entry_price="{ item }">
            {{ tradesStore.formatCurrency(item.entry_price) }}
          </template>
          
          <template #exit_price="{ item }">
            <span v-if="item.exit_price">
              {{ tradesStore.formatCurrency(item.exit_price) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
          </template>
          
          <template #pnl="{ item }">
            <span 
              v-if="item.pnl !== undefined"
              :class="item.pnl >= 0 ? 'text-green-600' : 'text-red-600'"
              class="font-medium"
            >
              {{ item.pnl >= 0 ? '+' : '' }}{{ tradesStore.formatCurrency(item.pnl) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
          </template>
          
          <template #duration="{ item }">
            {{ tradesStore.getDuration(item) }}
          </template>
        </DataTable>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from '@/components/ui/select'
import { 
  BarChart3, 
  TrendingUp, 
  Target, 
  Clock 
} from 'lucide-vue-next'
import { useTradesStore } from '@/stores/trades'
import { config } from '@/config/app'
import DataTable from '@/components/finance/DataTable.vue'
import DateRange from '@/components/finance/DateRange.vue'
import DialogForm from '@/components/finance/DialogForm.vue'
import { useToast } from '@/components/ui/toast/use-toast'

// Stores
const tradesStore = useTradesStore()
const { toast } = useToast()

// Estado local
const selectedPeriod = ref('30d')

// Configurações
const brokers = config.finance.brokers
const markets = config.finance.markets
const strategies = config.finance.tradingStrategies

// Computed
const winRate = computed(() => {
  const trades = tradesStore.filteredDayTrades.filter(t => t.pnl !== undefined)
  if (trades.length === 0) return 0
  
  const wins = trades.filter(t => t.pnl > 0).length
  return Math.round((wins / trades.length) * 100)
})

const openTrades = computed(() => 
  tradesStore.filteredDayTrades.filter(t => !t.closed_at).length
)

const filterOptions = computed(() => [
  { value: 'all', label: 'Todos' },
  { value: 'open', label: 'Abertas' },
  { value: 'closed', label: 'Fechadas' },
  ...brokers.map(broker => ({ value: broker, label: broker })),
  ...strategies.map(strategy => ({ value: strategy, label: strategy }))
])

// Colunas da tabela
const tradeColumns = [
  { key: 'opened_at', label: 'Abertura', sortable: true, format: 'datetime' },
  { key: 'symbol', label: 'Símbolo', sortable: true },
  { key: 'side', label: 'Lado', sortable: true },
  { key: 'qty', label: 'Qtd', sortable: true, class: 'text-center' },
  { key: 'entry_price', label: 'Preço Entrada', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'exit_price', label: 'Preço Saída', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'fees', label: 'Taxas', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'pnl', label: 'PnL', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'strategy', label: 'Estratégia', sortable: true },
  { key: 'duration', label: 'Duração', sortable: false }
]

// Métodos
const handlePeriodChange = (periodData) => {
  selectedPeriod.value = periodData.period
  loadData()
}

const loadData = async () => {
  await tradesStore.fetchDayTrades(selectedPeriod.value)
}

const handleCreateTrade = async (formData) => {
  try {
    // Definir data padrão se não informada
    if (!formData.opened_at) {
      formData.opened_at = new Date().toISOString()
    }
    
    await tradesStore.createDayTrade(formData)
    
    toast({
      title: 'Sucesso',
      description: 'Operação registrada com sucesso!',
    })
  } catch (error) {
    toast({
      title: 'Erro',
      description: 'Erro ao registrar operação. Tente novamente.',
      variant: 'destructive'
    })
  }
}

const handleEditTrade = (item) => {
  console.log('Editar operação:', item)
  toast({
    title: 'Em desenvolvimento',
    description: 'Funcionalidade de edição será implementada em breve.',
  })
}

const handleDeleteTrade = async (item) => {
  if (confirm('Tem certeza que deseja excluir esta operação?')) {
    try {
      // Implementar exclusão quando a API estiver pronta
      toast({
        title: 'Em desenvolvimento',
        description: 'Funcionalidade de exclusão será implementada em breve.',
      })
    } catch (error) {
      toast({
        title: 'Erro',
        description: 'Erro ao excluir operação. Tente novamente.',
        variant: 'destructive'
      })
    }
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>
