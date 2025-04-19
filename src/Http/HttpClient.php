<?php

namespace Parser\Http;

class HttpClient
{
    private const MAX_REDIRECTS = 0;
    private const TIMEOUT = 30;
    private string $cookieFile;

    public function __construct()
    {
        $this->cookieFile = sys_get_temp_dir() . '/parser_cookies.txt';
    }

    public function get(string $url): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive'
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        
        curl_close($ch);

        if (($httpCode === 302 || $httpCode === 301) && $redirectUrl) {
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

    public function getSecond(string $url, array $cookies = []): array
    {
        $cookie = "";
        foreach ($cookies as $key => $value) {
            $cookie .= "$key=$value; ";
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIE => $cookie,
//            CURLOPT_COOKIEFILE => $this->cookieFile,
//            CURLOPT_COOKIEJAR => $this->cookieFile,
//            CURLOPT_COOKIESESSION => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive'
        ));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        
        curl_close($ch);

        return [
            'status' => 'success',
            'content' => $response
        ];
    }


    public function getCookies(string $content): array
    {
        $cookie = [];
        preg_match("/document\.cookie='(.*?)'/", $content, $matches);
        if (!empty($matches[1])) {
            $cookie_str = $matches[1];
            list($cookie_name, $cookie_value) = explode('=', $cookie_str);
            $cookie = [ $cookie_name => $cookie_value ];
        }
        return $cookie;
    }

    public function clearCookies(): void
    {
        if (file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }
} 