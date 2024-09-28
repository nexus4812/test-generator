<?php

namespace Nexus4812\TestGenerator\Client;

use PHPUnit\Framework\TestCase;

class ConversationHistoryTest extends TestCase
{
    public function testAddMessage()
    {
        $history = new ConversationHistory();
        $this->assertEmpty($history->getMessages());

        $message = ['type' => 'user', 'text' => 'Hello there!'];
        $history->addMessage($message);
        $this->assertCount(1, $history->getMessages());
        $this->assertEquals([$message], $history->getMessages());
    }

    public function testGetMessagesInitiallyEmpty()
    {
        $history = new ConversationHistory();
        $this->assertEmpty($history->getMessages());
    }

    public function testInitializeWithSystemMessageAddsMessageIfEmpty()
    {
        $history = new ConversationHistory();
        $systemMessage = ['type' => 'system', 'text' => 'Welcome!'];

        $history->initializeWithSystemMessage($systemMessage);
        $this->assertEquals([$systemMessage], $history->getMessages());
    }

    public function testInitializeWithSystemMessageDoesNotAddIfNotEmpty()
    {
        $history = new ConversationHistory();
        $firstMessage = ['type' => 'user', 'text' => 'Hi!'];
        $systemMessage = ['type' => 'system', 'text' => 'Welcome!'];

        $history->addMessage($firstMessage);
        $history->initializeWithSystemMessage($systemMessage);

        $this->assertEquals([$firstMessage], $history->getMessages());
    }

    public function testResetClearsMessages()
    {
        $history = new ConversationHistory();
        $history->addMessage(['type' => 'user', 'text' => 'Hello there!']);

        $this->assertCount(1, $history->getMessages());

        $history->reset();
        $this->assertEmpty($history->getMessages());
    }
}