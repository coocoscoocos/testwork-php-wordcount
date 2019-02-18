<?php

namespace Coocos\Wordcounter;


use Coocos\Wordcounter\Exception\BigTokenException;
use Coocos\Wordcounter\Exception\FileNotFoundException;
use Coocos\Wordcounter\Exception\NoTokenException;

class FileTokenReader implements TokenReaderInterface
{
    const DELIMITERS = " .,?!;'\":][}{)(*^%$#@`\n\t";

    private $maxTokenSize;
    private $file;
    private $isFileClosed = false;
    private $charBuffer = '';
    private $wordBuffer = null;

    private function __construct($file, $maxTokenSize)
    {
        $this->file = $file;
        $this->maxTokenSize = $maxTokenSize;
    }

    /**
     * @param string $filename имя исходного текстового файла
     * @param int $maxWordSize максимально допустимый размер токена, при превышении будет выбрашено исключение в методах:
     *   hasNext или getToken. Для избежания чрезмерного использования памяти.
     *
     * @throws FileNotFoundException Исходный факл не найден
     * @throws \InvalidArgumentException Неподходящее значение аргумента $maxWordSize
     * @throws \RuntimeException Любая другая ошибка при окрытии файла
     *
     * @return FileTokenReader
     */
    static function openFile($filename, $maxWordSize = 100)
    {
        if (!is_file($filename)) {
            throw new FileNotFoundException("Файл $filename не найден");
        }
        if ($maxWordSize < 1) {
            throw new \InvalidArgumentException('Минимально допустимое значение параметра maxWordSize = 1 байт');
        }
        $file = @fopen($filename, 'r');
        if (false === $file) {
            throw new \RuntimeException("Ошибка открытия файла $filename для чтения");
        }
        return new FileTokenReader($file, $maxWordSize);
    }

    /**
     * Проверка токенов, доступных для чтения. В этом методу осуществляется считывание из файла и запись в буфер.
     *
     * @throws BigTokenException
     *
     * @return bool Есть токены доступные для считывания
     */
    function hasNext()
    {
        if (null !== $this->wordBuffer) {
            return true;
        }
        return $this->readNextWordToWordBufer();
    }

    /**
     * Получение токена. Предварительно рекомендуется вызвать метод hasNext.
     *
     * @throws NoTokenException если доступных для чтения токенов
     * @throws BigTokenException
     *
     * @return string
     */
    function getToken()
    {
        if (null == $this->wordBuffer || !$this->hasNext()) {
            throw new NoTokenException('Нет даных доступных для чтения');
        }
        $word = $this->wordBuffer;
        $this->wordBuffer = null;
        return $word;
    }

    /**
     * @throws BigTokenException
     *
     * @return bool
     */
    private function readNextWordToWordBufer()
    {
        while (!$this->isFileClosed) {
            $char = fgetc($this->file);
            if (false === $char) {
                fclose($this->file);
                $this->isFileClosed = true;
                if (strlen($this->charBuffer) > 0) {
                    $this->wordBuffer = $this->charBuffer;
                    $this->charBuffer = '';
                    return true;
                }
                break;
            } elseif (self::isDelimiter($char)) {
                if (strlen($this->charBuffer) > 0) {
                    $this->wordBuffer = $this->charBuffer;
                    $this->charBuffer = '';
                    return true;
                }
            } else {
                $this->charBuffer .= $char;
                if (strlen($this->charBuffer) > $this->maxTokenSize) {
                    throw new BigTokenException;
                }
            }
        }
        return false;
    }

    private static function isDelimiter($char) {
        return false !== strpos(self::DELIMITERS, $char);
    }
}