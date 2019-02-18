<?php

namespace Coocos\Wordcounter;

use Coocos\Wordcounter\Exception\BigTokenException;
use Coocos\Wordcounter\Exception\NoTokenException;

interface TokenReaderInterface
{

    /**
     * Проверка токенов, доступных для чтения.
     *
     * @throws BigTokenException при считывании обнаружен токен, длина которого превышает максимально допустимую.
     *
     * @return bool Есть токены доступные для считывания
     */
    function hasNext();

    /**
     * Получение токена. Предварительно рекомендуется вызвать метод hasNext.
     *
     * @throws NoTokenException если доступных для чтения токенов
     * @throws BigTokenException
     *
     * @return string
     */
    function getToken();
}