<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Configurações</h1>
        <p class="text-muted-foreground">
          Gerencie as configurações da aplicação
        </p>
      </div>
    </div>

    <!-- Configurações Gerais -->
    <Card>
      <CardHeader>
        <CardTitle>Configurações Gerais</CardTitle>
        <CardDescription>
          Configurações básicas da aplicação
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="text-sm font-medium">Nome da Aplicação</label>
            <Input v-model="settings.appName" placeholder="Finance Command Center" />
          </div>
          <div>
            <label class="text-sm font-medium">Moeda Padrão</label>
            <Select v-model="settings.defaultCurrency">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="BRL">Real Brasileiro (BRL)</SelectItem>
                <SelectItem value="USD">Dólar Americano (USD)</SelectItem>
                <SelectItem value="EUR">Euro (EUR)</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="text-sm font-medium">Fuso Horário</label>
            <Select v-model="settings.timezone">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="America/Bahia">America/Bahia</SelectItem>
                <SelectItem value="America/Sao_Paulo">America/Sao_Paulo</SelectItem>
                <SelectItem value="UTC">UTC</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div>
            <label class="text-sm font-medium">Tema</label>
            <Select v-model="settings.theme">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="light">Claro</SelectItem>
                <SelectItem value="dark">Escuro</SelectItem>
                <SelectItem value="system">Sistema</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Configurações de Trading -->
    <Card>
      <CardHeader>
        <CardTitle>Configurações de Trading</CardTitle>
        <CardDescription>
          Configurações específicas para operações
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-6">
        <div>
          <h4 class="text-sm font-medium mb-4">Fatores de Contrato</h4>
          <div class="space-y-3">
            <div 
              v-for="(factor, symbol) in settings.contractFactors" 
              :key="symbol"
              class="flex items-center space-x-4"
            >
              <div class="w-20 text-sm font-medium">{{ symbol }}</div>
              <Input 
                v-model="settings.contractFactors[symbol]" 
                type="number" 
                step="0.1"
                class="w-32"
              />
              <Button 
                @click="removeContractFactor(symbol)" 
                variant="outline" 
                size="sm"
              >
                <Trash2 class="h-4 w-4" />
              </Button>
            </div>
            <div class="flex items-center space-x-4">
              <Input 
                v-model="newContractSymbol" 
                placeholder="Símbolo" 
                class="w-20"
              />
              <Input 
                v-model="newContractFactor" 
                type="number" 
                step="0.1"
                placeholder="Fator"
                class="w-32"
              />
              <Button @click="addContractFactor" variant="outline" size="sm">
                <Plus class="h-4 w-4 mr-2" />
                Adicionar
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Configurações de Categorias -->
    <Card>
      <CardHeader>
        <CardTitle>Categorias</CardTitle>
        <CardDescription>
          Gerencie as categorias de receitas e despesas
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-6">
        <div class="space-y-3">
          <div 
            v-for="category in settings.categories" 
            :key="category.id"
            class="flex items-center space-x-4 p-3 border rounded"
          >
            <div 
              class="w-4 h-4 rounded-full" 
              :style="{ backgroundColor: category.color }"
            />
            <Input v-model="category.name" class="flex-1" />
            <Input 
              v-model="category.color" 
              type="color" 
              class="w-16 h-10"
            />
            <Button 
              @click="removeCategory(category.id)" 
              variant="outline" 
              size="sm"
            >
              <Trash2 class="h-4 w-4" />
            </Button>
          </div>
          <div class="flex items-center space-x-4 p-3 border rounded border-dashed">
            <div class="w-4 h-4 rounded-full bg-gray-300" />
            <Input v-model="newCategoryName" placeholder="Nome da categoria" class="flex-1" />
            <Input 
              v-model="newCategoryColor" 
              type="color" 
              class="w-16 h-10"
            />
            <Button @click="addCategory" variant="outline" size="sm">
              <Plus class="h-4 w-4 mr-2" />
              Adicionar
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Configurações de Backup -->
    <Card>
      <CardHeader>
        <CardTitle>Backup e Exportação</CardTitle>
        <CardDescription>
          Gerencie backups e exportações de dados
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center justify-between p-4 border rounded">
          <div>
            <h4 class="font-medium">Exportar Dados</h4>
            <p class="text-sm text-muted-foreground">
              Exporte todos os seus dados em formato JSON
            </p>
          </div>
          <Button @click="exportData" variant="outline">
            <Download class="h-4 w-4 mr-2" />
            Exportar
          </Button>
        </div>
        
        <div class="flex items-center justify-between p-4 border rounded">
          <div>
            <h4 class="font-medium">Importar Dados</h4>
            <p class="text-sm text-muted-foreground">
              Importe dados de um arquivo JSON
            </p>
          </div>
          <Button @click="importData" variant="outline">
            <Upload class="h-4 w-4 mr-2" />
            Importar
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Ações -->
    <div class="flex justify-end space-x-2">
      <Button @click="resetSettings" variant="outline">
        Resetar
      </Button>
      <Button @click="saveSettings">
        Salvar Configurações
      </Button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
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
  Plus, 
  Trash2, 
  Download, 
  Upload 
} from 'lucide-vue-next'
import { useToast } from '@/components/ui/toast/use-toast'
import { config } from '@/config/app'

const { toast } = useToast()

// Estado local
const settings = ref({
  appName: 'Finance Command Center',
  defaultCurrency: 'BRL',
  timezone: 'America/Bahia',
  theme: 'dark',
  contractFactors: { ...config.finance.contractFactors },
  categories: [...config.finance.defaultCategories]
})

const newContractSymbol = ref('')
const newContractFactor = ref(0)
const newCategoryName = ref('')
const newCategoryColor = ref('#6b7280')

// Métodos
const loadSettings = () => {
  // Carregar configurações do localStorage
  const saved = localStorage.getItem('finance-settings')
  if (saved) {
    settings.value = { ...settings.value, ...JSON.parse(saved) }
  }
}

const saveSettings = () => {
  try {
    localStorage.setItem('finance-settings', JSON.stringify(settings.value))
    toast({
      title: 'Sucesso',
      description: 'Configurações salvas com sucesso!',
    })
  } catch (error) {
    toast({
      title: 'Erro',
      description: 'Erro ao salvar configurações.',
      variant: 'destructive'
    })
  }
}

const resetSettings = () => {
  if (confirm('Tem certeza que deseja resetar todas as configurações?')) {
    settings.value = {
      appName: 'Finance Command Center',
      defaultCurrency: 'BRL',
      timezone: 'America/Bahia',
      theme: 'dark',
      contractFactors: { ...config.finance.contractFactors },
      categories: [...config.finance.defaultCategories]
    }
    localStorage.removeItem('finance-settings')
    toast({
      title: 'Sucesso',
      description: 'Configurações resetadas!',
    })
  }
}

const addContractFactor = () => {
  if (newContractSymbol.value && newContractFactor.value > 0) {
    settings.value.contractFactors[newContractSymbol.value] = newContractFactor.value
    newContractSymbol.value = ''
    newContractFactor.value = 0
  }
}

const removeContractFactor = (symbol) => {
  delete settings.value.contractFactors[symbol]
}

const addCategory = () => {
  if (newCategoryName.value) {
    const newCategory = {
      id: crypto.randomUUID(),
      name: newCategoryName.value,
      color: newCategoryColor.value
    }
    settings.value.categories.push(newCategory)
    newCategoryName.value = ''
    newCategoryColor.value = '#6b7280'
  }
}

const removeCategory = (id) => {
  const index = settings.value.categories.findIndex(c => c.id === id)
  if (index !== -1) {
    settings.value.categories.splice(index, 1)
  }
}

const exportData = () => {
  try {
    const data = {
      settings: settings.value,
      timestamp: new Date().toISOString()
    }
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `finance-backup-${new Date().toISOString().split('T')[0]}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
    
    toast({
      title: 'Sucesso',
      description: 'Dados exportados com sucesso!',
    })
  } catch (error) {
    toast({
      title: 'Erro',
      description: 'Erro ao exportar dados.',
      variant: 'destructive'
    })
  }
}

const importData = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = '.json'
  input.onchange = (e) => {
    const file = e.target.files[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (e) => {
        try {
          const data = JSON.parse(e.target.result)
          if (data.settings) {
            settings.value = { ...settings.value, ...data.settings }
            toast({
              title: 'Sucesso',
              description: 'Dados importados com sucesso!',
            })
          }
        } catch (error) {
          toast({
            title: 'Erro',
            description: 'Erro ao importar dados. Verifique o arquivo.',
            variant: 'destructive'
          })
        }
      }
      reader.readAsText(file)
    }
  }
  input.click()
}

// Lifecycle
onMounted(() => {
  loadSettings()
})
</script>
