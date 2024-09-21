<?php

namespace Nexus4812\TestGenerator\FileSystem;

use ReflectionClass;

class FileSystem
{
    private readonly string $projectRoot;

    public function __construct(string|null $projectRoot = null)
    {
        $this->projectRoot = $projectRoot ?? self::getProjectRootPath();
    }

    public static function getProjectRootPath(): string
    {
        $reflection = new ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return dirname($reflection->getFileName(), 3);
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

    private function getClassFilePath(string $className): string
    {
        try {
            // ReflectionClassを使ってクラス情報を取得
            $reflector = new \ReflectionClass($className);
            // クラスが定義されているファイルのパスを取得
            $filePath = $reflector->getFileName();

            if ($filePath === false) {
                throw new \RuntimeException("Class file not found for: $className");
            }

            return realpath($filePath);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException("Class not found: $className", 0, $e);
        }
    }

    public function getCodeByClass(string $className): string
    {
        $result = file_get_contents($this->getClassFilePath($className));
        if ($result === false) {
            throw new \RuntimeException('file_get_contents is failed');
        }

        return $result;
    }
}
