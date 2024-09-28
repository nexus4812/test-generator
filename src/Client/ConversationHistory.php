<?php

namespace Nexus4812\TestGenerator\Client;

class ConversationHistory
{
    private array $messages = [];

    public function addMessage(array $message): void
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function initializeWithSystemMessage(array $systemMessage): void
    {
        if (count($this->messages) === 0) {
            $this->messages[] = $systemMessage;
        }
    }

    public function reset(): void
    {
        $this->messages = [];
    }
}

