<template>
  <header class="border-b bg-card sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
          <h1 class="text-xl md:text-2xl font-bold text-foreground">
            💰 {{ appName }}
          </h1>
        </div>
        
        <!-- Search and Actions -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="relative hidden md:block">
            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input 
              placeholder="Buscar transações, trades..." 
              class="pl-10 w-64"
              v-model="searchQuery"
              @keyup.enter="handleSearch"
            />
          </div>
          
          <!-- Quick Actions -->
          <div class="flex items-center space-x-2">
            <Button @click="openQuickTransaction" variant="outline" size="sm">
              <Plus class="h-4 w-4 mr-2" />
              Lançar
            </Button>
            
            <Button @click="openNotifications" variant="outline" size="sm">
              <Bell class="h-4 w-4" />
              <span v-if="unreadNotifications > 0" class="ml-1 text-xs bg-red-500 text-white rounded-full px-1 min-w-[16px] h-4 flex items-center justify-center">
                {{ unreadNotifications }}
              </span>
            </Button>
            
            <!-- User Menu -->
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="sm" class="relative h-8 w-8 rounded-full">
                  <Avatar class="h-8 w-8">
                    <AvatarImage src="/placeholder-user.jpg" alt="User" />
                    <AvatarFallback>R</AvatarFallback>
                  </Avatar>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-56" align="end">
                <DropdownMenuLabel class="font-normal">
                  <div class="flex flex-col space-y-1">
                    <p class="text-sm font-medium leading-none">Raphael Martins</p>
                    <p class="text-xs leading-none text-muted-foreground">
                      rapha@raphael-martins.com
                    </p>
                  </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="goToSettings">
                  <Settings class="mr-2 h-4 w-4" />
                  <span>Configurações</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="exportData">
                  <Download class="mr-2 h-4 w-4" />
                  <span>Exportar Dados</span>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="logout">
                  <LogOut class="mr-2 h-4 w-4" />
                  <span>Sair</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'
import { 
  Search, 
  Plus, 
  Bell, 
  Settings, 
  Download, 
  LogOut 
} from 'lucide-vue-next'
import { config } from '@/config/app'
import { useToast } from '@/components/ui/toast/use-toast'

const router = useRouter()
const { toast } = useToast()

// Estado local
const searchQuery = ref('')
const unreadNotifications = ref(3)
const appName = config.app.name

// Métodos
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Implementar busca global
    console.log('Buscar:', searchQuery.value)
    toast({
      title: 'Em desenvolvimento',
      description: 'Funcionalidade de busca será implementada em breve.',
    })
  }
}

const openQuickTransaction = () => {
  // Implementar modal rápido de transação
  console.log('Abrir transação rápida')
  toast({
    title: 'Em desenvolvimento',
    description: 'Modal de transação rápida será implementado em breve.',
  })
}

const openNotifications = () => {
  // Implementar painel de notificações
  console.log('Abrir notificações')
  toast({
    title: 'Em desenvolvimento',
    description: 'Painel de notificações será implementado em breve.',
  })
}

const goToSettings = () => {
  router.push('/settings')
}

const exportData = () => {
  // Implementar exportação de dados
  console.log('Exportar dados')
  toast({
    title: 'Em desenvolvimento',
    description: 'Funcionalidade de exportação será implementada em breve.',
  })
}

const logout = () => {
  // Implementar logout
  console.log('Logout')
  toast({
    title: 'Em desenvolvimento',
    description: 'Funcionalidade de logout será implementada em breve.',
  })
}
</script>
