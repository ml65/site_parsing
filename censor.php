<?php 


$url = 'https://mamatov.com';

//$url .= '?MatchSearch%5BsinceDt%5D=01.01.2023&MatchSearch%5BtoDt%5D=01.01.2024';

    $data = get($url);

var_dump($data);

    function get(string $url): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
//            CURLOPT_COOKIEFILE => "test.txt",
//            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
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
