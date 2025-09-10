<template>
  <div class="space-y-6 md:space-y-8">
    <div class="text-center">
      <h1 class="text-2xl md:text-4xl font-bold text-foreground mb-4">
        ℹ️ Sobre o CKO Framework
      </h1>
      <p class="text-lg md:text-xl text-muted-foreground max-w-3xl mx-auto px-4">
        Um framework full-stack moderno e seguro para desenvolvimento rápido de MVPs e micro-projetos
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
      <div class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>🎯 Objetivo</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-muted-foreground">
              O CKO Framework foi criado para acelerar o desenvolvimento de projetos web,
              oferecendo uma stack moderna, segura e fácil de usar.
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>🚀 Características</CardTitle>
          </CardHeader>
          <CardContent>
            <ul class="space-y-2 text-muted-foreground">
              <li>• Desenvolvimento rápido com hot reload</li>
              <li>• Containers Docker isolados e seguros</li>
              <li>• API RESTful com Slim Framework</li>
              <li>• Frontend moderno com Vue 3</li>
              <li>• ORM Eloquent para banco de dados</li>
              <li>• Flexibilidade entre SQLite e MySQL</li>
            </ul>
          </CardContent>
        </Card>
      </div>

      <div class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>🛠️ Tecnologias</CardTitle>
          </CardHeader>
          <CardContent>
            <ul class="space-y-2 text-muted-foreground">
              <li>• Desenvolvimento rápido com hot reload</li>
              <li>• Containers Docker isolados e seguros</li>
              <li>• API RESTful com Slim Framework</li>
              <li>• Frontend moderno com Vue 3</li>
              <li>• ORM Eloquent para banco de dados</li>
              <li>• Flexibilidade entre SQLite e MySQL</li>
            </ul>
            </CardContent>
          </Card>
      </div>

      <div class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>🛠️ Tecnologias</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div>
                <h4 class="font-medium">Backend</h4>
                <p class="text-sm text-muted-foreground">PHP 8.2, Slim Framework, Eloquent ORM</p>
              </div>
              <div>
                <h4 class="font-medium">Frontend</h4>
                <p class="text-sm text-muted-foreground">Vue 3, Tailwind CSS, Shadcn/ui</p>
              </div>
              <div>
                <h4 class="font-medium">Infraestrutura</h4>
                <p class="text-sm text-muted-foreground">Docker, Nginx, SQLite/MySQL</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>📚 Como Usar</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-2 text-sm text-muted-foreground">
              <p><strong>1.</strong> Clone o repositório</p>
              <p><strong>2.</strong> Execute: <code class="bg-muted px-2 py-1 rounded">./scripts/setup.sh</code></p>
              <p><strong>3.</strong> Acesse: http://localhost:3000</p>
              <p><strong>4.</strong> API: http://localhost:8000</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>🔒 Segurança</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-muted-foreground mb-4">
          O framework foi projetado com segurança em mente desde o início:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <h4 class="font-medium">Containers</h4>
            <ul class="text-sm text-muted-foreground space-y-1">
              <li>• Usuários não-root</li>
              <li>• Volumes limitados</li>
              <li>• Network isolation</li>
            </ul>
          </div>
          <div class="space-y-2">
            <h4 class="font-medium">Aplicação</h4>
            <ul class="text-sm text-muted-foreground space-y-1">
              <li>• Headers de segurança</li>
              <li>• Validação de entrada</li>
              <li>• CORS configurado</li>
            </ul>
          </div>
        </div>
      </CardContent>
    </Card>

    <div class="text-center">
      <p class="text-muted-foreground">
        <strong>Hot Reload Test:</strong> Edite este arquivo e veja as mudanças em tempo real!
      </p>
      <p class="text-sm text-muted-foreground mt-2">
        Última atualização: {{ lastUpdate }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const lastUpdate = ref(new Date().toLocaleString('pt-BR'))

onMounted(() => {
  setInterval(() => {
    lastUpdate.value = new Date().toLocaleString('pt-BR')
  }, 1000)
})
</script>