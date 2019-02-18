<?php

namespace Tests\Coocos\Wordcounter;

use Coocos\Wordcounter\Exception\FileCreateException;
use Coocos\Wordcounter\TokenCounter;

class TokenCounterTest extends BaseTest
{

    function testUniqueWords()
    {
        $tokens = ['мама', 'мыла', 'раму'];
        $tokenCounter = new TokenCounter(1000, self::TEMP_FILE);
        foreach ($tokens as $token) {
            $tokenCounter->addToken($token);
        }
        $tokenCounter->saveResultFile(self::RESULT_FILE);
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

    function testWithoutTokens()
    {
        $tokenCounter = new TokenCounter();
        $tokenCounter->saveResultFile(self::RESULT_FILE);
        $this->assertFileExists(self::RESULT_FILE);
        $content = file_get_contents(self::RESULT_FILE);
        $this->assertEquals('', $content);
    }

    function testBadResultFilename()
    {
        $this->expectException(FileCreateException::class);
        $this->expectExceptionMessage('Ошибка создания файла /proc/BAD_FILENAME');
        $tokenCounter = new TokenCounter();
        $tokenCounter->addToken('abc');
        $tokenCounter->saveResultFile('/proc/BAD_FILENAME');
    }

    function testResultExists()
    {
        $this->expectException(FileCreateException::class);
        @touch(self::RESULT_FILE);
        $tokenCounter = new TokenCounter(1, self::TEMP_FILE);
        $tokenCounter->addToken('abc');
        $tokenCounter->addToken('def');
        $tokenCounter->saveResultFile(self::RESULT_FILE);
    }

    function testRemoveTempFileAfterClose()
    {
        $tokenCounter = new TokenCounter(1, self::TEMP_FILE);
        $tokenCounter->addToken('abc');
        $tokenCounter->addToken('def');
        $this->assertFileExists(self::TEMP_FILE);
        $tokenCounter->close();
        $this->assertFileNotExists(self::TEMP_FILE);
    }
}