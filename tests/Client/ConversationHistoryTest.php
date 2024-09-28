<?php

namespace Nexus4812\TestGenerator\Client;

use PHPUnit\Framework\TestCase;

class ConversationHistoryTest extends TestCase
{
    public function testAddMessage()
    {
        $conversationHistory = new ConversationHistory();
        $message = ['text' => 'Hello, world!'];
        
        $conversationHistory->addMessage($message);
        
        $this->assertCount(1, $conversationHistory->getMessages());
        $this->assertSame([$message], $conversationHistory->getMessages());
    }
    
    public function testGetMessagesInitiallyEmpty()
    {
        $conversationHistory = new ConversationHistory();
        $this->assertEmpty($conversationHistory->getMessages());
    }
    
    public function testInitializeWithSystemMessageWhenEmpty()
    {
        $conversationHistory = new ConversationHistory();
        $systemMessage = ['text' => 'System initialization message'];
        
        $conversationHistory->initializeWithSystemMessage($systemMessage);
        
        $this->assertCount(1, $conversationHistory->getMessages());
        $this->assertSame([$systemMessage], $conversationHistory->getMessages());
    }
    
    public function testInitializeWithSystemMessageWhenNotEmpty()
    {
        $conversationHistory = new ConversationHistory();
        $firstMessage = ['text' => 'First message'];
        $systemMessage = ['text' => 'System initialization message'];
        
        $conversationHistory->addMessage($firstMessage);
        $conversationHistory->initializeWithSystemMessage($systemMessage);
        
        $this->assertCount(1, $conversationHistory->getMessages());
        $this->assertSame([$firstMessage], $conversationHistory->getMessages());
        $this->assertNotContains($systemMessage, $conversationHistory->getMessages());
    }
    
    public function testReset()
    {
        $conversationHistory = new ConversationHistory();
        $message = ['text' => 'Hello, world!'];
        
        $conversationHistory->addMessage($message);
        $conversationHistory->reset();
        
        $this->assertEmpty($conversationHistory->getMessages());
    }
}