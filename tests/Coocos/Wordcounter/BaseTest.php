<?php

namespace Tests\Coocos\Wordcounter;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    const INPUT_FILE = __DIR__ . '/../../../test.txt';
    const RESULT_FILE = __DIR__ . '/../../../result.txt';
    const TEMP_FILE = __DIR__ . '/../../../tempfile';

    function tearDown()
    {
        foreach ([self::INPUT_FILE, self::RESULT_FILE, self::TEMP_FILE] as $filename) {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    static function generateWord($length, $allowedChars = 'a')
    {
        $word = '';
        for ($i = 0 ; $i < $length ; $i++) {
            $word .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
        }
        return $word;
    }

    static function createInputFile($content)
    {
        if (file_exists(self::INPUT_FILE)) {
            unlink(self::INPUT_FILE);
        }
        file_put_contents(self::INPUT_FILE, $content);
    }
}