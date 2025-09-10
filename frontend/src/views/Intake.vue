<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Intake</h1>
        <p class="text-muted-foreground">
          Processamento de documentos e anexos
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <Button @click="handleUpload" variant="default">
          <Upload class="h-4 w-4 mr-2" />
          Upload
        </Button>
      </div>
    </div>

    <!-- Status Cards -->
    <div class="grid gap-4 md:grid-cols-4">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Pendentes</CardTitle>
          <Clock class="h-4 w-4 text-yellow-600" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-yellow-600">
            {{ pendingItems }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Processados</CardTitle>
          <CheckCircle class="h-4 w-4 text-green-600" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-green-600">
            {{ processedItems }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Erros</CardTitle>
          <XCircle class="h-4 w-4 text-red-600" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold text-red-600">
            {{ errorItems }}
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Total</CardTitle>
          <FileText class="h-4 w-4" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">
            {{ totalItems }}
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Inbox -->
    <Card>
      <CardHeader>
        <CardTitle>Inbox</CardTitle>
        <CardDescription>
          Documentos e anexos para processamento
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div v-if="isLoading" class="flex items-center justify-center py-8">
          <div class="flex items-center space-x-2">
            <Loader2 class="h-4 w-4 animate-spin" />
            <span>Carregando...</span>
          </div>
        </div>
        
        <div v-else-if="intakeItems.length === 0" class="text-center py-8">
          <FileX class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
          <h3 class="text-lg font-medium mb-2">Nenhum item no inbox</h3>
          <p class="text-muted-foreground mb-4">
            Faça upload de documentos para começar o processamento
          </p>
          <Button @click="handleUpload">
            <Upload class="h-4 w-4 mr-2" />
            Fazer Upload
          </Button>
        </div>
        
        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Card 
            v-for="item in intakeItems" 
            :key="item.id"
            class="hover:shadow-lg transition-shadow cursor-pointer"
            @click="openItem(item)"
          >
            <CardHeader>
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <component :is="getFileIcon(item.type)" class="h-5 w-5" />
                  <span class="font-medium">{{ item.name }}</span>
                </div>
                <Badge :variant="getStatusVariant(item.status)">
                  {{ getStatusLabel(item.status) }}
                </Badge>
              </div>
            </CardHeader>
            <CardContent>
              <div class="space-y-2 text-sm text-muted-foreground">
                <div class="flex items-center justify-between">
                  <span>Tipo:</span>
                  <span>{{ getTypeLabel(item.type) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Tamanho:</span>
                  <span>{{ formatFileSize(item.size) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Upload:</span>
                  <span>{{ formatDate(item.uploaded_at) }}</span>
                </div>
                <div v-if="item.confidence" class="flex items-center justify-between">
                  <span>Confiança:</span>
                  <span>{{ item.confidence }}%</span>
                </div>
              </div>
              
              <div v-if="item.preview" class="mt-4">
                <img 
                  :src="item.preview" 
                  :alt="item.name"
                  class="w-full h-32 object-cover rounded"
                />
              </div>
              
              <div class="flex space-x-2 mt-4">
                <Button 
                  v-if="item.status === 'pending'"
                  @click.stop="processItem(item)" 
                  variant="outline" 
                  size="sm"
                  class="flex-1"
                >
                  <Play class="h-4 w-4 mr-2" />
                  Processar
                </Button>
                <Button 
                  v-if="item.status === 'processed'"
                  @click.stop="createTransaction(item)" 
                  variant="default" 
                  size="sm"
                  class="flex-1"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  Criar Transação
                </Button>
                <Button 
                  @click.stop="deleteItem(item)" 
                  variant="outline" 
                  size="sm"
                >
                  <Trash2 class="h-4 w-4" />
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
  Upload, 
  Clock, 
  CheckCircle, 
  XCircle, 
  FileText, 
  FileX, 
  Loader2,
  Play,
  Plus,
  Trash2,
  Image,
  File,
  Mic
} from 'lucide-vue-next'
import { useToast } from '@/components/ui/toast/use-toast'

const { toast } = useToast()

// Estado local
const isLoading = ref(false)
const intakeItems = ref([])

// Computed
const pendingItems = computed(() => 
  intakeItems.value.filter(item => item.status === 'pending').length
)

const processedItems = computed(() => 
  intakeItems.value.filter(item => item.status === 'processed').length
)

const errorItems = computed(() => 
  intakeItems.value.filter(item => item.status === 'error').length
)

const totalItems = computed(() => intakeItems.value.length)

// Métodos
const loadData = async () => {
  isLoading.value = true
  try {
    // Simular carregamento de dados
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Dados mock
    intakeItems.value = [
      {
        id: '1',
        name: 'nota_fiscal_001.pdf',
        type: 'pdf',
        size: 245760,
        status: 'pending',
        uploaded_at: new Date().toISOString(),
        confidence: null
      },
      {
        id: '2',
        name: 'comprovante_pix.jpg',
        type: 'image',
        size: 1024000,
        status: 'processed',
        uploaded_at: new Date(Date.now() - 86400000).toISOString(),
        confidence: 95,
        preview: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZjNzI4MCIgZHk9Ii4zZW0iIHRleHQtYW5jaG9yPSJtaWRkbGUiPkltYWdlbTwvdGV4dD48L3N2Zz4='
      },
      {
        id: '3',
        name: 'audio_gravacao.mp3',
        type: 'audio',
        size: 5120000,
        status: 'error',
        uploaded_at: new Date(Date.now() - 172800000).toISOString(),
        confidence: null
      }
    ]
  } finally {
    isLoading.value = false
  }
}

const getFileIcon = (type) => {
  switch (type) {
    case 'image':
      return Image
    case 'pdf':
      return File
    case 'audio':
      return Mic
    default:
      return File
  }
}

const getTypeLabel = (type) => {
  switch (type) {
    case 'image':
      return 'Imagem'
    case 'pdf':
      return 'PDF'
    case 'audio':
      return 'Áudio'
    default:
      return 'Arquivo'
  }
}

const getStatusVariant = (status) => {
  switch (status) {
    case 'pending':
      return 'secondary'
    case 'processed':
      return 'default'
    case 'error':
      return 'destructive'
    default:
      return 'secondary'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
    case 'pending':
      return 'Pendente'
    case 'processed':
      return 'Processado'
    case 'error':
      return 'Erro'
    default:
      return 'Desconhecido'
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const handleUpload = () => {
  toast({
    title: 'Em desenvolvimento',
    description: 'Funcionalidade de upload será implementada em breve.',
  })
}

const openItem = (item) => {
  console.log('Abrir item:', item)
  toast({
    title: 'Em desenvolvimento',
    description: 'Visualização de itens será implementada em breve.',
  })
}

const processItem = async (item) => {
  try {
    item.status = 'processing'
    // Simular processamento
    await new Promise(resolve => setTimeout(resolve, 2000))
    item.status = 'processed'
    item.confidence = Math.floor(Math.random() * 20) + 80 // 80-100%
    
    toast({
      title: 'Sucesso',
      description: 'Item processado com sucesso!',
    })
  } catch (error) {
    item.status = 'error'
    toast({
      title: 'Erro',
      description: 'Erro ao processar item.',
      variant: 'destructive'
    })
  }
}

const createTransaction = (item) => {
  console.log('Criar transação a partir do item:', item)
  toast({
    title: 'Em desenvolvimento',
    description: 'Criação de transação será implementada em breve.',
  })
}

const deleteItem = async (item) => {
  if (confirm('Tem certeza que deseja excluir este item?')) {
    const index = intakeItems.value.findIndex(i => i.id === item.id)
    if (index !== -1) {
      intakeItems.value.splice(index, 1)
      toast({
        title: 'Sucesso',
        description: 'Item excluído com sucesso!',
      })
    }
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>
