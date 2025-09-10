<template>
  <div class="space-y-6 md:space-y-8">
    <div class="text-center">
      <h1 class="text-2xl md:text-4xl font-bold text-foreground mb-4">
        🎉 Bem-vindo ao CKO Framework!
      </h1>
      <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto px-4">
        Framework full-stack para desenvolvimento rápido de MVPs e micro-projetos
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
      <Card>
        <CardHeader>
          <CardTitle>🚀 Backend PHP</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-muted-foreground">
            Slim Framework + Eloquent ORM para APIs rápidas e escaláveis
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>🎨 Frontend Vue</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-muted-foreground">
            Vue 3 + Tailwind CSS + Shadcn/ui para interfaces modernas
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>🐳 Docker</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-muted-foreground">
            Containers isolados e seguros para desenvolvimento e produção
          </p>
        </CardContent>
      </Card>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>🔧 Status dos Serviços</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span>API PHP (Porta 8000)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span>Frontend Vue (Porta 3000)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span>Banco SQLite</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span>Hot Reload Ativo</span>
          </div>
        </div>
      </CardContent>
    </Card>

    <div class="text-center">
      <p class="text-muted-foreground">
        <strong>Hot Reload Test:</strong> Edite este arquivo e veja as mudanças em tempo real!
      </p>
      <p class="text-sm text-muted-foreground mt-2">
        Timestamp atual: {{ currentTime }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const currentTime = ref(new Date().toLocaleTimeString())

onMounted(() => {
  setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString()
  }, 1000)
})
</script>