<?php

namespace CkoFramework\AI\Core;

/**
 * Unified Tool Interface
 * 
 * This interface allows tools to work with both Neuron AI and MCP
 */
interface ToolInterface
{
    /**
     * Get tool name
     */
    public function getName(): string;

    /**
     * Get tool description
     */
    public function getDescription(): string;

    /**
     * Get tool parameters schema
     */
    public function getParameters(): array;

    /**
     * Execute tool with parameters
     */
    public function execute(array $parameters): array;

    /**
     * Get tool metadata
     */
    public function getMetadata(): array;
}
