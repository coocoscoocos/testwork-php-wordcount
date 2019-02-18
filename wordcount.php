<?php

use Coocos\Wordcounter\WordCount;

require __DIR__ . '/vendor/autoload.php';

function printUsage()
{
    echo "Usage:\n\n";
    echo "   php wordcount.php <source_file> <result_file>\n\n";
    exit(-1);
}

if ($argc !== 3) {
    printUsage();
}

$sourceFilename = $argv[1];
$resultFilename = $argv[2];

try {
    WordCount::countWordsFromFile($sourceFilename, $resultFilename);
} catch (\Exception $e) {
    echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
    exit(-1);
}
