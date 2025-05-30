# Парсер сайтов организаций

Консольное приложение для сканирования сайтов организаций и сбора информации о них.

## Требования

- PHP 8.1 или выше
- Расширение curl
- Расширение json

## Установка

1. Склонируйте репозиторий
2. Установите зависимости:
```bash
composer install
```

## Использование

1. Создайте файл, например `sites.csv` в корне проекта со следующей структурой:
```csv
id,domain
1,example.com
2,example2.com
```

2. Запустите скрипт:
```bash
php src/index.php <имя файла.csv>
```

3. Результаты сканирования будут сохранены в файл `*.json` и `*.xlsx`

## Структура проекта

- `src/Model/` - Модели данных
- `src/Parser/` - Парсеры контента
- `src/Http/` - HTTP клиент
- `src/Reader/` - Чтение входных данных
- `src/Scanner/` - Сканер сайтов


Для запуска проекта нужно:

1. Создать файл sites.csv с доменами для сканирования
2. Установить зависимости через composer
3. Запустить скрипт: php src/index.php

Результаты сканирования будут сохранены в файл object.json и .

