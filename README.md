**Запуск**

`php wordcount source.txt result.txt`

**Ответы на возможные вопросы**

* Использование composer

Сторонние библиотеки composer и phpunit использованы только в целях unit тестирования.

* Странный формат временного файла

Количество упоминайний хранится в 4 байтовом двоичном поле, что дает возможность избегать копирования всего файла и обновлении значений. 
