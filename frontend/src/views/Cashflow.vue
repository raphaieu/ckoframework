<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Cashflow</h1>
        <p class="text-muted-foreground">
          Controle de receitas e despesas
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <DateRange 
          v-model="selectedPeriod" 
          @change="handlePeriodChange"
          class="w-64"
        />
        <DialogForm
          title="Nova Transação"
          description="Adicione uma nova receita ou despesa"
          trigger-text="Lançar"
          @submit="handleCreateTransaction"
        >
          <template #default="{ form, errors }">
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium">Tipo</label>
                  <Select v-model="form.type">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="income">Receita</SelectItem>
                      <SelectItem value="expense">Despesa</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <label class="text-sm font-medium">Categoria</label>
                  <Select v-model="form.category_id">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a categoria" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem 
                        v-for="category in cashflowStore.categories" 
                        :key="category.id" 
                        :value="category.id"
                      >
                        <div class="flex items-center space-x-2">
                          <div 
                            class="w-3 h-3 rounded-full" 
                            :style="{ backgroundColor: category.color }"
                          />
                          <span>{{ category.name }}</span>
                        </div>
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div>
                <label class="text-sm font-medium">Fonte</label>
                <Input v-model="form.source" placeholder="Ex: Salário, Supermercado..." />
              </div>
              
              <div>
                <label class="text-sm font-medium">Valor (R$)</label>
                <Input 
                  v-model="form.amount" 
                  type="number" 
                  step="0.01" 
                  placeholder="0,00"
                />
              </div>
              
              <div>
                <label class="text-sm font-medium">Data e Hora</label>
                <Input 
                  v-model="form.occurred_at" 
                  type="datetime-local"
                />
              </div>
              
              <div>
                <label class="text-sm font-medium">Observações</label>
                <Textarea 
                  v-model="form.notes" 
                  placeholder="Observações adicionais..."
                  rows="3"
                />
              </div>
            </div>
          </template>
        </DialogForm>
      </div>
    </div>

    <!-- Resumo -->
    <div class="grid gap-4 md:grid-cols-3">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Receitas</CardTitle>
          <TrendingUp class="h-4 w-4 text-green-600" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-green-600">
            {{ cashflowStore.formatCurrency(cashflowStore.incomeTotal) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Despesas</CardTitle>
          <TrendingDown class="h-4 w-4 text-red-600" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-red-600">
            {{ cashflowStore.formatCurrency(cashflowStore.expenseTotal) }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Saldo</CardTitle>
          <Wallet class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div 
            class="text-2xl font-bold"
            :class="cashflowStore.balance >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ cashflowStore.formatCurrency(cashflowStore.balance) }}
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Tabela de Transações -->
    <Card>
      <CardHeader>
        <CardTitle>Transações</CardTitle>
        <CardDescription>
          Lista de todas as receitas e despesas
        </CardDescription>
      </CardHeader>
      <CardContent>
        <DataTable
          :data="cashflowStore.filteredItems"
          :columns="transactionColumns"
          :is-loading="cashflowStore.isLoading"
          :filter-options="filterOptions"
          @edit="handleEditTransaction"
          @delete="handleDeleteTransaction"
          @refresh="loadData"
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
              {{ item.type === 'income' ? '+' : '-' }}{{ cashflowStore.formatCurrency(item.amount) }}
            </span>
          </template>
          
          <template #occurred_at="{ item }">
            {{ cashflowStore.formatDate(item.occurred_at) }}
          </template>
          
          <template #attachments="{ item }">
            <div v-if="item.attachments && item.attachments.length > 0" class="flex items-center space-x-1">
              <Paperclip class="h-4 w-4 text-muted-foreground" />
              <span class="text-sm text-muted-foreground">
                {{ item.attachments.length }}
              </span>
            </div>
            <span v-else class="text-muted-foreground">-</span>
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
import { Textarea } from '@/components/ui/textarea'
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from '@/components/ui/select'
import { Button } from '@/components/ui/button'
import { 
  TrendingUp, 
  TrendingDown, 
  Wallet, 
  Paperclip 
} from 'lucide-vue-next'
import { useCashflowStore } from '@/stores/cashflow'
import DataTable from '@/components/finance/DataTable.vue'
import DateRange from '@/components/finance/DateRange.vue'
import CategoryDot from '@/components/finance/CategoryDot.vue'
import DialogForm from '@/components/finance/DialogForm.vue'
import { useToast } from '@/components/ui/toast/use-toast'

// Stores
const cashflowStore = useCashflowStore()
const { toast } = useToast()

// Estado local
const selectedPeriod = ref('30d')

// Computed
const filterOptions = computed(() => [
  { value: 'all', label: 'Todos' },
  { value: 'income', label: 'Receitas' },
  { value: 'expense', label: 'Despesas' },
  ...cashflowStore.categories.map(cat => ({
    value: cat.id,
    label: cat.name
  }))
])

// Colunas da tabela
const transactionColumns = [
  { key: 'occurred_at', label: 'Data/Hora', sortable: true, format: 'datetime' },
  { key: 'type', label: 'Tipo', sortable: true },
  { key: 'category_id', label: 'Categoria', sortable: true },
  { key: 'source', label: 'Fonte', sortable: true },
  { key: 'amount', label: 'Valor', sortable: true, format: 'currency', class: 'text-right' },
  { key: 'attachments', label: 'Anexos', sortable: false, class: 'text-center' }
]

// Métodos
const handlePeriodChange = (periodData) => {
  selectedPeriod.value = periodData.period
  loadData()
}

const loadData = async () => {
  await cashflowStore.fetch(selectedPeriod.value)
}

const handleCreateTransaction = async (formData) => {
  try {
    // Definir data padrão se não informada
    if (!formData.occurred_at) {
      formData.occurred_at = new Date().toISOString()
    }
    
    await cashflowStore.create(formData)
    
    toast({
      title: 'Sucesso',
      description: 'Transação criada com sucesso!',
    })
  } catch (error) {
    toast({
      title: 'Erro',
      description: 'Erro ao criar transação. Tente novamente.',
      variant: 'destructive'
    })
  }
}

const handleEditTransaction = (item) => {
  // Implementar edição
  console.log('Editar transação:', item)
  toast({
    title: 'Em desenvolvimento',
    description: 'Funcionalidade de edição será implementada em breve.',
  })
}

const handleDeleteTransaction = async (item) => {
  if (confirm('Tem certeza que deseja excluir esta transação?')) {
    try {
      await cashflowStore.remove(item.id)
      
      toast({
        title: 'Sucesso',
        description: 'Transação excluída com sucesso!',
      })
    } catch (error) {
      toast({
        title: 'Erro',
        description: 'Erro ao excluir transação. Tente novamente.',
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
