<?php

namespace Nexus4812\TestGenerator\FileSystem;

class FileSystem
{
    private readonly string $projectRoot;

    public function __construct(string|null $projectRoot = null)
    {
        $this->projectRoot = $projectRoot ?? Path::getProjectRootPath();
    }

    public function saveTestToFile(string $testCode, string $className): void
    {
        // PSR-4準拠のテストファイルパスを生成
        $testFilePath = $this->projectRoot . '/tests/' . str_replace('\\', '/', $className) . 'Test.php';

        // testsディレクトリが存在しない場合は作成
        if (!file_exists(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0777, true);
        }

        // テストコードをファイルに書き込む
        file_put_contents($testFilePath, $testCode);
    }

    public function getCodeByClass(string $className): string
    {
        $result = file_get_contents(Path::getFilePathByClassName($className));
        if ($result === false) {
            throw new \RuntimeException('file_get_contents is failed');
        }

        return $result;
    }
}
