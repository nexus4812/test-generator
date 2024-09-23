<?php

namespace Nexus4812\TestGenerator;

use Nexus4812\TestGenerator\Client\GptClient;
use Nexus4812\TestGenerator\Client\PricingCalculator;
use Nexus4812\TestGenerator\Executor\PHPUnitExecutor;
use Nexus4812\TestGenerator\FileSystem\FileSystem;
use Nexus4812\TestGenerator\Linter\PHPLinter;

class Generator
{
    public function __construct(
        private readonly GptClient $gptClient,
        private readonly PHPUnitExecutor $unitExecutor,
        private readonly FileSystem $fileSystem,
        private readonly PHPLinter $linter,
    )
    {
    }

    public static function create(string $chatGptToken): self
    {
        return new static(
            new GptClient(\OpenAI::client($chatGptToken), new PricingCalculator()),
            new PHPUnitExecutor(),
            new FileSystem(),
            new PHPLinter(),
        );
    }

    public function generate(
        string $className
    ): void
    {
        $code = $this->fileSystem->getCodeByClass($className);

        // php -lのオプションを利用して、PHPの文法エラーになっていないか検証する
        if (!$this->linter->lintPHPCode($code)) {
            throw new \InvalidArgumentException($className . ' is broken code');
        }

        $testCode = $this->gptClient->generateTest($code);

        if (!$this->linter->lintPHPCode($testCode)) {
            throw new \RuntimeException("Test code is broken that chat gpt generated >> " . $testCode);
        }

        // ファイルをtests配下に保存して、PHPUnitを実行する
        $path = $this->fileSystem->saveTestToFile($testCode, $className);
        $result = $this->unitExecutor->executeTest($path);

        if (is_string($result)) {
            $this->retryGenerate($result, $className, 3);
        }
    }

    private function retryGenerate(string $errorReport, string $className, int $numOfMaxRetry = 0): string|true
    {
        $code = $this->fileSystem->getCodeByClass($className);
        $testCode = $this->fileSystem->getCodeByClass($className . 'Test');
        $generateTestCode = $this->gptClient->regenerateTest($code, $testCode, $errorReport);
        $path = $this->fileSystem->saveTestToFile($generateTestCode, $className);
        $result = $this->unitExecutor->executeTest($path);
        if ($result === true) {
            var_dump("retry is success");
            return true;
        }

        if (0 >= $numOfMaxRetry) {
            return $result;
        }

        return $this->retryGenerate($result, $className, $numOfMaxRetry - 1);
    }
}

