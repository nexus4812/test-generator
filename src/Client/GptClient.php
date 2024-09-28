<?php

namespace Nexus4812\TestGenerator\Client;

use Nexus4812\TestGenerator\FileSystem\FileLogger;
use OpenAI\Client;

readonly class GptClient
{
    private const MODEL = 'gpt-3.5-turbo';

    private const SYSTEM_MESSAGE = [
        'role' => 'system',
        'content' =>
            'You are an expert PHP developer. ' .
            'Generate PHPUnit test cases for the provided PHP code. ' .
            'Return only valid PHP code in plain text, without any markdown or code block formatting. ' .
            'The namespace of the test code must match the provided PHP code' .
            'Use Mockery when using Mock.' .
            'If you are using classes or functions that depend on external resources, such as shell_exec, file_get_contents, PDO, etc., and are difficult to unit test, you do not need to implement tests.' .
            'The output must always start with `<?php` '
    ];

    public function __construct(
        private Client            $client,
        private PricingCalculator $pricingCalculator,
        private FileLogger        $logger,
    )
    {
    }

    public function generateTest(string $phpCode): string
    {
        // ChatGPTにPHPコードに基づいたテストケース生成を依頼
        return $this->send("Here is a PHP code snippet. Please generate a PHPUnit test that covers all possible branches and ensures 100% coverage:\n\n$phpCode");
    }

    public function regenerateTest(string $phpCode, string $unitTestCode, string $errorReport): string
    {
        // ChatGPTにエラーレポートを基に再生成を依頼
        return $this->send("The following PHPUnit test failed. Here is the original PHP code:\n\n$phpCode\n\n, Here is the PHPUnit test code: \n\n$unitTestCode\n\n Here is the PHPUnit error report:\n\n$errorReport\n\nPlease regenerate the PHPUnit test with corrections to cover all branches and ensure 100% coverage.");
    }

    public function reduceFailedTest(string $phpCode, string $unitTestCode, string $errorReport): string
    {
        return $this->send("The following PHPUnit test failed. Here is the original PHP code:\n\n$phpCode\n\n, Here is the PHPUnit test code: \n\n$unitTestCode\n\n Here is the PHPUnit error report:\n\n$errorReport\n\n" .
            "Comment out the failing test cases in this test case and regenerate them so that the unit tests can be completed successfully");
    }

    private function send(string $contentMessage): string
    {
        // ChatGPTにエラーレポートを基に再生成を依頼
        $response = $this->client->chat()->create([
            'model' => self::MODEL,
            'messages' => [
                self::SYSTEM_MESSAGE,
                [
                    'role' => 'user',
                    'content' => $contentMessage
                ]
            ],
        ]);

        $cost = $this->pricingCalculator->calculateCost(
            self::MODEL,
            $response['usage']['prompt_tokens'],
            $response['usage']['completion_tokens'],
        );

        $response = $response['choices'][0]['message']['content'];

        $this->logger->logRequestAndResponse(
            $cost,
            $contentMessage,
            $response
        );

        return $response;
    }
}
