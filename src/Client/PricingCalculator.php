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

    // 保持するトークン数とモデルのリスト
    protected array $tokenRecords = [];

    /**
     * モデルとトークン数（入力と出力）を追加
     *
     * @param string $modelName
     * @param int $inputTokens
     * @param int $outputTokens
     */
    public function addTokens(string $modelName, int $inputTokens, int $outputTokens): void
    {
        if (!isset(self::PRICING[$modelName])) {
            throw new \InvalidArgumentException("Model $modelName is not supported.");
        }

        // モデルごとのトークン数と料金を計算
        $this->tokenRecords[] = [
            'model' => $modelName,
            'inputTokens' => $inputTokens,
            'outputTokens' => $outputTokens,
            'inputCost' => ($inputTokens / 1000000) * self::PRICING[$modelName]['input'],
            'outputCost' => ($outputTokens / 1000000) * self::PRICING[$modelName]['output']
        ];
    }

    /**
     * 合計金額を計算
     *
     * @return float
     */
    public function calculateTotalCost(): float
    {
        $totalCost = 0.0;

        foreach ($this->tokenRecords as $record) {
            $totalCost += $record['inputCost'] + $record['outputCost'];
        }

        return round($totalCost, 4); // 小数点以下4桁で丸める
    }

    /**
     * 追加されたトークン数と料金の一覧を取得
     *
     * @return array
     */
    public function getTokenRecords(): array
    {
        return $this->tokenRecords;
    }
}
