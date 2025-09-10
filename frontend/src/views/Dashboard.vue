<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-muted-foreground">
          Visão geral das suas finanças e investimentos
        </p>
      </div>
      <DateRange 
        v-model="selectedPeriod" 
        @change="handlePeriodChange"
        class="w-64"
      />
    </div>

    <!-- KPIs -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      <KpiCard
        title="Receitas"
        :value="cashflowStore.incomeTotal"
        :change="incomeChange"
        format="currency"
        icon="TrendingUp"
        trend="positive"
        subtitle="Últimos 30 dias"
      />
      <KpiCard
        title="Despesas"
        :value="cashflowStore.expenseTotal"
        :change="expenseChange"
        format="currency"
        icon="TrendingDown"
        trend="negative"
        subtitle="Últimos 30 dias"
      />
      <KpiCard
        title="Saldo"
        :value="cashflowStore.balance"
        :change="balanceChange"
        format="currency"
        icon="Wallet"
        :trend="balanceTrend"
        subtitle="Receitas - Despesas"
      />
      <KpiCard
        title="PnL Day Trade"
        :value="tradesStore.pnlDayTotal"
        :change="dayTradeChange"
        format="currency"
        icon="BarChart3"
        :trend="dayTradeTrend"
        subtitle="Últimos 30 dias"
      />
    </div>

    <!-- Gráficos -->
    <div class="grid gap-6 md:grid-cols-2">
      <!-- Gráfico de Performance por Categoria -->
      <Card>
        <CardHeader>
          <CardTitle>Performance por Categoria</CardTitle>
          <CardDescription>
            Receitas e despesas por categoria no período
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="h-80">
            <AreaStackedChart :data="categoryData" />
          </div>
        </CardContent>
      </Card>

      <!-- Gráfico de Alocação -->
      <Card>
        <CardHeader>
          <CardTitle>Alocação de Investimentos</CardTitle>
          <CardDescription>
            Distribuição entre Swing e Cripto
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="h-80">
            <AllocationDonutChart :data="allocationData" />
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Tabela de Transações Recentes -->
    <Card>
      <CardHeader>
        <CardTitle>Transações Recentes</CardTitle>
        <CardDescription>
          Últimas movimentações financeiras
        </CardDescription>
      </CardHeader>
      <CardContent>
        <DataTable
          :data="recentTransactions"
          :columns="transactionColumns"
          :is-loading="cashflowStore.isLoading"
          :show-filters="false"
          :show-pagination="false"
          :page-size="5"
        >
          <template #type="{ item }">
            <Badge :variant="item.type === 'income' ? 'default' : 'destructive'">
              {{ item.type === 'income' ? 'Receita' : 'Despesa' }}
            </Badge>
          </template>
          <template #category_id="{ item }">
            <CategoryDot 
              :category="item.category_id" 
              :categories="cashflowStore.categories"
            />
          </template>
          <template #amount="{ item }">
            <span 
              :class="item.type === 'income' ? 'text-green-600' : 'text-red-600'"
              class="font-medium"
            >
              {{ cashflowStore.formatCurrency(item.amount) }}
            </span>
          </template>
          <template #occurred_at="{ item }">
            {{ cashflowStore.formatDate(item.occurred_at) }}
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
import { useCashflowStore } from '@/stores/cashflow'
import { useTradesStore } from '@/stores/trades'
import { useHoldingsStore } from '@/stores/holdings'
import KpiCard from '@/components/finance/KpiCard.vue'
import DataTable from '@/components/finance/DataTable.vue'
import DateRange from '@/components/finance/DateRange.vue'
import CategoryDot from '@/components/finance/CategoryDot.vue'
import AreaStackedChart from '@/lib/charts/AreaStacked.vue'
import AllocationDonutChart from '@/lib/charts/AllocationDonut.vue'

// Stores
const cashflowStore = useCashflowStore()
const tradesStore = useTradesStore()
const holdingsStore = useHoldingsStore()

// Estado local
const selectedPeriod = ref('30d')

// Computed
const recentTransactions = computed(() => 
  cashflowStore.recent(5)
)

const categoryData = computed(() => {
  const grouped = cashflowStore.groupByCategory
  return grouped.map(category => ({
    name: category.name,
    income: category.income,
    expense: category.expense,
    color: category.color
  }))
})

const allocationData = computed(() => {
  return [
    {
      name: 'Swing Trading',
      value: holdingsStore.swingEquity,
      color: '#3b82f6'
    },
    {
      name: 'Criptomoedas',
      value: holdingsStore.cryptoEquity,
      color: '#10b981'
    }
  ]
})

// Colunas da tabela
const transactionColumns = [
  { key: 'occurred_at', label: 'Data', sortable: true, format: 'datetime' },
  { key: 'type', label: 'Tipo', sortable: true },
  { key: 'category_id', label: 'Categoria', sortable: true },
  { key: 'source', label: 'Fonte', sortable: true },
  { key: 'amount', label: 'Valor', sortable: true, format: 'currency', class: 'text-right' }
]

// Métodos
const handlePeriodChange = (periodData) => {
  selectedPeriod.value = periodData.period
  loadData()
}

const loadData = async () => {
  await Promise.all([
    cashflowStore.fetch(selectedPeriod.value),
    tradesStore.fetchDayTrades(selectedPeriod.value),
    tradesStore.fetchForexTrades(selectedPeriod.value),
    holdingsStore.fetchSwingPositions(),
    holdingsStore.fetchCryptoHoldings()
  ])
}

// Lifecycle
onMounted(() => {
  loadData()
})

// Placeholder para mudanças percentuais (seria calculado com dados históricos)
const incomeChange = ref(12.5)
const expenseChange = ref(-5.2)
const balanceChange = ref(18.7)
const dayTradeChange = ref(8.3)

const balanceTrend = computed(() => 
  cashflowStore.balance >= 0 ? 'positive' : 'negative'
)

const dayTradeTrend = computed(() => 
  tradesStore.pnlDayTotal >= 0 ? 'positive' : 'negative'
)
</script>
