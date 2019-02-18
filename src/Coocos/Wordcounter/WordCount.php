<?php

namespace Coocos\Wordcounter;


use Coocos\Wordcounter\Exception\BigTokenException;
use Coocos\Wordcounter\Exception\FileCreateException;
use Coocos\Wordcounter\Exception\FileNotFoundException;

class WordCount
{

    /**
     * @param $sourceFilename
     * @param $resultFilename
     *
     * @throws FileNotFoundException не найден файл с исходными данными.
     * @throws FileCreateException ошибка создания файла с результатом.
     * @throws BigTokenException при считывании обнаружен токен, длина которого превышает максимально допустимую.
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function countWordsFromFile($sourceFilename, $resultFilename)
    {
        $wordReader = FileTokenReader::openFile($sourceFilename);
        $wordCounter = new TokenCounter();
        while ($wordReader->hasNext()) {
            $token = $wordReader->getToken();
            $word = mb_strtolower($token);
            $wordCounter->addToken($word);
        }
        $wordCounter->saveResultFile($resultFilename);
    }

}