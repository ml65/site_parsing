<?php

namespace Parser\Scanner;

use Parser\Http\HttpClient;
use Parser\Model\Site;
use Parser\Parser\ParserInterface;

class SiteScanner
{
    private HttpClient $httpClient;
    private ParserInterface $parser;
    private array $visitedUrls = [];
    private const MAX_DEPTH = 2;

    public function __construct(HttpClient $httpClient, ParserInterface $parser)
    {
        $this->httpClient = $httpClient;
        $this->parser = $parser;
    }

    public function scan(Site $site, int $depth = 0): void
    {
        if ($depth > self::MAX_DEPTH) {
            return;
        }

        $url = 'https://' . $site->getDomain();
        
        if (isset($this->visitedUrls[$url])) {
            return;
        }
        
        $this->visitedUrls[$url] = true;
        
        $response = $this->httpClient->get($url);
        
        if ($response['status'] === 'redirect') {
            $redirectDomain = parse_url($response['redirect_url'], PHP_URL_HOST);
            $site->setRedirectDomain($redirectDomain);
            return;
        }
        
        if ($response['status'] === 'error') {
            return;
        }
        
        $this->parser->parse($response['content'], $site);
        
        // Извлекаем все ссылки из контента
        preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>/i', $response['content'], $matches);
        
        foreach ($matches[1] as $link) {
            $parsedUrl = parse_url($link);
            
            if (!isset($parsedUrl['host'])) {
                $parsedUrl['host'] = $site->getDomain();
                $parsedUrl['path'] = $link;
            }
            
            if ($parsedUrl['host'] === $site->getDomain()) {
                $newUrl = 'https://' . $parsedUrl['host'] . ($parsedUrl['path'] ?? '');
                $this->scan($site, $depth + 1);
            }
        }
    }
} 