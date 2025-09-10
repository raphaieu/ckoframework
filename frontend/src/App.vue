<template>
  <div id="app" class="min-h-screen bg-background">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Layout -->
    <div class="flex">
      <!-- Sidebar -->
      <AppSidebar />
      
      <!-- Main Content -->
      <main class="flex-1 p-6">
        <router-view />
      </main>
    </div>

    <!-- Toast Notifications -->
    <Toaster />
  </div>
</template>

<script setup>
import { Toaster } from '@/components/ui/toast'
import AppHeader from '@/components/finance/AppHeader.vue'
import AppSidebar from '@/components/finance/AppSidebar.vue'
</script>

<style scoped>
/* Estilos específicos do componente se necessário */
</style>