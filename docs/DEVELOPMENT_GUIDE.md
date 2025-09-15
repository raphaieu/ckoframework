# CKO Framework - Guia de Desenvolvimento

## 🚀 Guia Completo para Desenvolvedores

Este guia fornece instruções detalhadas para configurar, desenvolver e contribuir com o CKO Framework.

## 📋 Índice

1. [Configuração do Ambiente](#configuração-do-ambiente)
2. [Estrutura do Projeto](#estrutura-do-projeto)
3. [Desenvolvimento Backend](#desenvolvimento-backend)
4. [Desenvolvimento Frontend](#desenvolvimento-frontend)
5. [Sistema de AI](#sistema-de-ai)
6. [Testes](#testes)
7. [Deploy](#deploy)
8. [Contribuição](#contribuição)

## 🛠️ Configuração do Ambiente

### Pré-requisitos

**Sistema Operacional:**
- Linux (Ubuntu 20.04+)
- macOS (10.15+)
- Windows 10+ (com WSL2 recomendado)

**Ferramentas Necessárias:**
- **Node.js** 18+ e npm
- **PHP** 8.2+ com extensões: sqlite, json, curl, mbstring
- **Composer** 2.0+
- **Git** 2.30+

### Instalação das Ferramentas

#### Ubuntu/Debian
```bash
# Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# PHP 8.2
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-sqlite3 php8.2-json php8.2-curl php8.2-mbstring

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Git
sudo apt install -y git
```

#### macOS
```bash
# Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Node.js
brew install node

# PHP
brew install php@8.2

# Composer
brew install composer

# Git
brew install git
```

#### Windows (WSL2)
```bash
# Instalar WSL2
wsl --install

# Dentro do WSL2, seguir instruções do Ubuntu
```

### Configuração do Projeto

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/ckoframework.git
cd ckoframework

# 2. Configurar Backend
cd api
composer install
cp env.example .env

# 3. Configurar Frontend
cd ../frontend
npm install

# 4. Configurar Banco de Dados
cd ../api
php setup_database.php

# 5. Iniciar Serviços
# Terminal 1 - Backend
php -S localhost:8000 -t public

# Terminal 2 - Frontend
cd ../frontend
npm run dev
```

## 🏗️ Estrutura do Projeto

```
ckoframework/
├── api/                     # Backend PHP
│   ├── src/
│   │   ├── AI/            # Sistema de AI
│   │   │   ├── Agents/    # Agentes Neuron AI
│   │   │   ├── Tools/     # Tools unificadas
│   │   │   ├── Adapters/  # Adaptadores
│   │   │   ├── Core/      # Interfaces
│   │   │   └── Servers/   # Servidores MCP
│   │   ├── Controllers/   # Controladores
│   │   ├── Models/        # Modelos
│   │   └── Routes/        # Rotas
│   ├── database/          # Migrações e seeders
│   ├── public/           # Ponto de entrada
│   └── composer.json
├── frontend/               # Frontend Vue 3
│   ├── src/
│   │   ├── components/    # Componentes
│   │   ├── views/         # Páginas
│   │   ├── stores/        # Estado (Pinia)
│   │   ├── router/        # Rotas
│   │   └── lib/           # Utilitários
│   ├── package.json
│   └── vite.config.js
└── docs/                  # Documentação
```

## 🔧 Desenvolvimento Backend

### Estrutura de um Controller

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        try {
            $users = User::all();
            
            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validação
            $this->validate($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8'
            ]);

            // Criar usuário
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT)
            ]);

            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
}
```

### Estrutura de um Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    // Relacionamentos
    public function cashflowTransactions()
    {
        return $this->hasMany(CashflowTransaction::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function holdings()
    {
        return $this->hasMany(Holding::class);
    }
}
```

### Estrutura de uma Tool

```php
<?php

namespace CkoFramework\AI\Tools;

use CkoFramework\AI\Core\ToolInterface;

class CustomTool implements ToolInterface
{
    public function getName(): string
    {
        return 'custom_analysis';
    }

    public function getDescription(): string
    {
        return 'Custom analysis tool for specific business logic';
    }

    public function getParameters(): array
    {
        return [
            'param1' => [
                'type' => 'string',
                'description' => 'First parameter',
                'required' => true
            ],
            'param2' => [
                'type' => 'number',
                'description' => 'Second parameter',
                'required' => false,
                'default' => 0
            ]
        ];
    }

    public function execute(array $parameters): array
    {
        try {
            // Lógica da tool
            $result = $this->performAnalysis($parameters);
            
            return [
                'success' => true,
                'summary' => 'Analysis completed successfully',
                'data' => $result,
                'metadata' => [
                    'tool_name' => $this->getName(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function getMetadata(): array
    {
        return [
            'version' => '1.0.0',
            'author' => 'CKO Framework',
            'category' => 'custom'
        ];
    }

    private function performAnalysis(array $parameters): array
    {
        // Implementar lógica específica
        return [];
    }
}
```

### Estrutura de um Agente

```php
<?php

namespace CkoFramework\AI\Agents;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use CkoFramework\AI\Core\ToolRegistry;

class CustomAgent extends Agent
{
    private ToolRegistry $toolRegistry;

    public function __construct()
    {
        $this->toolRegistry = new ToolRegistry();
        $this->registerTools();
    }

    private function registerTools(): void
    {
        $this->toolRegistry->registerTool(new CustomTool());
        
        // Adicionar tools ao Neuron AI
        $this->addTool($this->toolRegistry->getNeuronAdapter('custom_analysis'));
    }

    public function analyze(string $query): array
    {
        try {
            $response = $this->chat([
                new UserMessage($query)
            ]);

            return [
                'query' => $query,
                'response' => $response->getContent(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            return [
                'query' => $query,
                'response' => 'Desculpe, ocorreu um erro.',
                'error' => $e->getMessage()
            ];
        }
    }
}
```

## 🎨 Desenvolvimento Frontend

### Estrutura de um Componente

```vue
<template>
  <div class="custom-component">
    <h2 class="text-xl font-semibold mb-4">{{ title }}</h2>
    
    <div v-if="loading" class="flex justify-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
    
    <div v-else-if="error" class="text-red-500">
      {{ error }}
    </div>
    
    <div v-else class="space-y-4">
      <div
        v-for="item in data"
        :key="item.id"
        class="p-4 border rounded-lg"
      >
        {{ item.name }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'

// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  }
})

// Composables
const { loading, error, request } = useApi()

// Data
const data = ref([])

// Methods
const fetchData = async () => {
  try {
    data.value = await request({
      method: 'GET',
      url: '/custom-endpoint'
    })
  } catch (err) {
    console.error('Error fetching data:', err)
  }
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.custom-component {
  @apply p-6 bg-white rounded-lg shadow-sm;
}
</style>
```

### Estrutura de uma Store

```javascript
// stores/custom.js
import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

export const useCustomStore = defineStore('custom', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    filters: {
      search: '',
      category: '',
      dateRange: null
    }
  }),

  getters: {
    filteredItems: (state) => {
      let filtered = state.items

      if (state.filters.search) {
        filtered = filtered.filter(item =>
          item.name.toLowerCase().includes(state.filters.search.toLowerCase())
        )
      }

      if (state.filters.category) {
        filtered = filtered.filter(item =>
          item.category === state.filters.category
        )
      }

      return filtered
    },

    totalItems: (state) => state.items.length,
    totalValue: (state) => state.items.reduce((sum, item) => sum + item.value, 0)
  },

  actions: {
    async fetchItems() {
      this.loading = true
      this.error = null

      try {
        const { request } = useApi()
        this.items = await request({
          method: 'GET',
          url: '/custom-items'
        })
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async addItem(item) {
      try {
        const { request } = useApi()
        const newItem = await request({
          method: 'POST',
          url: '/custom-items',
          data: item
        })
        
        this.items.push(newItem)
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async updateItem(id, updates) {
      try {
        const { request } = useApi()
        const updatedItem = await request({
          method: 'PUT',
          url: `/custom-items/${id}`,
          data: updates
        })
        
        const index = this.items.findIndex(item => item.id === id)
        if (index !== -1) {
          this.items[index] = updatedItem
        }
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    async deleteItem(id) {
      try {
        const { request } = useApi()
        await request({
          method: 'DELETE',
          url: `/custom-items/${id}`
        })
        
        this.items = this.items.filter(item => item.id !== id)
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        search: '',
        category: '',
        dateRange: null
      }
    }
  }
})
```

### Estrutura de uma View

```vue
<template>
  <div class="custom-view">
    <AppHeader :title="'Custom View'" />
    
    <div class="flex-1 p-6">
      <!-- Filtros -->
      <div class="mb-6">
        <div class="flex space-x-4">
          <Input
            v-model="filters.search"
            placeholder="Buscar..."
            class="flex-1"
          />
          <Select v-model="filters.category">
            <SelectItem value="">Todas as categorias</SelectItem>
            <SelectItem value="category1">Categoria 1</SelectItem>
            <SelectItem value="category2">Categoria 2</SelectItem>
          </Select>
          <Button @click="applyFilters">Filtrar</Button>
        </div>
      </div>

      <!-- Lista -->
      <div class="space-y-4">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="p-4 border rounded-lg hover:shadow-md transition-shadow"
        >
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-semibold">{{ item.name }}</h3>
              <p class="text-sm text-gray-600">{{ item.description }}</p>
            </div>
            <div class="flex space-x-2">
              <Button
                variant="outline"
                size="sm"
                @click="editItem(item)"
              >
                Editar
              </Button>
              <Button
                variant="destructive"
                size="sm"
                @click="deleteItem(item.id)"
              >
                Excluir
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Paginação -->
      <div class="mt-6 flex justify-center">
        <Pagination
          :current-page="currentPage"
          :total-pages="totalPages"
          @page-change="handlePageChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCustomStore } from '@/stores/custom'
import AppHeader from '@/components/layout/AppHeader.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectItem } from '@/components/ui/select'
import Pagination from '@/components/ui/Pagination.vue'

// Store
const customStore = useCustomStore()

// Data
const filters = ref({
  search: '',
  category: ''
})

const currentPage = ref(1)
const itemsPerPage = 10

// Computed
const filteredItems = computed(() => customStore.filteredItems)
const totalPages = computed(() => Math.ceil(customStore.totalItems / itemsPerPage))

// Methods
const applyFilters = () => {
  customStore.setFilters(filters.value)
}

const editItem = (item) => {
  // Implementar edição
  console.log('Edit item:', item)
}

const deleteItem = async (id) => {
  if (confirm('Tem certeza que deseja excluir este item?')) {
    try {
      await customStore.deleteItem(id)
    } catch (error) {
      console.error('Error deleting item:', error)
    }
  }
}

const handlePageChange = (page) => {
  currentPage.value = page
  // Implementar paginação
}

// Lifecycle
onMounted(() => {
  customStore.fetchItems()
})
</script>
```

## 🧠 Sistema de AI

### Criando uma Nova Tool

1. **Implementar ToolInterface:**
```php
class NewTool implements ToolInterface
{
    // Implementar métodos obrigatórios
}
```

2. **Registrar no ToolRegistry:**
```php
$registry->registerTool(new NewTool());
```

3. **Adicionar ao Agente:**
```php
$agent->addTool($registry->getNeuronAdapter('new_tool'));
```

### Criando um Novo Agente

1. **Estender Agent:**
```php
class NewAgent extends Agent
{
    // Implementar lógica específica
}
```

2. **Configurar Tools:**
```php
public function __construct()
{
    $this->toolRegistry = new ToolRegistry();
    $this->registerTools();
}
```

3. **Implementar Métodos:**
```php
public function analyze(string $query): array
{
    // Lógica de análise
}
```

## 🧪 Testes

### Testes Backend

```php
// tests/Unit/FinancialToolTest.php
class FinancialToolTest extends TestCase
{
    public function testExecuteCashflowAnalysis()
    {
        $tool = new FinancialTool();
        $result = $tool->execute([
            'analysis_type' => 'cashflow',
            'period' => 'month'
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }
}
```

### Testes Frontend

```javascript
// tests/components/FinancialCard.test.js
import { mount } from '@vue/test-utils'
import FinancialCard from '@/components/FinancialCard.vue'

describe('FinancialCard', () => {
  it('renders correctly', () => {
    const wrapper = mount(FinancialCard, {
      props: {
        title: 'Test Title',
        value: 'R$ 1.000,00'
      }
    })

    expect(wrapper.text()).toContain('Test Title')
    expect(wrapper.text()).toContain('R$ 1.000,00')
  })
})
```

### Executar Testes

```bash
# Backend
cd api
composer test

# Frontend
cd frontend
npm run test
```

## 🚀 Deploy

### Desenvolvimento

```bash
# Backend
cd api
php -S localhost:8000 -t public

# Frontend
cd frontend
npm run dev
```

### Produção

1. **Configurar Servidor Web:**
```nginx
# Nginx
server {
    listen 80;
    server_name api.ckoframework.com;
    root /var/www/ckoframework/api/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

2. **Configurar Frontend:**
```bash
cd frontend
npm run build
# Copiar arquivos de dist/ para servidor web
```

3. **Configurar Banco de Dados:**
```bash
cd api
php setup_database.php
```

## 🤝 Contribuição

### Fluxo de Contribuição

1. **Fork do Repositório**
2. **Criar Branch:**
   ```bash
   git checkout -b feature/nova-funcionalidade
   ```
3. **Fazer Alterações**
4. **Executar Testes:**
   ```bash
   # Backend
   cd api && composer test
   
   # Frontend
   cd frontend && npm run test
   ```
5. **Commit:**
   ```bash
   git commit -m "feat: adiciona nova funcionalidade"
   ```
6. **Push:**
   ```bash
   git push origin feature/nova-funcionalidade
   ```
7. **Pull Request**

### Padrões de Código

**PHP (PSR-12):**
```bash
cd api
composer cs
```

**JavaScript (ESLint):**
```bash
cd frontend
npm run lint
```

### Commits

Seguir padrão [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Tarefas de manutenção

## 📚 Recursos Adicionais

### Documentação
- [Vue 3 Docs](https://vuejs.org/)
- [Slim Framework](https://www.slimframework.com/)
- [TailwindCSS](https://tailwindcss.com/)
- [Neuron AI](https://github.com/inspector-apm/neuron-ai)

### Ferramentas
- [VS Code](https://code.visualstudio.com/)
- [PHPStorm](https://www.jetbrains.com/phpstorm/)
- [Postman](https://www.postman.com/)

### Comunidade
- [Discord](https://discord.gg/ckoframework)
- [GitHub Issues](https://github.com/seu-usuario/ckoframework/issues)
- [Wiki](https://github.com/seu-usuario/ckoframework/wiki)

---

**Guia de Desenvolvimento CKO Framework v1.0**
