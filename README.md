# test-generator

chatgptを利用して、PHPUnitのテストコードを自動生成させるサンプルです。


## Usage

```bash
# composer経由で依存関係を解決します
comoser i

# .envにトークンを設置します
echo "CHAT_GPT_TOKEN=<YOUR_API_TOKEN>" >> .env

# 実行用のファイルを作成します
touch test-generator.php
```

test-generator.phpは下記のように、プロダクト毎に設定します
```php:test-generator.php
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/vendor/autoload.php';

// symfony/finderで対象の作成したいPathを指定してください
$finder = new Finder();
$finder = $finder->files()->in(__DIR__. '/src/FileSystem')->name('*.php');

// 実行してテストコードを生成します
\Nexus4812\TestGenerator\Generator::create()->generateByFinder($finder);
```

実行するとOpenAIと通信を行い、テストコードの生成を実行します。

```bash
php test-generator.php
```

## 生成結果

実際に生成させたのが下記2つ

[ConversationHistoryTest.php](tests%2FClient%2FConversationHistoryTest.php)
[PricingCalculatorTest.php](tests%2FClient%2FPricingCalculatorTest.php)
