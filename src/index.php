<?php

require_once __DIR__ . '/../vendor/autoload.php';

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
        $site = new Site($siteData['domain']);
        $scanner->scan($site);
        $results[$siteData['domain']] = $site->toArray();
    }
    
    file_put_contents('object.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "Сканирование завершено. Результаты сохранены в object.json\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
} 