<?php

namespace Parser\Http;

class HttpClient
{
    private const MAX_REDIRECTS = 0;
    private const TIMEOUT = 30;

    public function get(string $url): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        
        curl_close($ch);

        if ($httpCode === 302 && $redirectUrl) {
            return [
                'status' => 'redirect',
                'redirect_url' => $redirectUrl
            ];
        }

        if ($httpCode !== 200) {
            return [
                'status' => 'error',
                'code' => $httpCode
            ];
        }

        return [
            'status' => 'success',
            'content' => $response
        ];
    }
} 