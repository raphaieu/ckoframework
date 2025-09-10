import dayjs from 'dayjs'
import 'dayjs/locale/pt-br'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.locale('pt-br')
dayjs.tz.setDefault('America/Bahia')

export const formatCurrency = (value, prefix = '', currency = 'BRL') => {
  if (value === null || value === undefined) return 'R$ 0,00'
  
  const formatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
  
  const formatted = formatter.format(value)
  return prefix ? `${prefix} ${formatted}` : formatted
}

export const formatNumber = (value, decimals = 2) => {
  if (value === null || value === undefined) return '0,00'
  
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  }).format(value)
}

export const formatDate = (date) => {
  if (!date) return ''
  
  return dayjs(date).tz().format('DD/MM/YYYY')
}

export const formatDateTime = (date) => {
  if (!date) return ''
  
  return dayjs(date).tz().format('DD/MM/YYYY HH:mm')
}

export const formatPercent = (value) => {
  if (value === null || value === undefined) return '0,00%'
  
  return new Intl.NumberFormat('pt-BR', {
    style: 'percent',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value / 100)
}
