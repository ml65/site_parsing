<?php

/**
 * Скрипт для разделения CSV файла на части и параллельной обработки
 */

// Проверяем наличие файла sites.csv
if (!file_exists('sites.csv')) {
    die("Ошибка: Файл sites.csv не найден\n");
}

// Создаем директорию tmp, если её нет
if (!file_exists('tmp')) {
    mkdir('tmp', 0755, true);
}

// Открываем файл для чтения
$file = fopen('sites.csv', 'r');
if (!$file) {
    die("Ошибка: Не удалось открыть файл sites.csv\n");
}

// Читаем первую строку (заголовки)
$header = fgets($file);
if (!$header) {
    fclose($file);
    die("Ошибка: Файл sites.csv пуст\n");
}

// Инициализируем переменные для сплита
$chunkSize = 1000;
$chunkNumber = 1;
$currentChunk = [];
$currentLineCount = 0;

// Читаем файл построчно
while (($line = fgets($file)) !== false) {
    $currentChunk[] = $line;
    $currentLineCount++;
    
    // Если достигли размера чанка, сохраняем его
    if ($currentLineCount >= $chunkSize) {
        saveChunk($currentChunk, $header, $chunkNumber);
        $currentChunk = [];
        $currentLineCount = 0;
        $chunkNumber++;
    }
}

// Сохраняем последний чанк, если он не пустой
if (!empty($currentChunk)) {
    saveChunk($currentChunk, $header, $chunkNumber);
}

fclose($file);

// Запускаем обработку каждого чанка в фоновом режиме
for ($i = 1; $i <= $chunkNumber; $i++) {
    $chunkFile = "tmp/sites_$i.csv";
    if (file_exists($chunkFile)) {
        $command = "php src/index.php $chunkFile > tmp/sites_".$i.".log 2>&1 &";
        exec($command);
        echo "Запущена обработка файла: $chunkFile\n";
    }
}

echo "Все задачи запущены в фоновом режиме\n";

/**
 * Сохраняет чанк данных в отдельный файл
 *
 * @param array $chunk Массив строк для сохранения
 * @param string $header Заголовок CSV
 * @param int $chunkNumber Номер чанка
 */
function saveChunk(array $chunk, string $header, int $chunkNumber): void
{
    $filename = "tmp/sites_$chunkNumber.csv";
    $file = fopen($filename, 'w');
    
    if (!$file) {
        die("Ошибка: Не удалось создать файл $filename\n");
    }
    
    // Записываем заголовок
    fwrite($file, $header);
    
    // Записываем данные
    foreach ($chunk as $line) {
        fwrite($file, $line);
    }
    
    fclose($file);
    echo "Создан файл: $filename\n";
} 