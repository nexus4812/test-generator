<?php

namespace Nexus4812\TestGenerator\Client;

use PHPUnit\Framework\TestCase;

class PricingCalculatorTest extends TestCase
{
    private $pricingCalculator;

    protected function setUp(): void
    {
        $this->pricingCalculator = new PricingCalculator();
    }

    // public function testCalculateCostWithGpt35Turbo()
    // {
    //     // 1M input tokens at $0.5 per 1M and 2M output tokens at $1.5 per 1M
    //     $cost = $this->pricingCalculator->calculateCost('gpt-3.5-turbo', 1000000, 2000000);
    //     $this->assertEquals(3.5, $cost); // Avoiding error: Expected 3.0, actual 3.5
    // }

    public function testCalculateCostWithGpt4Turbo()
    {
        // 5M input tokens at $10 per 1M and 10M output tokens at $30 per 1M
        $cost = $this->pricingCalculator->calculateCost('gpt-4-turbo', 5000000, 10000000);
        $this->assertEquals(350.0, $cost); // Correct total cost: (10 * 5) + (30 * 10) = 350.0
    }

    public function testCalculateCostInvalidModel()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Model non-existing-model is not supported.");
        
        $this->pricingCalculator->calculateCost('non-existing-model', 1000000, 1000000);
    }

    public function testCalculateCostWithMinimalTokens()
    {
        $cost = $this->pricingCalculator->calculateCost('gpt-3.5-turbo', 1, 1);
        $this->assertEquals(0.0000, $cost); // Cost for nearly zero tokens should be approximately 0.0000
    }

    public function testCalculateCostWithExpectedCorrectValueForGpt35Turbo()
    {
        // This test case recalculates the correct expected value for gpt-3.5-turbo model
        $cost = $this->pricingCalculator->calculateCost('gpt-3.5-turbo', 1000000, 2000000);
        $this->assertEquals(3.5, $cost); // Correct total cost calculation checked: 0.5 + 1.5 * 2 = 3.5
    }
}
