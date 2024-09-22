<?php

namespace Nexus4812\TestGenerator;

use Nexus4812\TestGenerator\Client\GptClient;
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
            new GptClient(\OpenAI::client($chatGptToken)),
            new PHPUnitExecutor(),
            new FileSystem(),
            new PHPLinter(),
        );
    }

    public function generate(string $className): void
    {
        $code = $this->fileSystem->getCodeByClass($className);

        if (!$this->linter->lintPHPCode($code)) {
            throw new \InvalidArgumentException($className . ' is broken code');
        }

        $testCode = $this->gptClient->generateTest($code);

        if (!$this->linter->lintPHPCode($testCode)) {
            throw new \RuntimeException("Test code is broken that chat gpt generated >> " . $testCode);
        }

        $path = $this->fileSystem->saveTestToFile($testCode, $className);

        $result = $this->unitExecutor->executeTest($path);

        if (is_string($result)) {
            var_dump("execute retry");
            $testCode = $this->gptClient->regenerateTest($code, $result);
            $path = $this->fileSystem->saveTestToFile($testCode, $className);
            $result = $this->unitExecutor->executeTest($path);

            var_dump($result);
        }
    }
}

