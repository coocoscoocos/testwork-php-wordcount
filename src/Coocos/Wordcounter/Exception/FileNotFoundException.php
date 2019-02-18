<?php

namespace Coocos\Wordcounter\Exception;


class FileNotFoundException extends \Exception
{
    public function __construct($filename)
    {
        parent::__construct("Файл $filename не найден");
    }
}