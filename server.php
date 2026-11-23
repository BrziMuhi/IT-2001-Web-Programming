<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if (is_dir($file) && is_file(rtrim($file, '/') . '/index.html')) {
    header('Content-Type: text/html; charset=utf-8');
    readfile(rtrim($file, '/') . '/index.html');
    return true;
}

if ($path !== '/' && is_file($file)) {
    if (substr($file, -4) === '.php') {
        require $file;
        return true;
    }
    return false;
}

if (strpos($path, '/backend') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
    require __DIR__ . '/backend/index.php';
    return true;
}

return false;
