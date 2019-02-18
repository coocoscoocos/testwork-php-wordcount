<?php

namespace Tests\Coocos\Wordcounter;

use Coocos\Wordcounter\WordCount;

class WordCounterTest extends BaseTest
{
    function testUniqueUpperLowerCase()
    {
        $tokens = ['мама', 'мыла', 'раму'];
        file_put_contents(self::INPUT_FILE, 'Мама мЫла раму');
        WordCount::countWordsFromFile(self::INPUT_FILE, self::RESULT_FILE);
        $lines = file(self::RESULT_FILE);
        foreach ($lines as $line) {
            list($token, $count) = explode(': ', trim($line));
            $this->assertEquals('1', $count);
            $key = array_search($token, $tokens);
            $this->assertNotFalse($key);
            unset($tokens[$key]);
        }
        $this->assertTrue(empty($tokens));

    }
}