<?php

namespace Nexus4812\TestGenerator\Executor;

class PHPUnitExecutor
{
    public function executeTest(string $path): true|string
    {
        // PHPUnitコマンドでテストを実行
        $output = shell_exec("vendor/bin/phpunit $path 2>&1");

        // 実行結果を確認して、エラーがあればfalse、正常であればtrueを返す
        if (
            str_contains($output, 'error') ||
            str_contains($output, 'failure') ||
            str_contains($output, 'Failed')
        ) {
            return $output;
        }
        return true;
    }
}
