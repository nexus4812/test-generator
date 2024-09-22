<?php

namespace Nexus4812\TestGenerator\Client;

use OpenAI\Client;

readonly class GptClient
{
    private const MODEL = 'gpt-4-turbo';

    private const SYSTEM_MESSAGE = [
        'role' => 'system',
        'content' =>
            'You are an expert PHP developer. ' .
            'Generate PHPUnit test cases for the provided PHP code. ' .
            'Return only valid PHP code in plain text, without any markdown or code block formatting. ' .
            'The namespace of the test code must match the provided PHP code' .
            'Use Mockery when using Mock.' .
            'The output must always start with `<?php` '
    ];

    public function __construct(private Client $client)
    {
    }

    public function generateTest(string $phpCode): string
    {
        // ChatGPTにPHPコードに基づいたテストケース生成を依頼
        $response = $this->client->chat()->create([
            'model' => self::MODEL,
            'messages' => [
                self::SYSTEM_MESSAGE,
                ['role' => 'user', 'content' => "Here is a PHP code snippet. Please generate a PHPUnit test that covers all possible branches and ensures 100% coverage:\n\n$phpCode"]
            ],
            'max_tokens' => 1000, // 十分な長さのテストコードを生成するためにトークン数を設定
        ]);

        return $response['choices'][0]['message']['content'];
    }

    public function regenerateTest(string $phpCode, string $errorReport): string
    {
        // ChatGPTにエラーレポートを基に再生成を依頼
        $response = $this->client->chat()->create([
            'model' => self::MODEL,
            'messages' => [
                self::SYSTEM_MESSAGE,
                ['role' => 'user', 'content' => "The following PHPUnit test failed. Here is the original PHP code:\n\n$phpCode\n\nHere is the PHPUnit error report:\n\n$errorReport\n\nPlease regenerate the PHPUnit test with corrections to cover all branches and ensure 100% coverage."]
            ],
            'max_tokens' => 1000,
        ]);

        return $response['choices'][0]['message']['content'];
    }
}
