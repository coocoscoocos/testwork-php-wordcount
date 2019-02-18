<?php

namespace Coocos\Wordcounter;


use Coocos\Wordcounter\Exception\FileCreateException;

class TokenCounter
{
    const DEFAULT_TEMP_FILENAME = __DIR__ . '/../../../tempfile.bin';

    private $maxTokensInBuffer;
    private $tokenCountMap = [];
    private $tempFilename;
    private $tempFile;

    /**
     * TokenCounter constructor.
     * @param int $maxTokensInBuffer
     * @param string $tempFilename
     */
    public function __construct($maxTokensInBuffer = 1000, $tempFilename = self::DEFAULT_TEMP_FILENAME)
    {
        $this->maxTokensInBuffer = $maxTokensInBuffer;
        $this->tempFilename = $tempFilename;
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Посчитать токен.
     *
     * @param string $token
     *
     * @return void
     */
    public function addToken($token)
    {
        if (!isset($this->tokenCountMap[$token])) {
            $this->tokenCountMap[$token] = 1;
        } else {
            $this->tokenCountMap[$token]++;
        }
        if (count($this->tokenCountMap) >= $this->maxTokensInBuffer) {
            $this->flushToTempFile();
        }
    }

    /**
     * Формирование результата и сохранения в файл.
     *
     * @throws \RuntimeException ошибка при работе с временным файлом
     * @throws FileCreateException ошибка создания файла с результатом.
     *
     * @param string $resultFilename Имя файла, который должен быть создан.
     */
    public function saveResultFile($resultFilename)
    {
        if (count($this->tokenCountMap) > 0) {
            $this->flushToTempFile();
        }
        if (file_exists($resultFilename)) {
            throw new FileCreateException($resultFilename);
        }
        if (!@touch($resultFilename)) {
            throw new FileCreateException($resultFilename);
        }
        if (!$this->tempFile) {
            return;
        }
        fseek($this->tempFile, 0);
        $resultFile = @fopen($resultFilename, 'w');
        if (false === $resultFile) {
            throw new FileCreateException($resultFilename);
        }
        while (true) {
            $countBin = fread($this->tempFile, 4);
            if (strlen($countBin) != 4) {
                break;
            }
            $token = trim(fgets($this->tempFile));
            $count = unpack('V', $countBin)[1];
            fwrite($resultFile, "$token: $count\n");
        }
        fclose($resultFile);
    }

    /**
     * @throws \RuntimeException ошибка при работе с временным файлом
     *
     * @return void
     */
    private function flushToTempFile()
    {
        if (count($this->tokenCountMap) == 0) {
            return;
        }
        $this->openTempFile();
        fseek($this->tempFile, 0);
        while (true) {
            if (count($this->tokenCountMap) == 0) {
                fseek($this->tempFile, 0, SEEK_END);
                break;
            }
            $countBin = fread($this->tempFile, 4);
            if (strlen($countBin) != 4) {
                break;
            }
            $offsetStartLine = ftell($this->tempFile);
            $token = trim(fgets($this->tempFile));
            if (isset($this->tokenCountMap[$token])) {
                $count = unpack('V', $countBin)[1];
                $offsetNextLine = ftell($this->tempFile);
                $count += $this->tokenCountMap[$token];
                unset($this->tokenCountMap[$token]);
                fseek($this->tempFile, $offsetStartLine);
                fwrite($this->tempFile, pack('V', $count));
                fseek($this->tempFile, $offsetNextLine);
            }
        }
        foreach ($this->tokenCountMap as $token => $count) {
            fwrite($this->tempFile, pack('V', $count) . $token . PHP_EOL);
        }
        $this->tokenCountMap = [];
    }

    /**
     * @throws \RuntimeException
     *
     * @return void
     */
    private function openTempFile()
    {
        if (!$this->tempFile) {
            if (file_exists($this->tempFilename)) {
                unlink($this->tempFilename);
                $this->tempFilename = true;
            }
            $this->tempFile = @fopen($this->tempFilename, 'c+');
            if (false === $this->tempFile) {
                throw new \RuntimeException('Ошибка при работе с временным файлом');
            }
        }
    }

    /**
     * Освобождение ресурсов. Удаление временных файлов.
     *
     * @return void
     */
    public function close()
    {
        if ($this->tempFile) {
            fclose($this->tempFile);
            unlink($this->tempFilename);
            $this->tempFile = null;
        }
    }
}