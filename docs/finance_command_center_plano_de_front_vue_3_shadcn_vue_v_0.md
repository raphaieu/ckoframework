# Finance Command Center — Plano de Front (Vue 3 SPA + shadcn-vue)

> **Objetivo**: Definir o passo‑a‑passo para construir o front do seu dashboard financeiro (Vue 3 + Tailwind + shadcn-vue), com estrutura de telas/rotas, componentes, tabelas/colunas, estados, mocks e contratos de dados que guiarão a modelagem e o backend futuramente.

> **Base**: CKO Framework - Framework full-stack para desenvolvimento rápido de MVPs e micro-projetos.

---

## 1) Stack & Padrões

- **Framework**: Vue 3 (Vite) — **SPA** (baseado no CKO Framework)
- **Estrutura**: `frontend/src/` com subpastas: `components/`, `views/`, `stores/`, `lib/`, `router/`, `config/`
- **UI**: TailwindCSS + **shadcn-vue** (tabela, dialog, drawer, input, select, tabs, date picker, toast)
- **Estado**: Pinia (stores por domínio dentro de `stores/`)
- **Data viz**: `echarts-for-vue` (recomendado) ou `vue-chartjs`
- **Datas/Moedas**: Day.js (TZ America/Bahia) + Intl.NumberFormat(`pt-BR`, { currency: 'BRL' })
- **Forms & Validação**: `vee-validate` + `zod`
- **Uploads**: Componente em `components/finance/UploadField.vue` com preview; hooks para OCR/ASR futuros
- **Backend**: PHP 8.2 + Slim Framework + Eloquent ORM (CKO Framework)
- **Banco**: SQLite (desenvolvimento) / MySQL (produção)
- **Infra**: Docker Compose

---

## 2) IA da Aplicação (rotas e navegação)

```
frontend/src/
  ├─ App.vue
  ├─ main.js
  ├─ style.css
  ├─ router/
  │   └─ index.js
  ├─ views/
  │   ├─ Dashboard.vue
  │   ├─ Cashflow.vue
  │   ├─ TradesDay.vue
  │   ├─ Forex.vue
  │   ├─ Swing.vue
  │   ├─ Crypto.vue
  │   ├─ Intake.vue
  │   └─ Settings.vue
  ├─ components/
  │   ├─ ui/ (shadcn-vue components)
  │   └─ finance/
  │       ├─ KpiCard.vue
  │       ├─ DataTable.vue
  │       ├─ DialogForm.vue
  │       ├─ DateRange.vue
  │       ├─ CategoryDot.vue
  │       └─ UploadField.vue
  ├─ stores/
  │   ├─ cashflow.js
  │   ├─ trades.js
  │   └─ holdings.js
  ├─ lib/
  │   ├─ api/ (clientes HTTP)
  │   ├─ utils/ (utilitários)
  │   ├─ charts/
  │   │   ├─ AreaStacked.vue
  │   │   └─ AllocationDonut.vue
  │   └─ mocks/
  │       ├─ cashflow.json
  │       ├─ trades-day.json
  │       ├─ forex.json
  │       ├─ swing.json
  │       └─ crypto.json
  └─ config/
      └─ app.js (constantes, fatores de contrato, etc.)
```

**Rotas** (`router/index.js`): `/`, `/cashflow`, `/trades/day`, `/trades/forex`, `/investments/swing`, `/crypto`, `/intake`, `/settings`

**Header**: busca global + seletor de período (7d/30d/custom) + botão “Lançar” **Sidebar**: seções principais com estado ativo

---

## 3) Telas, Componentes e Colunas

### 3.1 Dashboard `/`

- **KPIs**: Receitas, Despesas, Saldo (período), PnL Day Trade, PnL Forex, Dividendos, Patrimônio (Swing + Cripto)
- **Gráficos**: Área empilhada (performance por categoria) e Donut (alocação Swing x Cripto)
- **Tabela Recentes**: Data, Tipo, Categoria, Fonte, Valor

**Componentes**: `KpiCard`, `AreaStacked`, `AllocationDonut`, `DataTable`

---

### 3.2 Cashflow `/cashflow`

**Filtros**: Período, Tipo (receita/despesa), Categoria, Texto (fonte/notas)

**Tabela** (shadcn Table):

- **Colunas**:
  1. **Data/Hora** (sortable)
  2. **Tipo** (badge: receita/despesa)
  3. **Categoria** (dot + nome)
  4. **Fonte** (texto livre)
  5. **Valor (R\$)** (alinhado à direita; sinal conforme tipo)
  6. **Anexos** (ícone + contador)
  7. **Ações** (editar/excluir)

**Dialog “Lançar Transação”**: Tipo, Categoria, Fonte, Valor, Data/Hora, Notas, Anexos

**Componentes**: `DataTable`, `DialogForm`, `CategoryDot`, `UploadField`

---

### 3.3 Day Trade `/trades/day`

**Filtros**: Período, Corretora, Mercado, Símbolo, Estratégia, Status (aberta/fechada)

**Tabela**:

- **Colunas**:
  1. **Abertura** (datetime)
  2. **Símbolo**
  3. **Lado** (buy/sell)
  4. **Qtd** (contratos)
  5. **Preço Entrada**
  6. **Preço Saída**
  7. **Fees**
  8. **PnL (R\$)** (calculado) — fórmula: `(precoSaida - precoEntrada) * qtd * fatorContrato - fees`
  9. **Estratégia**
  10. **Duração** (humanizada)
  11. **Ações**

**Importação em lote**: CSV/JSON (Profit, MT5) com tela de mapeamento de colunas.

**Drawer “Detalhe”**: observações, tags de risco.

**Obs.**: **fatorContrato** por símbolo definido em `config/app.js` (ex.: `WIN=0.2`, `WDO=10`, ajustar ao seu padrão).

---

### 3.4 Forex `/trades/forex`

**Tabela**:

- **Colunas**:
  1. **Abertura**
  2. **Par**
  3. **Lado**
  4. **Lote**
  5. **Entrada**
  6. **Saída**
  7. **SL**
  8. **TP**
  9. **Swap**
  10. **Comissão**
  11. **Pips** (padrão: `(exit-entry)*10000` ou `*100` p/ JPY)
  12. **PnL (R\$)** (com conversão opcional BRL)
  13. **Duração**

**Componentes**: `DataTable`, `PipsBadge`, `FxCalcPopover`

---

### 3.5 Swing/Long `/investments/swing`

**Cards por posição**: Símbolo, Qtd, PM, Preço Atual, **P/L não realizado (R\$ e %)**, Dividendos no período

**Tabela de Lotes**:

- **Colunas**: Qtd, Preço, Data Compra, Corretora, Notas

**Dialog “Registrar Dividendo/JCP”**: valor, data, observação

---

### 3.6 Cripto `/crypto`

**Tabs por carteira**: Binance | MetaMask

**Tabela por ativo**:

- **Colunas**: Ativo, Quantidade Total, PM (moeda base), Preço Atual, **P/L não realizado (R\$)**, %

**Drawer de Lotes**: qty, preço, moeda, data

---

### 3.7 Intake `/intake`

**Inbox**: cartões com preview (imagem/pdf/áudio), status e confiança (quando IA estiver ligada)

**Fluxo**: upload → (futuro) OCR/ASR → revisão → criar transação

---

## 4) Schemas de Dados (Front) — v0.1

> *Estes typings/JSON guiarão os DTOs da API e tabelas futuras.*

### 4.1 Transação (Cashflow)

```json
{
  "id": "uuid",
  "type": "income | expense",
  "category_id": "string",
  "source": "string",
  "amount": 0,
  "currency": "BRL",
  "occurred_at": "2025-09-07T12:00:00-03:00",
  "notes": "",
  "attachments": [{ "id": "uuid", "kind": "image|audio|pdf", "url": "https://..." }]
}
```

### 4.2 DayTrade

```json
{
  "id": "uuid",
  "broker": "Clear",
  "market": "B3",
  "symbol": "WINV25",
  "side": "buy | sell",
  "qty": 1,
  "entry_price": 0,
  "exit_price": 0,
  "fees": 0,
  "pnl": 0,
  "risk_tag": "A|B|C",
  "opened_at": "2025-09-05T09:30:20-03:00",
  "closed_at": "2025-09-05T09:31:05-03:00",
  "strategy": "Payroll scalp"
}
```

### 4.3 ForexTrade

```json
{
  "id": "uuid",
  "pair": "EURUSD",
  "lot_size": 0.1,
  "side": "buy | sell",
  "entry_price": 0,
  "exit_price": 0,
  "sl": 0,
  "tp": 0,
  "swap": 0,
  "commissions": 0,
  "opened_at": "2025-08-29T04:20:00-03:00",
  "closed_at": "2025-08-29T06:05:00-03:00"
}
```

### 4.4 SwingPosition

```json
{
  "id": "uuid",
  "broker": "NuInvest",
  "symbol": "VALE3",
  "qty": 0,
  "lots": [{ "qty": 0, "price": 0, "bought_at": "2025-08-28T11:30:00-03:00" }],
  "current_price": 0,
  "dividends": [{ "amount": 0, "currency": "BRL", "received_at": "2025-09-02T00:00:00-03:00", "note": "" }]
}
```

### 4.5 CryptoHolding

```json
{
  "id": "uuid",
  "wallet": "Binance | MetaMask",
  "chain": "BSC | ETH",
  "asset": "BTC | ETH | ...",
  "lots": [{ "qty": 0, "price": 0, "currency": "BRL", "bought_at": "2025-08-18T19:00:00-03:00" }],
  "current_price": 0,
  "currency": "BRL"
}
```

### 4.6 Category

```json
{ "id": "uuid", "name": "string", "color": "#hex" }
```

---

## 5) Stores (Pinia) — assinatura e ações

### 5.1 `useCashflowStore`

- **state**: `items: Transaction[]`, `filters`, `isLoading`
- **actions**: `fetch(period, filters)`, `create(dto)`, `update(id, dto)`, `remove(id)`
- **getters**: `incomeTotal`, `expenseTotal`, `balance`, `groupByCategory`, `recent(n)`

### 5.2 `useTradesStore`

- **state**: `day: DayTrade[]`, `forex: ForexTrade[]`, `filters`
- **actions**: `fetchDay`, `fetchForex`, `importDay(csv)`, `create/update/remove`
- **getters**: `pnlDayTotal`, `pnlForexTotal`

### 5.3 `useHoldingsStore`

- **state**: `swing: SwingPosition[]`, `crypto: CryptoHolding[]`
- **actions**: `fetchSwing`, `fetchCrypto`, `addDividend`, `addLot`
- **getters**: `swingEquity`, `cryptoEquity`

---

## 6) Componentes shadcn-vue sugeridos

- **Layout**: `AppSidebar`, `AppHeader`, `AppContent`
- **UI**: `Button`, `Card`, `Table`, `Badge`, `Dialog`, `Drawer`, `Tabs`, `Select`, `Popover`, `DatePicker`, `Input`, `Textarea`, `Toast`
- **Padrões UX**: confirmação ao excluir, atalhos (⌘K p/ busca), clipes de anexo, preview

---

## 7) Mocks & Fixtures (para iniciar já)

- `fixtures/cashflow.json`: 30–60 lançamentos com variedade de categorias
- `fixtures/trades-day.json`: operações do WIN/WDO (com fees e estratégias)
- `fixtures/forex.json`: EURUSD/USDBRL com swap/comissão
- `fixtures/swing.json`: VALE3/PETR4 com lotes + dividendos
- `fixtures/crypto.json`: BTC/ETH em Binance/MetaMask

Carregar fixtures em `onMounted()` dos stores quando `process.dev`.

---

## 8) Contratos de API (preview)

> *Mesmo focando no front, já definimos os endpoints que o back deve cumprir depois.*

```
GET  /api/cashflow?from=...&to=...&type=...&category=...&q=...
POST /api/cashflow
PATCH /api/cashflow/:id
DELETE /api/cashflow/:id

GET  /api/trades/day?from=...&to=...&symbol=...&strategy=...
POST /api/trades/day (ou /import)

GET  /api/trades/forex?from=...&to=...&pair=...
POST /api/trades/forex

GET  /api/investments/swing
POST /api/investments/swing/dividends

GET  /api/crypto
POST /api/crypto/lots

GET  /api/categories
POST /api/categories
```

**Padrões**: ISO 8601 com TZ; respostas paginadas `{ data, meta: { page, per_page, total } }`.

---

## 9) Fluxos-chave

### 9.1 Lançar transação manual

1. Botão “Lançar” → Dialog com formulário
2. Validar → `POST /api/cashflow` → Toast sucesso → atualizar tabela + KPIs

### 9.2 Intake por anexo

1. Upload (áudio/imagem/pdf) → item aparece no `/intake`
2. (Futuro) OCR/ASR → sugestão de categoria/valor
3. Revisar → “Criar transação” → preenche formulário → salvar

### 9.3 Import Day Trade

1. `Importar` → escolher fonte (Profit/MT5)
2. Mapear colunas → preview → salvar em lote → feedback de quantos itens

---

## 10) Estrutura de Arquivos (Vue SPA - CKO Framework)

```
frontend/src/
  App.vue
  main.js
  style.css
  router/
    index.js
  views/
    Dashboard.vue
    Cashflow.vue
    TradesDay.vue
    Forex.vue
    Swing.vue
    Crypto.vue
    Intake.vue
    Settings.vue
  components/
    ui/ (shadcn-vue components)
    finance/
      KpiCard.vue
      DataTable.vue
      DialogForm.vue
      DateRange.vue
      CategoryDot.vue
      UploadField.vue
  stores/
    cashflow.js
    trades.js
    holdings.js
  lib/
    api/ (clientes HTTP)
    utils/ (utilitários)
    charts/
      AreaStacked.vue
      AllocationDonut.vue
    mocks/
      cashflow.json
      trades-day.json
      forex.json
      swing.json
      crypto.json
  config/
    app.js
```

**Snippets base**:

``

```js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
```

``

```js
import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '@/views/Dashboard.vue'

export default createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: Dashboard },
    { path: '/cashflow', component: () => import('@/views/Cashflow.vue') },
    { path: '/trades/day', component: () => import('@/views/TradesDay.vue') },
    { path: '/trades/forex', component: () => import('@/views/Forex.vue') },
    { path: '/investments/swing', component: () => import('@/views/Swing.vue') },
    { path: '/crypto', component: () => import('@/views/Crypto.vue') },
    { path: '/intake', component: () => import('@/views/Intake.vue') },
    { path: '/settings', component: () => import('@/views/Settings.vue') },
  ]
})
```

`` (shell)

```vue
<template>
  <div class="min-h-screen bg-neutral-950 text-white">
    <AppHeader />
    <div class="max-w-7xl mx-auto px-4 py-6 flex gap-4">
      <AppSidebar />
      <main class="flex-1">
        <RouterView />
      </main>
    </div>
  </div>
</template>
<script setup>
import AppHeader from './lib/ui/AppHeader.vue'
import AppSidebar from './lib/ui/AppSidebar.vue'
</script>
```

---

## 11) Checklist de Aceite (MVP Front)

-

---

## 12) Próximos Passos Sugeridos (MVP Frontend)

### Fase 1 - Estrutura Base (MVP)
1. **Implementar estrutura de pastas** conforme CKO Framework
2. **Adicionar dependências** necessárias (dayjs, echarts, vee-validate, zod)
3. **Criar stores Pinia** para gerenciamento de estado (cashflow, trades, holdings)
4. **Criar componentes base** (KpiCard, DataTable, DialogForm, DateRange, CategoryDot)
5. **Montar shell do App** (Header/Sidebar) + router com rotas vazias

### Fase 2 - Funcionalidades Core
6. Implementar **Cashflow** (tabela + CRUD no front com fixtures, filtros, paginação)
7. Implementar **Dashboard** (KPIs + gráficos conectados aos stores)
8. Implementar **Day Trade** (lista + import CSV com mapeamento de colunas)
9. Implementar **Forex** (lista + cálculo de pips e PnL)
10. Implementar **Swing** e **Cripto** (cards/tabelas + lotes/dividendos)

### Fase 3 - Funcionalidades Avançadas
11. Implementar **Intake** (upload + review → criar transação)
12. Especificar **contracts REST** definitivos e adapters em `lib/api/*`

### Fase 4 - Backend & Qualidade (Futuro)
13. Implementar **models e controllers** no backend
14. Implementar **autenticação** JWT
15. Adicionar **validação** de formulários
16. Configurar **uploads** de arquivos
17. Implementar **cache** Redis
18. Adicionar **testes** unitários e de integração

