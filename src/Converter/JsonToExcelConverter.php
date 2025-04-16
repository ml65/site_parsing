<?php

namespace Parser\Converter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JsonToExcelConverter
{
    private const COLUMNS = [
        'A' => 'ID',
        'B' => 'Домен',
        'C' => 'Редирект',
        'D' => 'Редирект URL',
        'E' => 'Просмотренные URL',
        'F' => 'Телефоны',
        'G' => 'Email',
        'H' => 'Telegram',
        'I' => 'ИНН',
        'J' => 'ОГРН',
        'K' => 'ОГРНИП',
        'L' => 'СПб'
    ];

    /**
     * Конвертирует JSON файл в Excel
     * 
     * @param string $jsonFile Путь к JSON файлу
     * @param string $excelFile Путь для сохранения Excel файла
     * @return void
     */
    public function convert(string $jsonFile, string $excelFile): void
    {
        $data = json_decode(file_get_contents($jsonFile), true);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Устанавливаем заголовки
        foreach (self::COLUMNS as $column => $header) {
            $sheet->setCellValue($column . '1', $header);
        }
        
        // Стиль для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . array_key_last(self::COLUMNS) . '1')->applyFromArray($headerStyle);
        
        // Заполняем данные
        $row = 2;
        foreach ($data as $domain => $siteData) {
//            var_dump($siteData); exit;
            $sheet->setCellValue('A' . $row, $siteData['id']);
            $sheet->setCellValue('B' . $row, $domain);
            $sheet->setCellValue('C' . $row, $siteData['redirect_domain'] ?? '');
            $sheet->setCellValue('D' . $row, $siteData['redirect_url'] ?? '');
            $sheet->setCellValue('E' . $row, $this->formatArray2($siteData['visited_urls'] ?? []));
            $sheet->setCellValue('F' . $row, $this->formatArray2($siteData['phones'] ?? []));
            $sheet->setCellValue('G' . $row, $this->formatArray2($siteData['emails'] ?? []));
            $sheet->setCellValue('H' . $row, $this->formatArray2($siteData['telegram'] ?? []));
            $sheet->setCellValue('I' . $row, $this->formatArray($siteData['inn'] ?? []));
            $sheet->setCellValue('J' . $row, $this->formatArray($siteData['ogrn'] ?? []));
            $sheet->setCellValue('K' . $row, $this->formatArray($siteData['ogrnip'] ?? []));
            $sheet->setCellValue('L' . $row, $siteData['spb'] ? 'Да' : 'Нет');
            
            $row++;
        }
        
        // Автоматическая ширина столбцов
        foreach (range('A', array_key_last(self::COLUMNS)) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Стиль для данных
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ];
        
        $sheet->getStyle('A2:' . array_key_last(self::COLUMNS) . ($row - 1))->applyFromArray($dataStyle);
        
        // Автоматическая высота строк
        for ($i = 2; $i <= $row - 1; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }
        
        // Сохраняем файл
        $writer = new Xlsx($spreadsheet);
        $writer->save($excelFile);
    }
    
    /**
     * Форматирует массив в строку для Excel
     * 
     * @param array $data Массив данных
     * @return string Отформатированная строка
     */
    private function formatArray(array $data): string
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = $key . ' (' . $value . ')';
        }
        return implode("\n", $result);
    }

    /**
     * Форматирует массив в строку для Excel
     * 
     * @param array $data Массив данных
     * @return string Отформатированная строка
     */
    private function formatArray2(array $data): string
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = $key . ';';
        }
        return implode("\n", $result);
    }
} 