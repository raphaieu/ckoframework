// Configurações de ambiente
export const config = {
  api: {
    baseUrl: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  },
  app: {
    name: import.meta.env.VITE_APP_NAME || 'CKO Framework',
    version: import.meta.env.VITE_APP_VERSION || '1.0.0',
  },
  dev: {
    mode: import.meta.env.VITE_DEV_MODE === 'true',
  }
}

// Helper para URLs da API
export const apiUrl = (endpoint) => {
  const baseUrl = config.api.baseUrl.replace(/\/$/, '') // Remove trailing slash
  const cleanEndpoint = endpoint.replace(/^\//, '') // Remove leading slash
  return `${baseUrl}/${cleanEndpoint}`
}
