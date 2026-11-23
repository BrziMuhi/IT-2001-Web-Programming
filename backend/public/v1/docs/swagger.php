<?php
require __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/doc_setup.php';
foreach (glob(__DIR__ . '/paths/*.php') as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())->generate([__DIR__ . '/doc_setup.php', __DIR__ . '/paths']);

header('Content-Type: application/json');
echo $openapi->toJson();
