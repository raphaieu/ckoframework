<?php

namespace CkoFramework\AI\MCP;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * MCP Client for communicating with MCP servers
 * 
 * This class handles communication with Model Context Protocol servers
 */
class Client
{
    private GuzzleClient $httpClient;
    private string $baseUrl;

    public function __construct(string $host, int $port, string $path = '/mcp')
    {
        $this->baseUrl = "http://{$host}:{$port}{$path}";
        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Call a tool on the MCP server
     */
    public function callTool(string $toolName, string $method, array $parameters = []): mixed
    {
        try {
            $response = $this->httpClient->post('/tools/call', [
                'json' => [
                    'tool' => $toolName,
                    'method' => $method,
                    'parameters' => $parameters
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['error'])) {
                throw new \Exception("MCP Error: " . $data['error']['message']);
            }

            return $data['result'] ?? null;

        } catch (GuzzleException $e) {
            throw new \Exception("Failed to call MCP tool: " . $e->getMessage());
        }
    }

    /**
     * List available tools from MCP server
     */
    public function listTools(): array
    {
        try {
            $response = $this->httpClient->get('/tools/list');
            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data['tools'] ?? [];

        } catch (GuzzleException $e) {
            throw new \Exception("Failed to list MCP tools: " . $e->getMessage());
        }
    }

    /**
     * Get tool information
     */
    public function getToolInfo(string $toolName): array
    {
        try {
            $response = $this->httpClient->get("/tools/{$toolName}");
            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data;

        } catch (GuzzleException $e) {
            throw new \Exception("Failed to get tool info: " . $e->getMessage());
        }
    }

    /**
     * Check if MCP server is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = $this->httpClient->get('/health');
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
