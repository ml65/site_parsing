<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Путь к каталогу с файлами
$directory = 'tmp/'; // Укажите ваш путь
$outputFile = 'merged_output.xlsx';

// Получаем список всех xlsx файлов
//$files = glob($directory . 'sites_*.xlsx');
$files = glob($directory . 'sites_*.xlsx');

if (empty($files)) {
    die('No XLSX files found in directory');
}

// Создаем новый spreadsheet
$resultSpreadsheet = new Spreadsheet();
$resultSheet = $resultSpreadsheet->getActiveSheet();
$currentRow = 1;

// Флаг для первой строки заголовков
$headersWritten = false;

foreach ($files as $file) {
    // Загружаем текущий файл
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    // Получаем все данные листа
    $data = $sheet->toArray();

    // Если это первый файл - копируем заголовки
    if (!$headersWritten) {
        $resultSheet->fromArray($data[0], null, 'A' . $currentRow);
        $currentRow++;
        $headersWritten = true;
    }
    
    // Копируем все строки кроме заголовка
    for ($i = 1; $i < count($data); $i++) {
        $resultSheet->fromArray($data[$i], null, 'A' . $currentRow);
        $currentRow++;
    }
    
    // Очищаем память
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}

// Сохраняем результат
$writer = IOFactory::createWriter($resultSpreadsheet, 'Xlsx');
$writer->save($outputFile);

echo "Files merged successfully into $outputFile";

// Очищаем память
$resultSpreadsheet->disconnectWorksheets();
unset($resultSpreadsheet);
?>