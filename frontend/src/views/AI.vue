<template>
  <div class="ai-chat-container">
    <!-- Header -->
    <div class="ai-header">
      <div class="flex items-center gap-3">
        <div class="ai-icon">
          <Bot class="h-6 w-6" />
        </div>
        <div>
          <h1 class="text-2xl font-bold">AI Financeiro</h1>
          <p class="text-muted-foreground">Assistente inteligente para análise financeira</p>
        </div>
      </div>
      <div class="ai-status">
        <Badge :variant="aiStatus === 'active' ? 'default' : 'destructive'">
          {{ aiStatus === 'active' ? 'Conectado' : 'Desconectado' }}
        </Badge>
        <span class="text-sm text-muted-foreground ml-2">
          {{ aiProvider }} - {{ aiModel }}
        </span>
      </div>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" ref="chatContainer">
      <div v-for="message in messages" :key="message.id" class="message-container">
        <div :class="['message', message.type]">
          <div class="message-avatar">
            <Bot v-if="message.type === 'ai'" class="h-5 w-5" />
            <User v-else class="h-5 w-5" />
          </div>
          <div class="message-content">
            <div class="message-header">
              <span class="font-medium">
                {{ message.type === 'ai' ? 'AI Financeiro' : 'Você' }}
              </span>
              <span class="text-xs text-muted-foreground ml-2">
                {{ formatTime(message.timestamp) }}
              </span>
            </div>
            <div class="message-text" v-html="formatMessage(message.content)"></div>
            
            <!-- AI Response Data -->
            <div v-if="message.type === 'ai' && message.data" class="message-data">
              <Collapsible>
                <CollapsibleTrigger class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                  <ChevronDown class="h-4 w-4" />
                  Ver dados financeiros
                </CollapsibleTrigger>
                <CollapsibleContent class="mt-2">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Cashflow Data -->
                    <Card>
                      <CardHeader class="pb-2">
                        <CardTitle class="text-sm">Fluxo de Caixa</CardTitle>
                      </CardHeader>
                      <CardContent class="space-y-1">
                        <div class="flex justify-between text-sm">
                          <span>Receitas:</span>
                          <span class="text-green-600">
                            {{ formatCurrency(message.data.cashflow?.total_income) }}
                          </span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span>Despesas:</span>
                          <span class="text-red-600">
                            {{ formatCurrency(message.data.cashflow?.total_expenses) }}
                          </span>
                        </div>
                        <div class="flex justify-between text-sm font-medium">
                          <span>Saldo:</span>
                          <span :class="message.data.cashflow?.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ formatCurrency(message.data.cashflow?.balance) }}
                          </span>
                        </div>
                      </CardContent>
                    </Card>

                    <!-- Trades Data -->
                    <Card>
                      <CardHeader class="pb-2">
                        <CardTitle class="text-sm">Trades</CardTitle>
                      </CardHeader>
                      <CardContent class="space-y-1">
                        <div class="flex justify-between text-sm">
                          <span>Total:</span>
                          <span>{{ message.data.trades?.total_trades }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span>P&L:</span>
                          <span :class="message.data.trades?.total_pnl >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ formatCurrency(message.data.trades?.total_pnl) }}
                          </span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span>Taxa Acerto:</span>
                          <span>{{ message.data.trades?.win_rate }}%</span>
                        </div>
                      </CardContent>
                    </Card>

                    <!-- Holdings Data -->
                    <Card>
                      <CardHeader class="pb-2">
                        <CardTitle class="text-sm">Portfólio</CardTitle>
                      </CardHeader>
                      <CardContent class="space-y-1">
                        <div class="flex justify-between text-sm">
                          <span>Holdings:</span>
                          <span>{{ message.data.holdings?.total_holdings }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span>Valor:</span>
                          <span>{{ formatCurrency(message.data.holdings?.total_value) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span>P&L:</span>
                          <span :class="message.data.holdings?.total_pnl >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ message.data.holdings?.total_pnl_percent }}%
                          </span>
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </CollapsibleContent>
              </Collapsible>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="isLoading" class="message-container">
        <div class="message ai">
          <div class="message-avatar">
            <Bot class="h-5 w-5" />
          </div>
          <div class="message-content">
            <div class="message-text">
              <div class="flex items-center gap-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                <span>Analisando dados financeiros...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input">
      <div class="flex gap-2">
        <div class="flex-1">
          <Textarea
            v-model="inputMessage"
            placeholder="Pergunte sobre sua situação financeira... (ex: Analise meu fluxo de caixa)"
            class="min-h-[60px] resize-none"
            @keydown.enter.prevent="sendMessage"
            :disabled="isLoading"
          />
        </div>
        <Button @click="sendMessage" :disabled="!inputMessage.trim() || isLoading" size="lg">
          <Send class="h-4 w-4" />
        </Button>
      </div>
      
      <!-- Quick Actions -->
      <div class="quick-actions">
        <div class="text-sm text-muted-foreground mb-2">Perguntas rápidas:</div>
        <div class="flex flex-wrap gap-2">
          <Button
            v-for="quickQuestion in quickQuestions"
            :key="quickQuestion"
            variant="outline"
            size="sm"
            @click="sendQuickQuestion(quickQuestion)"
            :disabled="isLoading"
          >
            {{ quickQuestion }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import { Bot, User, Send, ChevronDown } from 'lucide-vue-next'
import { Button } from '@/components/ui/button/index.js'
import { Textarea } from '@/components/ui/textarea/index.js'
import { Badge } from '@/components/ui/badge/index.js'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card/index.js'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible/index.js'
import { formatCurrency, formatDate } from '@/lib/utils/formatters.js'

// Reactive data
const messages = ref([])
const inputMessage = ref('')
const isLoading = ref(false)
const aiStatus = ref('unknown')
const aiProvider = ref('unknown')
const aiModel = ref('unknown')
const chatContainer = ref(null)

// Quick questions
const quickQuestions = ref([
  'Analise meu fluxo de caixa',
  'Como está minha performance de trades?',
  'Sugira melhorias no meu portfólio',
  'Qual minha situação financeira geral?',
  'Identifique riscos financeiros',
  'Recomende estratégias de investimento'
])

// Methods
const sendMessage = async () => {
  if (!inputMessage.value.trim() || isLoading.value) return
  
  const userMessage = inputMessage.value.trim()
  inputMessage.value = ''
  
  // Add user message
  addMessage('user', userMessage)
  
  // Send to AI
  await analyzeWithAI(userMessage)
}

const sendQuickQuestion = (question) => {
  inputMessage.value = question
  sendMessage()
}

const addMessage = (type, content, data = null) => {
  messages.value.push({
    id: Date.now() + Math.random(),
    type,
    content,
    data,
    timestamp: new Date()
  })
  
  nextTick(() => {
    scrollToBottom()
  })
}

const analyzeWithAI = async (query) => {
  isLoading.value = true
  
  try {
    const response = await fetch('http://localhost:8000/api/ai/analyze', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ query })
    })
    
    const result = await response.json()
    
    if (result.success) {
      addMessage('ai', result.data.response, result.data.data)
    } else {
      addMessage('ai', `Erro: ${result.error}`)
    }
  } catch (error) {
    console.error('Erro ao analisar com AI:', error)
    addMessage('ai', `Erro de conexão: ${error.message}`)
  } finally {
    isLoading.value = false
  }
}

const checkAIStatus = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/ai/status')
    const result = await response.json()
    
    if (result.success) {
      aiStatus.value = result.status
      aiProvider.value = result.provider
      aiModel.value = result.model
    }
  } catch (error) {
    console.error('Erro ao verificar status do AI:', error)
    aiStatus.value = 'error'
  }
}

const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

const formatMessage = (content) => {
  // Convert markdown-like formatting to HTML
  return content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/\n/g, '<br>')
    .replace(/(\d+\.\s)/g, '<br>$1')
    .replace(/^<br>/, '')
}

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(() => {
  checkAIStatus()
  
  // Add welcome message
  addMessage('ai', 'Olá! Sou seu assistente financeiro inteligente. Posso analisar seu fluxo de caixa, trades, portfólio e muito mais. Como posso ajudar você hoje?')
})
</script>

<style scoped>
.ai-chat-container {
  @apply h-full flex flex-col bg-background;
}

.ai-header {
  @apply flex items-center justify-between p-6 border-b bg-card;
}

.ai-icon {
  @apply p-2 rounded-lg bg-primary/10 text-primary;
}

.ai-status {
  @apply flex items-center;
}

.chat-messages {
  @apply flex-1 overflow-y-auto p-6 space-y-4;
}

.message-container {
  @apply w-full;
}

.message {
  @apply flex gap-3 max-w-4xl;
}

.message.user {
  @apply ml-auto flex-row-reverse;
}

.message-avatar {
  @apply flex-shrink-0 w-8 h-8 rounded-full bg-muted flex items-center justify-center;
}

.message.user .message-avatar {
  @apply bg-primary text-primary-foreground;
}

.message-content {
  @apply flex-1 min-w-0;
}

.message-header {
  @apply flex items-center mb-1;
}

.message-text {
  @apply text-sm leading-relaxed;
}

.message-data {
  @apply mt-3 p-3 bg-muted/50 rounded-lg;
}

.chat-input {
  @apply p-6 border-t bg-card space-y-4;
}

.quick-actions {
  @apply space-y-2;
}

/* Scrollbar styling */
.chat-messages::-webkit-scrollbar {
  @apply w-2;
}

.chat-messages::-webkit-scrollbar-track {
  @apply bg-muted/50;
}

.chat-messages::-webkit-scrollbar-thumb {
  @apply bg-muted-foreground/30 rounded-full;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  @apply bg-muted-foreground/50;
}
</style>
