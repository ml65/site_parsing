<?php

namespace Parser\Scanner;

use Parser\Http\HttpClient;
use Parser\Model\Site;
use Parser\Parser\ParserInterface;

class SiteScanner
{
    private HttpClient $httpClient;
    private ParserInterface $parser;
    private const MAX_DEPTH = 1;

    public function __construct(HttpClient $httpClient, ParserInterface $parser)
    {
        $this->httpClient = $httpClient;
        $this->parser = $parser;
    }


    public function scan(string $url, Site $site, int $depth = 0): void
    {
        if ($depth > self::MAX_DEPTH) {
            return;
        }

        echo "=URL=",$url,"\n";
        if ($site->isVisitedUrls($url)) {
            return;
        }
        
        $site->addVisitedUrls($url);
        $response = $this->httpClient->get($url);
        
        if ($response['status'] === 'redirect') {
            $newUrl = $response['redirect_url'];
            $site->setRedirectUrl($newUrl);
            $redirectDomain = parse_url($newUrl, PHP_URL_HOST);
            $site->setRedirectDomain($redirectDomain);
            echo "=REDIRECT=",$redirectDomain," ",$newUrl,"\n";
            $this->scan($newUrl, $site, $depth);
            return;
        }
        
        if ($response['status'] === 'error') {
            return;
        }
        

        // Извлекаем все ссылки из контента
//        preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>/i', $response['content'], $matches);
        preg_match_all('/(https:\/\/[^\s"\']+)/i', $response['content'], $matches);
        
        foreach ($matches[1] as $link) {
            $parsedUrl = parse_url($link);
            echo "=LINK=",$link,"\n";
            if (!isset($parsedUrl['host'])) {
                $parsedUrl['host'] = $site->getDomain();
                $parsedUrl['path'] = $link;
            }
            
            if (($parsedUrl['host'] === $site->getDomain() || $parsedUrl['host'] === $site->getRedirectDomain()) 
                && !strpos($parsedUrl['path'], '#')) {
                $newUrl = 'https://' . $parsedUrl['host'] . ($parsedUrl['path'] ?? '');
                $this->scan($newUrl, $site, $depth + 1);
            } else {
                $this->parser->parse($link, $site, $url);
            }
        }

        if (!$this->parser->parse($response['content'], $site, $url)) {
            // страница защищена? Формируем кукиес и делаем второй запрос
            echo "=Нет информации на странице!!! пытаемся получить куки =",$url,"\n";
            $cookies = $this->httpClient->getCookies($response['content']);
            $response = $this->httpClient->getSecond($url, $cookies);
            $this->parser->parse($response['content'], $site, $url);

        }


    }

        /**
     * Возвращает домен предыдущего уровня
     * Например: www.some.domain.ru -> some.domain.ru
     * 
     * @param string $domain Исходный домен
     * @return string|null Домен предыдущего уровня или null, если невозможно получить
     */
    public function getParentDomain(string $domain): ?string
    {
        $parts = explode('.', $domain);
        
        // Если домен состоит из 2 или менее частей, вернуть null
        if (count($parts) <= 2) {
            return null;
        }
        
        // Удаляем первую часть домена
        array_shift($parts);
        
        return implode('.', $parts);
    }

} 