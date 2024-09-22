<?php

namespace Nexus4812\TestGenerator\FileSystem;

use ReflectionClass;

class Path
{
    public static function getFilePathByClassName(string $className): string
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

    public static function getProjectRootPath(): string
    {
        $reflection = new ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return dirname($reflection->getFileName(), 3);
    }
}
