<?php

namespace Nexus4812\TestGenerator\Client;

use OpenAI\Client;

class GptClient
{
    public function __construct(private readonly Client $client)
    {
    }

    public function generateTest(string $phpCode): string
    {
        // ChatGPTにPHPコードに基づいたテストケース生成を依頼
        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo', // 最新のChatGPTモデルを利用
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert PHP developer. Generate PHPUnit test cases for the provided PHP code. Return only valid PHP code in plain text, without any markdown or code block formatting. Use appropriate namespaces and follow PSR-4 coding standards. The output must always start with `<?php` '],
                ['role' => 'user', 'content' => "Here is a PHP code snippet. Please generate a PSR-4 compliant PHPUnit test that covers all possible branches and ensures 100% coverage:\n\n$phpCode"]
            ],
            'max_tokens' => 1000, // 十分な長さのテストコードを生成するためにトークン数を設定
        ]);

        return $response['choices'][0]['message']['content'];
    }
}
