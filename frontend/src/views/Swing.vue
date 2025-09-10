<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Swing Trading</h1>
        <p class="text-muted-foreground">
          Controle de posições de longo prazo
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <DialogForm
          title="Nova Posição"
          description="Registre uma nova posição de swing trading"
          trigger-text="Nova Posição"
          @submit="handleCreatePosition"
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
                  <label class="text-sm font-medium">Símbolo</label>
                  <Input v-model="form.symbol" placeholder="Ex: VALE3, PETR4" />
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Quantidade</label>
                  <Input v-model="form.qty" type="number" placeholder="100" />
                </div>
                <div>
                  <label class="text-sm font-medium">Preço Atual</label>
                  <Input v-model="form.current_price" type="number" step="0.01" placeholder="0,00" />
                </div>
              </div>
              
              <div>
                <label class="text-sm font-medium">Lotes</label>
                <div class="space-y-2">
                  <div 
                    v-for="(lot, index) in form.lots" 
                    :key="index"
                    class="flex items-center space-x-2 p-2 border rounded"
                  >
                    <Input v-model="lot.qty" type="number" placeholder="Qtd" class="w-20" />
                    <Input v-model="lot.price" type="number" step="0.01" placeholder="Preço" class="w-24" />
                    <Input v-model="lot.bought_at" type="date" class="w-32" />
                    <Button 
                      @click="removeLot(index)" 
                      variant="outline" 
                      size="sm"
                      type="button"
                    >
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                  <Button @click="addLot" variant="outline" size="sm" type="button">
                    <Plus class="h-4 w-4 mr-2" />
                    Adicionar Lote
                  </Button>
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
          <CardTitle class="text-sm font-medium">Total de Posições</CardTitle>
          <BarChart3 class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ holdingsStore.swingPositions.length }}
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
            {{ holdingsStore.formatCurrency(holdingsStore.swingEquity) }}
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
            :class="unrealizedPnL >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ unrealizedPnL >= 0 ? '+' : '' }}{{ holdingsStore.formatCurrency(unrealizedPnL) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Dividendos (30d)</CardTitle>
          <Clock class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-green-600">
            {{ holdingsStore.formatCurrency(totalDividends) }}
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Cards de Posições -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Card 
        v-for="position in holdingsStore.swingPositionsWithPnL" 
        :key="position.id"
        class="hover:shadow-lg transition-shadow"
      >
        <CardHeader>
          <div class="flex items-center justify-between">
            <div>
              <CardTitle class="text-lg">{{ position.symbol }}</CardTitle>
              <CardDescription>{{ position.broker }}</CardDescription>
            </div>
            <Badge :variant="position.pnl >= 0 ? 'default' : 'destructive'">
              {{ position.pnlPercent >= 0 ? '+' : '' }}{{ holdingsStore.formatPercent(position.pnlPercent) }}
            </Badge>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-muted-foreground">Quantidade:</span>
              <div class="font-medium">{{ position.qty }}</div>
            </div>
            <div>
              <span class="text-muted-foreground">Preço Atual:</span>
              <div class="font-medium">{{ holdingsStore.formatCurrency(position.current_price) }}</div>
            </div>
            <div>
              <span class="text-muted-foreground">Valor Atual:</span>
              <div class="font-medium">{{ holdingsStore.formatCurrency(position.currentValue) }}</div>
            </div>
            <div>
              <span class="text-muted-foreground">Custo:</span>
              <div class="font-medium">{{ holdingsStore.formatCurrency(position.costBasis) }}</div>
            </div>
          </div>
          
          <div class="pt-2 border-t">
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">P/L Não Realizado:</span>
              <span 
                class="font-medium"
                :class="position.pnl >= 0 ? 'text-green-600' : 'text-red-600'"
              >
                {{ position.pnl >= 0 ? '+' : '' }}{{ holdingsStore.formatCurrency(position.pnl) }}
              </span>
            </div>
          </div>
          
          <div class="flex space-x-2">
            <Button 
              @click="openLotsModal(position)" 
              variant="outline" 
              size="sm" 
              class="flex-1"
            >
              <Eye class="h-4 w-4 mr-2" />
              Lotes
            </Button>
            <Button 
              @click="openDividendModal(position)" 
              variant="outline" 
              size="sm" 
              class="flex-1"
            >
              <Plus class="h-4 w-4 mr-2" />
              Dividendo
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Tabela de Lotes -->
    <Card>
      <CardHeader>
        <CardTitle>Lotes por Posição</CardTitle>
        <CardDescription>
          Detalhamento dos lotes de cada posição
        </CardDescription>
      </CardHeader>
      <CardContent>
        <DataTable
          :data="allLots"
          :columns="lotColumns"
          :is-loading="holdingsStore.isLoading"
          :show-filters="false"
          :show-actions="false"
        >
          <template #price="{ item }">
            {{ holdingsStore.formatCurrency(item.price) }}
          </template>
          <template #bought_at="{ item }">
            {{ holdingsStore.formatDate(item.bought_at) }}
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
import { Button } from '@/components/ui/button'
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
  Clock,
  Plus,
  Trash2,
  Eye
} from 'lucide-vue-next'
import { useHoldingsStore } from '@/stores/holdings'
import { config } from '@/config/app'
import DataTable from '@/components/finance/DataTable.vue'
import DialogForm from '@/components/finance/DialogForm.vue'
import { useToast } from '@/components/ui/toast/use-toast'

// Stores
const holdingsStore = useHoldingsStore()
const { toast } = useToast()

// Configurações
const brokers = config.finance.brokers

// Computed
const unrealizedPnL = computed(() => {
  return holdingsStore.swingPositionsWithPnL.reduce((sum, pos) => sum + pos.pnl, 0)
})

const totalDividends = computed(() => {
  return holdingsStore.swingPositions.reduce((sum, pos) => {
    return sum + pos.dividends.reduce((divSum, div) => divSum + div.amount, 0)
  }, 0)
})

const allLots = computed(() => {
  const lots = []
  holdingsStore.swingPositions.forEach(position => {
    position.lots.forEach(lot => {
      lots.push({
        ...lot,
        symbol: position.symbol,
        broker: position.broker
      })
    })
  })
  return lots
})

// Colunas da tabela
const lotColumns = [
  { key: 'symbol', label: 'Símbolo', sortable: true },
  { key: 'broker', label: 'Corretora', sortable: true },
  { key: 'qty', label: 'Quantidade', sortable: true, class: 'text-center' },
  { key: 'price', label: 'Preço', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'bought_at', label: 'Data Compra', sortable: true, format: 'date' }
]

// Métodos
const loadData = async () => {
  await holdingsStore.fetchSwingPositions()
}

const handleCreatePosition = async (formData) => {
  try {
    // Calcular quantidade total dos lotes
    formData.qty = formData.lots.reduce((sum, lot) => sum + (lot.qty || 0), 0)
    
    await holdingsStore.createSwingPosition(formData)
    
    toast({
      title: 'Sucesso',
      description: 'Posição criada com sucesso!',
    })
  } catch (error) {
    toast({
      title: 'Erro',
      description: 'Erro ao criar posição. Tente novamente.',
      variant: 'destructive'
    })
  }
}

const addLot = () => {
  // Implementar adição de lote no formulário
  console.log('Adicionar lote')
}

const removeLot = (index) => {
  // Implementar remoção de lote no formulário
  console.log('Remover lote', index)
}

const openLotsModal = (position) => {
  console.log('Abrir modal de lotes:', position)
  toast({
    title: 'Em desenvolvimento',
    description: 'Modal de lotes será implementado em breve.',
  })
}

const openDividendModal = (position) => {
  console.log('Abrir modal de dividendo:', position)
  toast({
    title: 'Em desenvolvimento',
    description: 'Modal de dividendos será implementado em breve.',
  })
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>
