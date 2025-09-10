<template>
  <div class="space-y-4 md:space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <h1 class="text-2xl md:text-3xl font-bold text-foreground">👥 Usuários</h1>
      <Button @click="showCreateForm = true" class="w-full sm:w-auto">
        + Novo Usuário
      </Button>
    </div>

    <!-- Lista de Usuários -->
    <Card>
      <CardHeader>
        <CardTitle>Lista de Usuários</CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="loading" class="text-center py-8">
          <p class="text-muted-foreground">Carregando usuários...</p>
        </div>
        
        <div v-else-if="error" class="text-center py-8">
          <p class="text-destructive">{{ error }}</p>
          <Button @click="loadUsers" variant="secondary" class="mt-2">
            Tentar Novamente
          </Button>
        </div>
        
        <div v-else-if="users.length === 0" class="text-center py-8">
          <p class="text-muted-foreground">Nenhum usuário encontrado</p>
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="user in users" 
            :key="user.id"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 border rounded-lg gap-4"
          >
            <div class="flex-1">
              <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <h4 class="font-medium">{{ user.name }}</h4>
                <Badge variant="secondary" class="w-fit">ID: {{ user.id }}</Badge>
              </div>
              <p class="text-muted-foreground">{{ user.email }}</p>
              <p class="text-sm text-muted-foreground">
                Criado em: {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
              </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
              <Button @click="editUser(user)" variant="secondary" class="w-full sm:w-auto">
                Editar
              </Button>
              <Button @click="deleteUser(user.id)" variant="destructive" class="w-full sm:w-auto">
                Excluir
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Dialog de Criação/Edição -->
    <Dialog v-model:open="showCreateForm">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>
            {{ editingUser ? 'Editar Usuário' : 'Novo Usuário' }}
          </DialogTitle>
        </DialogHeader>
        <form @submit.prevent="editingUser ? updateUser() : createUser()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Nome</label>
            <Input 
              v-model="form.name" 
              type="text" 
              required
              placeholder="Digite o nome"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Email</label>
            <Input 
              v-model="form.email" 
              type="email" 
              required
              placeholder="Digite o email"
            />
          </div>
          
          <DialogFooter>
            <Button type="button" @click="closeForm" variant="secondary">
              Cancelar
            </Button>
            <Button type="submit">
              {{ editingUser ? 'Atualizar' : 'Criar' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Badge } from '@/components/ui/badge'
import { useToast } from '@/components/ui/toast/use-toast'
import { apiUrl } from '@/config/env'

const users = ref([])
const loading = ref(false)
const error = ref(null)
const showCreateForm = ref(false)
const editingUser = ref(null)
const form = ref({
  name: '',
  email: ''
})

const { toast } = useToast()

const loadUsers = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await fetch(apiUrl('users'))
    const data = await response.json()
    
    if (data.success) {
      users.value = data.data
    } else {
      error.value = data.message
    }
  } catch (err) {
    error.value = 'Erro ao carregar usuários: ' + err.message
  } finally {
    loading.value = false
  }
}

const createUser = async () => {
  try {
    const response = await fetch(apiUrl('users'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form.value)
    })
    
    const data = await response.json()
    
    if (data.success) {
      await loadUsers()
      closeForm()
      form.value = { name: '', email: '' }
      toast({
        title: "Sucesso!",
        description: "Usuário criado com sucesso.",
      })
    } else {
      error.value = data.message
      toast({
        title: "Erro",
        description: data.message,
        variant: "destructive",
      })
    }
  } catch (err) {
    error.value = 'Erro ao criar usuário: ' + err.message
    toast({
      title: "Erro",
      description: 'Erro ao criar usuário: ' + err.message,
      variant: "destructive",
    })
  }
}

const editUser = (user) => {
  showCreateForm.value = true
  editingUser.value = user
  form.value = { ...user }
}

const updateUser = async () => {
  try {
    const response = await fetch(apiUrl(`users/${editingUser.value.id}`), {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form.value)
    })
    
    const data = await response.json()
    
    if (data.success) {
      await loadUsers()
      closeForm()
      toast({
        title: "Sucesso!",
        description: "Usuário atualizado com sucesso.",
      })
    } else {
      error.value = data.message
      toast({
        title: "Erro",
        description: data.message,
        variant: "destructive",
      })
    }
  } catch (err) {
    error.value = 'Erro ao atualizar usuário: ' + err.message
  }
}

const deleteUser = async (id) => {
  if (!confirm('Tem certeza que deseja excluir este usuário?')) return
  
  try {
    const response = await fetch(apiUrl(`users/${id}`), {
      method: 'DELETE'
    })
    
    const data = await response.json()
    
    if (data.success) {
      await loadUsers()
      toast({
        title: "Sucesso!",
        description: "Usuário excluído com sucesso.",
      })
    } else {
      error.value = data.message
      toast({
        title: "Erro",
        description: data.message,
        variant: "destructive",
      })
    }
  } catch (err) {
    error.value = 'Erro ao excluir usuário: ' + err.message
  }
}

const closeForm = () => {
  showCreateForm.value = false
  editingUser.value = null
  form.value = { name: '', email: '' }
}

onMounted(() => {
  loadUsers()
})
</script>