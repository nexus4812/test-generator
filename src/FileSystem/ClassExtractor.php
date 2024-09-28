<?php

namespace Nexus4812\TestGenerator\FileSystem;

use Symfony\Component\Finder\Finder;

class ClassExtractor
{
    /**
     * Finderインスタンスを受け取って、その結果からクラスを抽出する
     */
    public function getAllClassesFromDirectory(Finder $finder): array
    {
        $classes = [];

        foreach ($finder as $file) {
            $classes[] = $this->getClassesFromFile($file->getRealPath());
        }

        return $classes;
    }

    /**
     * ファイルからクラスと名前空間をトークンで解析し抽出
     */
    private function getClassesFromFile(string $filePath): string|null
    {
        $file = file_get_contents($filePath);

        if (!str_starts_with($file, '<?php')) {
            return null;
        }
        // namespaceを正規表現で抽出
        $namespace = '';
        if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+);/', $file, $matches)) {
            $namespace = $matches[1];
        }

        // クラス名を正規表現で抽出
        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $file, $matches)) {
            $className = $matches[1];
            return $namespace ? "$namespace\\$className" : $className;
        }

        return null;
    }
}
