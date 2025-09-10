<template>
  <div ref="chartRef" class="w-full h-full"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import * as echarts from 'echarts'

const props = defineProps({
  data: {
    type: Array,
    required: true
  }
})

const chartRef = ref(null)
let chartInstance = null

const initChart = () => {
  if (!chartRef.value) return

  chartInstance = echarts.init(chartRef.value)
  
  const total = props.data.reduce((sum, item) => sum + item.value, 0)
  
  const option = {
    tooltip: {
      trigger: 'item',
      formatter: function(params) {
        const percentage = ((params.value / total) * 100).toFixed(1)
        const value = new Intl.NumberFormat('pt-BR', {
          style: 'currency',
          currency: 'BRL'
        }).format(params.value)
        return `
          <div style="margin-bottom: 8px;"><strong>${params.name}</strong></div>
          <div>Valor: ${value}</div>
          <div>Percentual: ${percentage}%</div>
        `
      }
    },
    legend: {
      orient: 'vertical',
      left: 'left',
      top: 'center',
      formatter: function(name) {
        const item = props.data.find(d => d.name === name)
        if (!item) return name
        const percentage = ((item.value / total) * 100).toFixed(1)
        return `${name} (${percentage}%)`
      }
    },
    series: [
      {
        name: 'Alocação',
        type: 'pie',
        radius: ['40%', '70%'],
        center: ['60%', '50%'],
        avoidLabelOverlap: false,
        itemStyle: {
          borderRadius: 8,
          borderColor: '#fff',
          borderWidth: 2
        },
        label: {
          show: false,
          position: 'center'
        },
        emphasis: {
          label: {
            show: true,
            fontSize: '18',
            fontWeight: 'bold',
            formatter: function(params) {
              const percentage = ((params.value / total) * 100).toFixed(1)
              return `${percentage}%`
            }
          }
        },
        labelLine: {
          show: false
        },
        data: props.data.map(item => ({
          value: item.value,
          name: item.name,
          itemStyle: {
            color: item.color
          }
        }))
      }
    ]
  }

  chartInstance.setOption(option)
}

const updateChart = () => {
  if (!chartInstance) return
  
  const total = props.data.reduce((sum, item) => sum + item.value, 0)
  
  const option = {
    legend: {
      formatter: function(name) {
        const item = props.data.find(d => d.name === name)
        if (!item) return name
        const percentage = ((item.value / total) * 100).toFixed(1)
        return `${name} (${percentage}%)`
      }
    },
    series: [{
      data: props.data.map(item => ({
        value: item.value,
        name: item.name,
        itemStyle: {
          color: item.color
        }
      }))
    }]
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
