<?php

namespace Nexus4812\TestGenerator\FileSystem;

readonly class FileSystem
{
    private string $projectRoot;

    public function __construct(string|null $projectRoot = null)
    {
        $this->projectRoot = $projectRoot ?? Path::getProjectRootPath();
    }

    public function saveTestToFile(string $testCode, string $className): string
    {
        [$path, $namespace]  = $this->getAutoloadDevSettingByClassName($className);

        $relativeClassName = str_replace($namespace, '', $className);

        $testFilePath = $this->projectRoot . '/' . $path . Path::nameSpaceToPath($relativeClassName) . 'Test.php';

        // testsディレクトリが存在しない場合は作成
        if (!file_exists(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0777, true);
        }

        // テストコードをファイルに書き込む
        file_put_contents($testFilePath, $testCode);

        return $testFilePath;
    }

    public function getCodeByClass(string $className): string
    {
        $result = file_get_contents(Path::getFilePathByClassName($className));
        if ($result === false) {
            throw new \RuntimeException('file_get_contents is failed. argument is ' . $className);
        }

        return $result;
    }

    private function getComposerJsonAsArray(): array
    {
        $result = file_get_contents(Path::getProjectRootPath() . '/composer.json');
        if ($result === false) {
            throw new \RuntimeException('getComposerJsonAsArray is failed');
        }

        return json_decode(json: $result, flags: JSON_OBJECT_AS_ARRAY );
    }

    private function getAutoloadPSR4Directories(): array
    {
        $composer = $this->getComposerJsonAsArray();

        if (!empty($composer['autoload']['psr-4'])) {
            return $composer['autoload']['psr-4'];
        }

        return [];
    }

    private function getAutoloadDevPSR4Directories(): array
    {
        $composer = $this->getComposerJsonAsArray();

        if (!empty($composer['autoload-dev']['psr-4'])) {
            return $composer['autoload-dev']['psr-4'];
        }

        return [];
    }

    private function getAutoloadNameSpaceByClassName(string $className): string
    {
        foreach ($this->getAutoloadPSR4Directories() as $key => $directory) {
            if (str_starts_with($className, $key)) {
                return $key;
            }
        }

        throw new \LogicException($className . ' is not psr4 autoload setting');
    }

    /**
     * @param string $className
     * @return string[]
     */
    private function getAutoloadDevSettingByClassName(string $className): array
    {
        $nameSpace = $this->getAutoloadNameSpaceByClassName($className);

        $autoLoadDev = $this->getAutoloadDevPSR4Directories();

        if (!empty($autoLoadDev[$nameSpace])) {
            return [$autoLoadDev[$nameSpace], $nameSpace];
        }

        throw new \LogicException($className . ' is not psr4 autoload-dev setting');
    }
}
