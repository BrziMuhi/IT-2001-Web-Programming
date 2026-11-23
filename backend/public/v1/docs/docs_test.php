<?php
require __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/doc_setup.php';
foreach (glob(__DIR__ . '/paths/*.php') as $file) {
    require_once $file;
}

const PORT = 8766;
const BASE = 'http://localhost:' . PORT . '/backend';

$spec = json_decode((new \OpenApi\Generator())->generate([__DIR__ . '/doc_setup.php', __DIR__ . '/paths'])->toJson(), true);

$root = dirname(__DIR__, 4);
$log = sys_get_temp_dir() . '/verdant_docs_server.log';
$server = proc_open([PHP_BINARY, '-S', 'localhost:' . PORT, $root . '/server.php'], [1 => ['file', $log, 'w'], 2 => ['file', $log, 'a']], $pipes, $root);
for ($i = 0; $i < 50; $i++) {
    $socket = @fsockopen('localhost', PORT);
    if ($socket) {
        fclose($socket);
        break;
    }
    usleep(100000);
}

function request($method, $path, $body = null)
{
    $options = ['method' => $method, 'ignore_errors' => true, 'header' => "Content-Type: application/json\r\n"];
    if ($body !== null) {
        $options['content'] = json_encode($body);
    }
    $raw = @file_get_contents(BASE . $path, false, stream_context_create(['http' => $options]));
    preg_match('/\s(\d{3})\s/', $http_response_header[0] ?? '', $match);
    return [(int) ($match[1] ?? 0), json_decode($raw === false ? 'null' : $raw, true), $raw];
}

function documented($spec, $method, $url, $status)
{
    $path = strtok($url, '?');
    foreach ($spec['paths'] as $template => $operations) {
        $regex = '#^' . preg_replace('#\\\{[^}]+\\\}#', '[0-9]+', preg_quote($template, '#')) . '$#';
        if (preg_match($regex, $path) && isset($operations[strtolower($method)])) {
            return isset($operations[strtolower($method)]['responses'][$status]);
        }
    }
    return false;
}

$undocumented = 0;
function check($method, $url, $body = null)
{
    global $spec, $undocumented;
    [$status, $json] = request($method, $url, $body);
    $ok = documented($spec, $method, $url, $status);
    if (!$ok) {
        $undocumented++;
    }
    echo '    ' . str_pad("$method $url ", 54, '.') . " $status  " . ($ok ? 'documented' : 'NOT DOCUMENTED') . "\n";
    return $json;
}

echo "=== OpenAPI document ===\n";
echo str_pad('OpenAPI document ', 58, '.') . " OpenAPI {$spec['openapi']}, " . count($spec['paths']) . ' paths, ' . count($spec['components']['schemas']) . " schemas\n";
[$status, , $raw] = request('GET', '/public/v1/docs/');
echo str_pad('GET /public/v1/docs/ ', 58, '.') . " $status  " . (strpos((string) $raw, 'swagger-ui') !== false ? 'Swagger UI' : 'NOT the Swagger UI page') . "\n";
[$status, $json] = request('GET', '/public/v1/docs/swagger.php');
echo str_pad('GET /public/v1/docs/swagger.php ', 58, '.') . " $status  " . (isset($json['paths']) ? 'OpenAPI JSON, ' . count($json['paths']) . ' paths' : 'NOT OpenAPI JSON') . "\n";

echo "\n=== documented endpoints ===\n";
foreach ($spec['paths'] as $template => $operations) {
    foreach ($operations as $method => $operation) {
        if (!isset($operation['responses'])) {
            continue;
        }
        $params = array_map(fn($p) => "{$p['name']} ({$p['in']}, {$p['schema']['type']})", $operation['parameters'] ?? []);
        echo str_pad(strtoupper($method) . " $template ", 58, '.') . ' responses: ' . implode(', ', array_keys($operation['responses']))
            . ' | tags: ' . implode(', ', $operation['tags'] ?? [])
            . ($params ? ' | parameters: ' . implode(', ', $params) : '') . "\n";
    }
}

$future = date('Y-m-d', strtotime('+10 days'));

echo "\n=== real requests ===\n";
check('GET', '/categories');
check('GET', '/categories/1');
check('GET', '/categories/999');
check('POST', '/categories', ['name' => 'M']);
$category = check('POST', '/categories', ['name' => 'Docs Soups']);
check('PUT', "/categories/{$category['id']}", ['name' => 'Docs Hot Soups']);
check('PUT', '/categories/999', ['name' => 'Nothing']);
check('PUT', "/categories/{$category['id']}", ['name' => 'Starters']);
check('DELETE', '/categories/2');
check('DELETE', "/categories/{$category['id']}");
check('DELETE', "/categories/{$category['id']}");
check('GET', '/categories/2/menu-items');
check('GET', '/categories/999/menu-items');
check('GET', '/categories/0/menu-items');

check('GET', '/menu-items');
check('GET', '/menu-items?available=1');
check('GET', '/menu-items/1');
check('GET', '/menu-items/999');
check('POST', '/menu-items', ['category_id' => 1, 'name' => 'Docs Soup', 'price' => -5]);
$item = check('POST', '/menu-items', ['category_id' => 1, 'name' => 'Docs Soup', 'price' => 6.5]);
check('PUT', "/menu-items/{$item['id']}", ['price' => 7]);
check('PUT', "/menu-items/{$item['id']}", ['price' => 'free']);
check('PUT', '/menu-items/999', ['price' => 7]);
check('DELETE', "/menu-items/{$item['id']}");
check('DELETE', "/menu-items/{$item['id']}");

check('GET', '/tables');
check('GET', '/tables?guests=5');
check('GET', '/tables?guests=50');
check('GET', '/tables/3');
check('GET', '/tables/999');
check('POST', '/tables', ['table_number' => 5, 'capacity' => 4]);
$table = check('POST', '/tables', ['table_number' => 21, 'capacity' => 4]);
check('PUT', "/tables/{$table['id']}", ['capacity' => 6]);
check('PUT', "/tables/{$table['id']}", ['capacity' => 60]);
check('PUT', '/tables/999', ['capacity' => 6]);
check('DELETE', '/tables/3');
check('DELETE', "/tables/{$table['id']}");
check('DELETE', "/tables/{$table['id']}");

check('GET', '/reservations');
check('GET', '/reservations?date=2026-12-05');
check('GET', '/reservations?date=tomorrow');
check('GET', '/reservations/1');
check('GET', '/reservations/999');
check('POST', '/reservations', ['user_id' => 3, 'reservation_date' => '2025-01-10', 'reservation_time' => '19:00', 'guests' => 2]);
$reservation = check('POST', '/reservations', ['user_id' => 3, 'reservation_date' => $future, 'reservation_time' => '19:30', 'guests' => 4]);
check('PUT', "/reservations/{$reservation['id']}", ['table_id' => 1]);
check('PUT', "/reservations/{$reservation['id']}", ['table_id' => 4, 'status' => 'confirmed']);
check('PUT', '/reservations/999', ['status' => 'confirmed']);
check('PUT', "/reservations/{$reservation['id']}/cancel");
check('PUT', "/reservations/{$reservation['id']}/cancel");
check('PUT', '/reservations/999/cancel');
check('DELETE', "/reservations/{$reservation['id']}");
check('DELETE', "/reservations/{$reservation['id']}");
check('GET', '/users/2/reservations');
check('GET', '/users/999/reservations');
check('GET', '/users/0/reservations');

check('GET', '/users');
check('GET', '/users/1');
check('GET', '/users/999');
check('POST', '/users', ['first_name' => 'Docs', 'last_name' => 'User', 'email' => 'docs@example.com', 'password' => '123']);
$user = check('POST', '/users', ['first_name' => 'Docs', 'last_name' => 'User', 'email' => 'docs@example.com', 'password' => 'verdant123']);
check('PUT', "/users/{$user['id']}", ['phone' => '+387 61 000 111']);
check('PUT', "/users/{$user['id']}", ['role' => 'boss']);
check('PUT', '/users/999', ['phone' => '+387 61 000 111']);
check('DELETE', "/users/{$user['id']}");
check('DELETE', "/users/{$user['id']}");

check('GET', '/contact-messages');
check('GET', '/contact-messages/1');
check('GET', '/contact-messages/999');
check('POST', '/contact-messages', ['name' => 'Emir', 'email' => 'emir@example.com', 'subject' => 'Hi', 'message' => 'Short']);
$message = check('POST', '/contact-messages', ['name' => 'Emir Kovac', 'email' => 'emir@example.com', 'subject' => 'Docs message', 'message' => 'Are you open on Sundays?']);
check('PUT', "/contact-messages/{$message['id']}", ['subject' => 'Docs message (edited)']);
check('PUT', "/contact-messages/{$message['id']}", ['subject' => 'x']);
check('PUT', '/contact-messages/999', ['subject' => 'Docs message']);
check('DELETE', "/contact-messages/{$message['id']}");
check('DELETE', "/contact-messages/{$message['id']}");
check('GET', '/users/2/contact-messages');
check('GET', '/users/999/contact-messages');
check('GET', '/users/0/contact-messages');

check('GET', '/weather/forecast');
check('GET', '/weather/forecast?city=a');
check('GET', '/weather/forecast?city=Vitez&days=50');
check('GET', '/weather/forecast?city=Nowhereville');
check('GET', '/weather/forecast?city=Vitez&days=3');

proc_terminate($server);
proc_close($server);
echo "\n" . ($undocumented === 0 ? 'Every answer is documented.' : "$undocumented answer(s) NOT DOCUMENTED.") . "\n";
