<?php

use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/vendor/autoload.php';

$finder = new Finder();
$finder = $finder->files()->in(__DIR__. '/src/FileSystem')->name('*.php');

\Nexus4812\TestGenerator\Generator::create()->generateByFinder($finder);
