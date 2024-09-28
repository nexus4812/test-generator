<?php

namespace Nexus4812\TestGenerator\Client;

class PricingCalculator
{
    // モデルごとの入力と出力の料金を1M（1,000,000）トークンあたりの価格(米ドル)として定義
    const PRICING = [
        'gpt-3.5-turbo' => [
            'input' => 0.5,
            'output' => 1.5
        ],
        'gpt-4-turbo' => [
            'input' => 10,
            'output' => 30
        ],
    ];

    /**
     * モデルとトークン数（入力と出力）を追加
     *
     * @param string $modelName
     * @param int $inputTokens
     * @param int $outputTokens
     * @return float
     */
    public function calculateCost(string $modelName, int $inputTokens, int $outputTokens): float
    {
        if (!isset(self::PRICING[$modelName])) {
            throw new \InvalidArgumentException("Model $modelName is not supported.");
        }

        $input = ($inputTokens / 1000000) * self::PRICING[$modelName]['input'];
        $output = ($outputTokens / 1000000) * self::PRICING[$modelName]['output'];

        return round($input + $output, 4);
    }
}
