<?php

namespace CkoFramework\AI\Core;

/**
 * Configuration class for AI-Kit
 * 
 * Handles configuration loading from environment variables and defaults
 */
class Config
{
    private array $config = [];

    public function __construct()
    {
        $this->loadFromEnv();
    }

    /**
     * Load configuration from environment variables
     */
    private function loadFromEnv(): void
    {
        $this->config = [
            'ai' => [
                'provider' => $_ENV['AI_PROVIDER'] ?? 'openai',
                'model' => $_ENV['AI_MODEL'] ?? 'gpt-4',
                'api_key' => $_ENV['AI_API_KEY'] ?? '',
            ],
            'mcp' => [
                'host' => $_ENV['MCP_SERVER_HOST'] ?? 'localhost',
                'port' => (int) ($_ENV['MCP_SERVER_PORT'] ?? 3001),
                'path' => $_ENV['MCP_SERVER_PATH'] ?? '/mcp',
            ],
            'neuron' => [
                'key' => $_ENV['NEURON_AI_KEY'] ?? '',
                'provider' => $_ENV['NEURON_AI_PROVIDER'] ?? 'openai',
            ]
        ];
    }

    /**
     * Get configuration value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set configuration value
     */
    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Get all configuration
     */
    public function all(): array
    {
        return $this->config;
    }
}
