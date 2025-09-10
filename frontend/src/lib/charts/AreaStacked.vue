<template>
  <div ref="chartRef" class="w-full h-full"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import * as echarts from 'echarts'
import { config } from '@/config/app'

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  height: {
    type: String,
    default: '300px'
  }
})

const chartRef = ref(null)
let chartInstance = null

const initChart = () => {
  if (!chartRef.value) return

  chartInstance = echarts.init(chartRef.value)
  
  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'cross'
      },
      formatter: function(params) {
        let result = `<div style="margin-bottom: 8px;"><strong>${params[0].axisValue}</strong></div>`
        params.forEach(param => {
          const color = param.color
          const value = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
          }).format(param.value)
          result += `<div style="display: flex; align-items: center; margin: 4px 0;">
            <span style="display: inline-block; width: 10px; height: 10px; background-color: ${color}; margin-right: 8px; border-radius: 2px;"></span>
            <span>${param.seriesName}: ${value}</span>
          </div>`
        })
        return result
      }
    },
    legend: {
      data: ['Receitas', 'Despesas'],
      bottom: 0
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '15%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: props.data.map(item => item.name)
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        formatter: function(value) {
          return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(value)
        }
      }
    },
    series: [
      {
        name: 'Receitas',
        type: 'line',
        stack: 'Total',
        areaStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(16, 185, 129, 0.3)' },
            { offset: 1, color: 'rgba(16, 185, 129, 0.1)' }
          ])
        },
        lineStyle: {
          color: '#10b981'
        },
        itemStyle: {
          color: '#10b981'
        },
        data: props.data.map(item => item.income)
      },
      {
        name: 'Despesas',
        type: 'line',
        stack: 'Total',
        areaStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(239, 68, 68, 0.3)' },
            { offset: 1, color: 'rgba(239, 68, 68, 0.1)' }
          ])
        },
        lineStyle: {
          color: '#ef4444'
        },
        itemStyle: {
          color: '#ef4444'
        },
        data: props.data.map(item => -item.expense) // Negativo para mostrar abaixo do zero
      }
    ]
  }

  chartInstance.setOption(option)
}

const updateChart = () => {
  if (!chartInstance) return
  
  const option = {
    xAxis: {
      data: props.data.map(item => item.name)
    },
    series: [
      {
        data: props.data.map(item => item.income)
      },
      {
        data: props.data.map(item => -item.expense)
      }
    ]
  }
  
  chartInstance.setOption(option)
}

const resizeChart = () => {
  if (chartInstance) {
    chartInstance.resize()
  }
}

onMounted(() => {
  initChart()
  window.addEventListener('resize', resizeChart)
})

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.dispose()
  }
  window.removeEventListener('resize', resizeChart)
})

watch(() => props.data, () => {
  updateChart()
}, { deep: true })
</script>
