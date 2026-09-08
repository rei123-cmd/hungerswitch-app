<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri === '/' || $requestUri === '') {
    require __DIR__ . '/../public/index.php';
    exit;
}

$file = __DIR__ . '/../public' . $requestUri;

if (file_exists($file) && !is_dir($file)) {
    require $file;
    exit;
}

http_response_code(404);
echo "404 Not Found";