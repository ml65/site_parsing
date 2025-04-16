<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Parser\Converter\JsonToExcelConverter;
use Parser\Http\HttpClient;
use Parser\Model\Site;
use Parser\Parser\HtmlParser;
use Parser\Reader\CsvReader;
use Parser\Scanner\SiteScanner;

try {
    $csvReader = new CsvReader('sites.csv');
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
    }
    //var_dump($results);
    // Сохраняем результаты в JSON
    $jsonFile = 'object.json';
    file_put_contents($jsonFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "=1=";
    // Конвертируем в Excel
    $excelFile = 'results.xlsx';
    $converter = new JsonToExcelConverter();
    echo "=2=";
    $converter->convert($jsonFile, $excelFile);
    echo "=3=";
    echo "Сканирование завершено.\n";
    echo "Результаты сохранены в:\n";
    echo "- JSON: $jsonFile\n";
    echo "- Excel: $excelFile\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
} 