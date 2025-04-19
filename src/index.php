<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Parser\Converter\JsonToExcelConverter;
use Parser\Http\HttpClient;
use Parser\Model\Site;
use Parser\Parser\HtmlParser;
use Parser\Reader\CsvReader;
use Parser\Scanner\SiteScanner;

// Проверяем наличие аргумента командной строки
if ($argc < 2) {
    echo "Использование: php " . basename(__FILE__) . " <имя_файла.csv>\n";
    echo "Пример: php " . basename(__FILE__) . " sites.csv\n";
    exit(1);
}

$inputFile = $argv[1];

// Проверяем существование файла
if (!file_exists($inputFile)) {
    echo "Ошибка: Файл '$inputFile' не найден.\n";
    exit(1);
}

try {
    $csvReader = new CsvReader($inputFile);
    $sitesData = $csvReader->read();
    
    $httpClient = new HttpClient();
    $parser = new HtmlParser();
    $scanner = new SiteScanner($httpClient, $parser);
    
    $results = [];
    
    foreach ($sitesData as $siteData) {
        echo "=SITE=",$siteData['domain'],"\n";
        $site = new Site($siteData['domain'], $siteData['id']);
        $url = 'https://' . $site->getDomain();
        $scanner->scan($url, $site);
        $results[$siteData['domain']] = $site->toArray();
        // Отработка доменов предыдущего уровня, если они есть
        $parentDomain = $scanner->getParentDomain($siteData['domain']);
        if ($parentDomain && $parentDomain != $siteData['domain']) {
            $url = 'https://' . $parentDomain;
            $psite = new Site($parentDomain, $siteData['id']);
            $scanner->scan($url, $psite);
            $results[$parentDomain] = $psite->toArray();
        }
        // Отработка доменов редиректа предыдущего уровня, если они есть
        $redirectDomain = $site->getRedirectDomain();
        if ($redirectDomain) {
            $parentDomain = $scanner->getParentDomain($redirectDomain);
            if ($parentDomain && $parentDomain != $redirectDomain) {
                $url = 'https://' . $parentDomain;
                $psite = new Site($parentDomain, $siteData['id']);
                $scanner->scan($url, $psite);
                $results[$parentDomain] = $psite->toArray();
            }
        }
    }
    var_dump($results);
    // Сохраняем результаты в JSON
    $jsonFile = 'object.json';
    file_put_contents($jsonFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "=1=";
    // Конвертируем в Excel
    $excelFile = $inputFile . '.xlsx';
    $converter = new JsonToExcelConverter();
    $converter->convert($jsonFile, $excelFile);
    echo "Сканирование завершено.\n";
    echo "Результаты сохранены в:\n";
    echo "- JSON: $jsonFile\n";
    echo "- Excel: $excelFile\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
} 