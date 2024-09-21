<?php

namespace Nexus4812\TestGenerator\Executor;

class PHPUnitExecutor
{
    public function executeTest(string $className): bool
    {
        // PHPUnitコマンドでテストを実行
        $output = shell_exec("vendor/bin/phpunit --filter $className 2>&1");

        // 実行結果を確認して、エラーがあればfalse、正常であればtrueを返す
        if (str_contains($output, 'error') || str_contains($output, 'failure')) {
            echo "Test execution failed: \n" . $output;
            return false;
        }

        echo "Test executed successfully.\n";
        return true;
    }
}
