<?php

namespace Coocos\Wordcounter\Exception;


class FileCreateException extends \Exception
{
    public function __construct($filename)
    {
        parent::__construct("Ошибка создания файла $filename");
    }
}