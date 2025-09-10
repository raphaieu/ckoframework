<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Forex</h1>
        <p class="text-muted-foreground">
          Controle de operações de câmbio
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <DateRange 
          v-model="selectedPeriod" 
          @change="handlePeriodChange"
          class="w-64"
        />
        <DialogForm
          title="Nova Operação Forex"
          description="Registre uma nova operação de câmbio"
          trigger-text="Nova Operação"
          @submit="handleCreateTrade"
        >
          <template #default="{ form, errors }">
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Par de Moedas</label>
                  <Select v-model="form.pair">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o par" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="pair in forexPairs" 
                        :key="pair" 
                        :value="pair"
                      >
                        {{ pair }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
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
                  <label class="text-sm font-medium">Tamanho do Lote</label>
                  <Input v-model="form.lot_size" type="number" step="0.01" placeholder="0.1" />
                </div>
                <div>
                  <label class="text-sm font-medium">Preço Entrada</label>
                  <Input v-model="form.entry_price" type="number" step="0.00001" placeholder="1.00000" />
                </div>
                <div>
                  <label class="text-sm font-medium">Preço Saída</label>
                  <Input v-model="form.exit_price" type="number" step="0.00001" placeholder="1.00000" />
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Stop Loss</label>
                  <Input v-model="form.sl" type="number" step="0.00001" placeholder="1.00000" />
                </div>
                <div>
                  <label class="text-sm font-medium">Take Profit</label>
                  <Input v-model="form.tp" type="number" step="0.00001" placeholder="1.00000" />
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Swap</label>
                  <Input v-model="form.swap" type="number" step="0.01" placeholder="0.00" />
                </div>
                <div>
                  <label class="text-sm font-medium">Comissão</label>
                  <Input v-model="form.commissions" type="number" step="0.01" placeholder="0.00" />
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
            {{ tradesStore.filteredForexTrades.length }}
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
            :class="tradesStore.pnlForexTotal >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ tradesStore.formatCurrency(tradesStore.pnlForexTotal) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Pips Totais</CardTitle>
          <Target class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div 
            class="text-2xl font-bold"
            :class="totalPips >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ totalPips >= 0 ? '+' : '' }}{{ totalPips.toFixed(1) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Taxa de Acerto</CardTitle>
          <Clock class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ winRate }}%
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Tabela de Operações -->
    <Card>
      <CardHeader>
        <CardTitle>Operações Forex</CardTitle>
        <CardDescription>
          Lista de todas as operações de câmbio
        </CardDescription>
      </CardHeader>
      <CardContent>
        <DataTable
          :data="tradesStore.filteredForexTrades"
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
            {{ item.entry_price.toFixed(5) }}
          </template>
          
          <template #exit_price="{ item }">
            <span v-if="item.exit_price">
              {{ item.exit_price.toFixed(5) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
          </template>
          
          <template #sl="{ item }">
            <span v-if="item.sl">
              {{ item.sl.toFixed(5) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
          </template>
          
          <template #tp="{ item }">
            <span v-if="item.tp">
              {{ item.tp.toFixed(5) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
          </template>
          
          <template #pips="{ item }">
            <span 
              v-if="item.exit_price"
              :class="tradesStore.calculatePips(item) >= 0 ? 'text-green-600' : 'text-red-600'"
              class="font-medium"
            >
              {{ tradesStore.calculatePips(item) >= 0 ? '+' : '' }}{{ tradesStore.calculatePips(item).toFixed(1) }}
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
import DataTable from '@/components/finance/DataTable.vue'
import DateRange from '@/components/finance/DateRange.vue'
import DialogForm from '@/components/finance/DialogForm.vue'
import { useToast } from '@/components/ui/toast/use-toast'

// Stores
const tradesStore = useTradesStore()
const { toast } = useToast()

// Estado local
const selectedPeriod = ref('30d')

// Pares de moedas mais comuns
const forexPairs = [
  'EURUSD', 'GBPUSD', 'USDJPY', 'USDCHF', 'AUDUSD', 'USDCAD', 'NZDUSD',
  'EURJPY', 'GBPJPY', 'AUDJPY', 'CADJPY', 'CHFJPY', 'NZDJPY',
  'EURGBP', 'EURAUD', 'EURCHF', 'EURNZD', 'EURCAD',
  'GBPAUD', 'GBPCHF', 'GBPNZD', 'GBPCAD',
  'AUDCHF', 'AUDNZD', 'AUDCAD', 'CHFNZD', 'CHFCAD', 'NZDCAD'
]

// Computed
const totalPips = computed(() => {
  return tradesStore.filteredForexTrades
    .filter(t => t.exit_price)
    .reduce((sum, trade) => sum + tradesStore.calculatePips(trade), 0)
})

const winRate = computed(() => {
  const trades = tradesStore.filteredForexTrades.filter(t => t.pnl !== undefined)
  if (trades.length === 0) return 0
  
  const wins = trades.filter(t => t.pnl > 0).length
  return Math.round((wins / trades.length) * 100)
})

const filterOptions = computed(() => [
  { value: 'all', label: 'Todos' },
  ...forexPairs.map(pair => ({ value: pair, label: pair }))
])

// Colunas da tabela
const tradeColumns = [
  { key: 'opened_at', label: 'Abertura', sortable: true, format: 'datetime' },
  { key: 'pair', label: 'Par', sortable: true },
  { key: 'side', label: 'Lado', sortable: true },
  { key: 'lot_size', label: 'Lote', sortable: true, class: 'text-center' },
  { key: 'entry_price', label: 'Entrada', sortable: true, class: 'text-right' },
  { key: 'exit_price', label: 'Saída', sortable: true, class: 'text-right' },
  { key: 'sl', label: 'SL', sortable: true, class: 'text-right' },
  { key: 'tp', label: 'TP', sortable: true, class: 'text-right' },
  { key: 'swap', label: 'Swap', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'commissions', label: 'Comissão', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'pips', label: 'Pips', sortable: true, class: 'text-right' },
  { key: 'pnl', label: 'PnL', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'duration', label: 'Duração', sortable: false }
]

// Métodos
const handlePeriodChange = (periodData) => {
  selectedPeriod.value = periodData.period
  loadData()
}

const loadData = async () => {
  await tradesStore.fetchForexTrades(selectedPeriod.value)
}

const handleCreateTrade = async (formData) => {
  try {
    // Definir data padrão se não informada
    if (!formData.opened_at) {
      formData.opened_at = new Date().toISOString()
    }
    
    await tradesStore.createForexTrade(formData)
    
    toast({
      title: 'Sucesso',
      description: 'Operação forex registrada com sucesso!',
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
  console.log('Editar operação forex:', item)
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
