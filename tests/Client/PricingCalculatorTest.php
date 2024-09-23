<?php

namespace Nexus4812\TestGenerator\Client;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class PricingCalculatorTest extends TestCase
{
    public function testAddTokensWithValidModel()
    {
        $calculator = new PricingCalculator();

        $calculator->addTokens('gpt-3.5-turbo', 500000, 1000000);
        $expected = [
            [
                'model' => 'gpt-3.5-turbo',
                'inputTokens' => 500000,
                'outputTokens' => 1000000,
                'inputCost' => 0.25,
                'outputCost' => 1.5
            ]
        ];

        $this->assertEquals($expected, $calculator->getTokenRecords());
    }

    public function testAddTokensWithInvalidModel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Model gpt-5-turbo is not supported.");

        $calculator = new PricingCalculator();
        $calculator->addTokens('gpt-5-turbo', 1000000, 2000000);
    }

    public function testCalculateTotalCost()
    {
        $calculator = new PricingCalculator();

        $calculator->addTokens('gpt-3.5-turbo', 500000, 1000000);
        $calculator->addTokens('gpt-4-turbo', 2000000, 3000000);
        
        $expectedTotalCost = 0.25 + 1.5 + 20 + 90; // values from previous test and cost calculation in addTokens method
        $roundedTotalCost = round($expectedTotalCost, 4);

        $this->assertEquals($roundedTotalCost, $calculator->calculateTotalCost());
    }
}