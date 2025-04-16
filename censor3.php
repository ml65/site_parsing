<?php 


$url = 'https://mamatov.com';

// Инициализация cURL для первого запроса
$ch = curl_init();

// Настройки cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Не следовать редиректам
curl_setopt($ch, CURLOPT_HEADER, false); // Не включать заголовки в вывод
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключить проверку SSL (если нужно)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Отключить проверку хоста SSL (если нужно)

// Установка User-Agent, чтобы эмулировать браузер
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    'Connection: keep-alive'
));

// Выполнение первого запроса
$response = curl_exec($ch);

// Проверка на ошибки
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Закрытие первого сеанса
curl_close($ch);

// Извлечение имени и значения куки из JavaScript
preg_match("/document\.cookie='(.*?)'/", $response, $cookie_match);
if (!empty($cookie_match[1])) {
    $cookie_str = $cookie_match[1]; // Например, 'beget=begetok'
    // Извлечение имени и значения куки
    list($cookie_name, $cookie_value) = explode('=', $cookie_str);
} else {
    echo 'Cookie not found in response';
    exit;
}

// Подготовка куки для второго запроса
$cookie = "$cookie_name=$cookie_value";

// Инициализация cURL для второго запроса
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следовать редиректам
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Установка куки и заголовков
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    'Connection: keep-alive'
));
curl_setopt($ch, CURLOPT_COOKIE, $cookie);

// Выполнение второго запроса
$response = curl_exec($ch);

// Проверка на ошибки
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Вывод тела ответа
    echo $response;
}

// Закрытие сеанса
curl_close($ch);
?>