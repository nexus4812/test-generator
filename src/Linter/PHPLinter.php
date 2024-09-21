<?php

namespace Nexus4812\TestGenerator\Linter;

class PHPLinter
{
    public function lintPHPCode(string $phpCode): bool
    {
        // 一時ファイルを作成して構文チェック
        $tempFile = tempnam(sys_get_temp_dir(), 'php_check_');
        file_put_contents($tempFile, $phpCode);

        // php -lを実行して構文チェック
        $output = shell_exec("php -l " . escapeshellarg($tempFile) . " 2>&1");

        // 一時ファイルを削除
        unlink($tempFile);

        return str_contains($output, 'No syntax errors');
    }
}
