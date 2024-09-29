<?php

namespace Nexus4812\TestGenerator;

use Dotenv\Dotenv;
use Nexus4812\TestGenerator\Client\ConversationHistory;
use Nexus4812\TestGenerator\Client\GptClient;
use Nexus4812\TestGenerator\Client\PricingCalculator;
use Nexus4812\TestGenerator\Executor\PHPUnitExecutor;
use Nexus4812\TestGenerator\FileSystem\ClassExtractor;
use Nexus4812\TestGenerator\FileSystem\FileLogger;
use Nexus4812\TestGenerator\FileSystem\FileSystem;
use Nexus4812\TestGenerator\FileSystem\Path;
use Nexus4812\TestGenerator\Linter\PHPLinter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Finder\Finder;

class Generator
{
    public function __construct(
        private readonly GptClient $gptClient,
        private readonly PHPUnitExecutor $unitExecutor,
        private readonly FileSystem $fileSystem,
        private readonly PHPLinter $linter,
        private readonly ConsoleOutputInterface $output
    )
    {
    }

    public static function create(string|null $chatGptToken = null): self
    {
        if (is_null($chatGptToken)) {
            Dotenv::createImmutable(Path::getProjectRootPath())->load();
            $chatGptToken = $_ENV['CHAT_GPT_TOKEN'];
        }

        return new static(
            new GptClient(
                \OpenAI::client($chatGptToken),
                new PricingCalculator(),
                new FileLogger(null),
                new ConversationHistory()
            ),
            new PHPUnitExecutor(),
            new FileSystem(),
            new PHPLinter(),
            new ConsoleOutput()
        );
    }

    public function generateByFinder(Finder $finder): void
    {
        $this->generateMultiple((new ClassExtractor())->getAllClassesFromDirectory($finder));
    }

    public function generateMultiple(array $classNames): void
    {
        $progress = new ProgressBar($this->output, );
        $progress->start(count($classNames));
        foreach ($classNames as $className) {
            $this->generate($className);
            $progress->advance();
        }

        $progress->finish();
    }

    public function generate(
        string $className
    ): void
    {
        // phpのチェック
        $this->output->writeln("Create a unit test for class " . $className);
        $code = $this->fileSystem->getCodeByClass($className);
        $this->linter->lintPHPCodeOrFail($code);

        // テストコードの生成とPHPの構文チェック
        $testCode = $this->gptClient->generateTest($code);
        $this->linter->lintPHPCodeOrFail($testCode);

        // ファイルをtests配下に保存して、PHPUnitを実行する
        $path = $this->fileSystem->saveTestToFile($testCode, $className);
        $result = $this->unitExecutor->executeTest($path);

        if (is_string($result)) {
            $result = $this->retryGenerate($result, $className, 2);
        }

        if (is_string($result)) {
            $this->retryReduceTest($result, $className, 2);
        }

        $this->gptClient->resetTalk();
        $this->output->writeln("Complete: Created a unit test for class " . $className);
    }

    private function retryReduceTest(string $errorReport, string $className, int $numOfRetry = 1): string|true
    {
        if (0 >= $numOfRetry) {
            $this->output->writeln("Reduce failed unit test was failed");
            return $errorReport;
        }
        $this->output->writeln("Reduce failed unit test");

        $generateTestCode = $this->gptClient->reduceFailedTest($errorReport);
        $path = $this->fileSystem->saveTestToFile($generateTestCode, $className);
        $result = $this->unitExecutor->executeTest($path);
        if ($result === true) {
            $this->output->writeln("Reduce failed unit test was success");
            return true;
        }

        return $this->retryReduceTest($result, $className, $numOfRetry - 1);
    }

    private function retryGenerate(string $errorReport, string $className, int $numOfRetry = 1): string|true
    {
        if (0 >= $numOfRetry) {
            $this->output->writeln("Regenerate failed unit test was failed");
            return $errorReport;
        }

        $generateTestCode = $this->gptClient->regenerateTest($errorReport);
        $path = $this->fileSystem->saveTestToFile($generateTestCode, $className);
        $result = $this->unitExecutor->executeTest($path);
        if ($result === true) {
            $this->output->writeln("Regenerate unit test was success");
            return true;
        }

        return $this->retryGenerate($result, $className, $numOfRetry - 1);
    }
}

