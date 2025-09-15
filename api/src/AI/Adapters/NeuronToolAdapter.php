<?php

namespace CkoFramework\AI\Adapters;

use CkoFramework\AI\Core\ToolInterface;
use NeuronAI\Tools\ToolInterface as NeuronToolInterface;
use NeuronAI\Tools\ToolCall;
use NeuronAI\Tools\ToolResult;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\ToolPropertyInterface;

/**
 * Neuron AI Tool Adapter
 * 
 * Adapts unified tools to work with Neuron AI tool calling
 */
class NeuronToolAdapter implements NeuronToolInterface
{
    private ToolInterface $tool;

    public function __construct(ToolInterface $tool)
    {
        $this->tool = $tool;
    }

    public function getName(): string
    {
        return $this->tool->getName();
    }

    public function getDescription(): string
    {
        return $this->tool->getDescription();
    }

    public function getProperties(): array
    {
        // Return empty array to avoid compatibility issues
        // The tool will be called directly via execute() method
        return [];
    }

    public function getRequiredProperties(): array
    {
        $parameters = $this->tool->getParameters();
        $required = [];

        foreach ($parameters as $name => $param) {
            if ($param['required'] ?? false) {
                $required[] = $name;
            }
        }

        return $required;
    }

    public function addProperty(ToolPropertyInterface $property): NeuronToolInterface
    {
        // Not needed for this implementation
        return $this;
    }

    public function call(ToolCall $toolCall): ToolResult
    {
        try {
            $parameters = $toolCall->getParameters();
            $result = $this->tool->execute($parameters);

            return new ToolResult(
                content: $result['summary'] ?? $result['response'] ?? 'Analysis completed',
                data: $result['data'] ?? [],
                metadata: array_merge(
                    $result['metadata'] ?? [],
                    [
                        'tool_name' => $this->tool->getName(),
                        'tool_metadata' => $this->tool->getMetadata()
                    ]
                )
            );

        } catch (\Exception $e) {
            return new ToolResult(
                content: "Error executing tool: " . $e->getMessage(),
                data: [],
                metadata: [
                    'error' => true,
                    'message' => $e->getMessage(),
                    'tool_name' => $this->tool->getName()
                ]
            );
        }
    }

    public function setCallable(callable $callable): NeuronToolInterface
    {
        // Not needed for this implementation
        return $this;
    }

    public function getInputs(): array
    {
        return $this->tool->getParameters();
    }

    public function setInputs(array $inputs): NeuronToolInterface
    {
        // Not needed for this implementation
        return $this;
    }

    public function getCallId(): ?string
    {
        return null;
    }

    public function setCallId(?string $callId): NeuronToolInterface
    {
        // Not needed for this implementation
        return $this;
    }

    public function getResult(): string
    {
        return '';
    }

    public function getMaxTries(): int
    {
        return 3;
    }

    public function setMaxTries(int $maxTries): NeuronToolInterface
    {
        // Not needed for this implementation
        return $this;
    }

    public function execute(): void
    {
        // Not needed for this implementation
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'parameters' => $this->getParameters(),
            'metadata' => $this->tool->getMetadata()
        ];
    }
}
