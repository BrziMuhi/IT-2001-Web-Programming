<?php

const PORT = 8765;
const BASE = 'http://localhost:' . PORT . '/backend';

$root = dirname(__DIR__, 2);
$server = proc_open(
    [PHP_BINARY, '-S', 'localhost:' . PORT, $root . '/server.php'],
    [1 => ['file', sys_get_temp_dir() . '/verdant_server.log', 'w'], 2 => ['file', sys_get_temp_dir() . '/verdant_server.log', 'a']],
    $pipes,
    $root
);

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

function label($row)
{
    if (isset($row['customer_name'])) {
        return "{$row['customer_name']} {$row['reservation_date']} {$row['reservation_time']} ({$row['status']})";
    }
    if (isset($row['first_name'])) {
        return "{$row['first_name']} {$row['last_name']}";
    }
    if (isset($row['subject'])) {
        return $row['subject'];
    }
    if (isset($row['table_number'])) {
        return "table {$row['table_number']} ({$row['capacity']} seats)";
    }
    return $row['name'] ?? '';
}

function summary($json, $raw)
{
    if (!is_array($json)) {
        return 'NOT JSON: ' . substr(trim(strip_tags((string) $raw)), 0, 80);
    }
    if (isset($json['error'])) {
        return $json['error'];
    }
    if (isset($json['id'])) {
        return "id {$json['id']} - " . label($json);
    }
    if (isset($json['message'])) {
        return $json['message'];
    }
    $names = array_map('label', array_slice($json, 0, 4));
    return count($json) . ' item(s)' . (count($names) ? ': ' . implode(', ', $names) . (count($json) > 4 ? ', ...' : '') : '');
}

function check($method, $path, $body = null)
{
    [$status, $json, $raw] = request($method, $path, $body);
    $line = "$method $path ";
    echo str_pad($line, 58, '.') . " $status  " . summary($json, $raw) . "\n";
    return $json;
}

$future = date('Y-m-d', strtotime('+10 days'));

echo "=== the API is running ===\n";
check('GET', '/');

echo "\n=== error handler ===\n";
check('GET', '/categories/999');
check('POST', '/categories', ['name' => 'M']);
check('GET', '/this-does-not-exist');
check('DELETE', '/categories/abc?force=1');

echo "\n=== categories ===\n";
check('GET', '/categories');
check('GET', '/categories/1');
check('POST', '/categories', []);
check('POST', '/categories', ['name' => 'Starters']);
$category = check('POST', '/categories', ['name' => 'Checker Soups', 'description' => 'Warm soups']);
check('PUT', "/categories/{$category['id']}", ['name' => 'Checker Hot Soups']);
check('PUT', '/categories/999', ['name' => 'Nothing']);
check('DELETE', '/categories/2');
check('DELETE', "/categories/{$category['id']}");
check('GET', "/categories/{$category['id']}");
check('GET', '/categories/2/menu-items');
check('GET', '/categories/999/menu-items');

echo "\n=== menu items ===\n";
check('GET', '/menu-items');
check('GET', '/menu-items?available=1');
check('GET', '/menu-items/1');
check('GET', '/menu-items/999');
check('POST', '/menu-items', ['category_id' => 1, 'name' => 'Checker Soup', 'price' => -5]);
$item = check('POST', '/menu-items', ['category_id' => 1, 'name' => 'Checker Soup', 'price' => 6.5, 'is_available' => true]);
check('PUT', "/menu-items/{$item['id']}", ['price' => 7]);
check('DELETE', "/menu-items/{$item['id']}");

echo "\n=== tables ===\n";
check('GET', '/tables');
check('GET', '/tables?guests=5');
check('GET', '/tables?guests=50');
check('GET', '/tables/3');
check('POST', '/tables', ['table_number' => 5, 'capacity' => 4]);
$table = check('POST', '/tables', ['table_number' => 20, 'capacity' => 4]);
check('PUT', "/tables/{$table['id']}", ['capacity' => 6]);
check('DELETE', '/tables/3');
check('DELETE', "/tables/{$table['id']}");

echo "\n=== reservations ===\n";
check('GET', '/reservations');
check('GET', '/reservations?date=2026-12-05');
check('GET', '/reservations?date=tomorrow');
check('GET', '/reservations/1');
check('GET', '/reservations/999');
check('POST', '/reservations', ['user_id' => 3, 'reservation_date' => '2025-01-10', 'reservation_time' => '19:00', 'guests' => 2]);
check('POST', '/reservations', ['user_id' => 3, 'reservation_date' => $future, 'reservation_time' => '08:00', 'guests' => 2]);
$reservation = check('POST', '/reservations', ['user_id' => 3, 'reservation_date' => $future, 'reservation_time' => '19:30', 'guests' => 4, 'status' => 'confirmed', 'table_id' => 5]);
echo "    status of the new reservation: {$reservation['status']}, table: " . var_export($reservation['table_id'], true) . "\n";
check('PUT', "/reservations/{$reservation['id']}", ['table_id' => 1]);
check('PUT', "/reservations/{$reservation['id']}", ['table_id' => 4, 'status' => 'confirmed']);
check('PUT', "/reservations/{$reservation['id']}/cancel");
check('PUT', "/reservations/{$reservation['id']}/cancel");
check('DELETE', "/reservations/{$reservation['id']}");
check('GET', '/users/2/reservations');
check('GET', '/users/999/reservations');

echo "\n=== users ===\n";
check('GET', '/users');
$admin = request('GET', '/users/1')[1];
echo str_pad('GET /users/1 ', 58, '.') . " password_hash in the answer: " . (isset($admin['password_hash']) ? 'YES (bad)' : 'no') . "\n";
check('GET', '/users/999');
check('POST', '/users', ['first_name' => 'Checker', 'last_name' => 'User', 'email' => 'checker@example.com', 'password' => '123']);
check('POST', '/users', ['first_name' => 'Checker', 'last_name' => 'User', 'email' => 'admin@verdant.com', 'password' => 'verdant123']);
$user = check('POST', '/users', ['first_name' => 'Checker', 'last_name' => 'User', 'email' => 'Checker@Example.com', 'password' => 'verdant123']);
echo "    role: {$user['role']}, email: {$user['email']}, password_hash in the answer: " . (isset($user['password_hash']) ? 'YES (bad)' : 'no') . "\n";
check('PUT', "/users/{$user['id']}", ['phone' => '+387 61 000 111']);
check('PUT', "/users/{$user['id']}", ['role' => 'boss']);
check('DELETE', "/users/{$user['id']}");

echo "\n=== contact messages ===\n";
check('GET', '/contact-messages');
check('GET', '/contact-messages/1');
check('POST', '/contact-messages', ['name' => 'Emir', 'email' => 'emir@example.com', 'subject' => 'Hi', 'message' => 'Short']);
check('POST', '/contact-messages', ['name' => 'Emir', 'email' => 'emir@example.com', 'subject' => 'Hello', 'message' => '<script>alert(1)</script>']);
$message = check('POST', '/contact-messages', ['name' => 'Emir Kovac', 'email' => 'emir@example.com', 'subject' => 'Checker message', 'message' => 'Are you open on Sundays?']);
check('PUT', "/contact-messages/{$message['id']}", ['subject' => 'Checker message (edited)']);
check('DELETE', "/contact-messages/{$message['id']}");
check('GET', '/users/2/contact-messages');
check('GET', '/users/999/contact-messages');

proc_terminate($server);
proc_close($server);
echo "\nDone.\n";
