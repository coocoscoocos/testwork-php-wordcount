<?php

namespace Tests\Coocos\Wordcounter;

use Coocos\Wordcounter\Exception\BigTokenException;
use Coocos\Wordcounter\Exception\FileNotFoundException;
use Coocos\Wordcounter\FileTokenReader;

class FileWordReaderTest extends BaseTest
{

    function testOpenNotExistsFile()
    {
        $this->expectException(FileNotFoundException::class);
        FileTokenReader::openFile('NOT_EXISTS_FILENAME');
    }

    function testEmptyFile()
    {
        $this->createInputFile('');
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertFalse($wordReader->hasNext());
    }

    function testMaxWordSizeArgumentMinValueZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->createInputFile('');
        FileTokenReader::openFile(self::INPUT_FILE, 0);
    }

    function testMaxWordSizeArgumentMinValueNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->createInputFile('');
        FileTokenReader::openFile(self::INPUT_FILE, -100);
    }

    function testMaxWordSize()
    {
        $this->createInputFile(self::generateWord(100));
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertTrue($wordReader->hasNext());
    }

    function testMaxWordSizeCustomValue()
    {
        $this->createInputFile(self::generateWord(1023));
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE, 1024);
        $this->assertTrue($wordReader->hasNext());
    }

    function testMaxWordSizeExceeded()
    {
        $this->expectException(BigTokenException::class);
        $this->createInputFile(self::generateWord(101));
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertTrue($wordReader->hasNext());
    }

    function testBigFileWithoutWords()
    {
        $this->createInputFile(self::generateWord(1024, ' .,?!;\'":]['));
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertFalse($wordReader->hasNext());
    }

    function testWords()
    {
        $this->createInputFile('Мама мыла раму.');
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertTrue($wordReader->hasNext());
        $this->assertEquals('Мама', $wordReader->getToken());
        $this->assertTrue($wordReader->hasNext());
        $this->assertEquals('мыла', $wordReader->getToken());
        $this->assertTrue($wordReader->hasNext());
        $this->assertEquals('раму', $wordReader->getToken());
        $this->assertFalse($wordReader->hasNext());
    }

    function testNumbers()
    {
        $this->createInputFile('123456,789!');
        $wordReader = FileTokenReader::openFile(self::INPUT_FILE);
        $this->assertTrue($wordReader->hasNext());
        $this->assertEquals('123456', $wordReader->getToken());
        $this->assertTrue($wordReader->hasNext());
        $this->assertEquals('789', $wordReader->getToken());
        $this->assertFalse($wordReader->hasNext());
    }
}