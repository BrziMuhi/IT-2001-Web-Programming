<?php

require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../services/BaseService.php';
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../services/MenuItemService.php';
require_once __DIR__ . '/../services/RestaurantTableService.php';
require_once __DIR__ . '/../services/ReservationService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/ContactMessageService.php';

header('Content-Type: text/plain; charset=utf-8');

function attempt($description, $action)
{
    echo str_pad($description, 45, '.');
    try {
        $result = $action();
        $id = (is_array($result) && isset($result['id'])) ? " (id {$result['id']})" : '';
        echo " OK$id\n";
    } catch (Exception $e) {
        echo ' ' . get_class($e) . ' [' . $e->getCode() . ']: ' . $e->getMessage() . "\n";
    }
}

$service = new BaseService(new CategoryDao(), 'Category');

echo "=== BaseService ===\n";
attempt('get_all()', fn() => $service->get_all());
attempt('get_by_id(1)', fn() => $service->get_by_id(1));
attempt('get_by_id(999) - does not exist', fn() => $service->get_by_id(999));

$new = null;
attempt('add() a test category', function () use ($service, &$new) {
    return $new = $service->add(['name' => 'Test Category', 'description' => 'from service_test']);
});
attempt('update() the test category', fn() => $service->update(['description' => 'changed'], $new['id']));
attempt('update(999) - does not exist', fn() => $service->update(['description' => 'x'], 999));
attempt('delete() the test category', fn() => $service->delete($new['id']));
attempt('delete(999) - does not exist', fn() => $service->delete(999));

echo "\n=== Validator (every line should be a ValidationException) ===\n";
attempt('required: no name', fn() => Validator::required([], ['name']));
attempt('length: name "M"', fn() => Validator::length('M', 'name', 2, 60));
attempt('integer: guests "abc"', fn() => Validator::integer('abc', 'guests', 1, 8));
attempt('integer: guests 9', fn() => Validator::integer(9, 'guests', 1, 8));
attempt('number: price "abc"', fn() => Validator::number('abc', 'price', 0.01));
attempt('one_of: role "boss"', fn() => Validator::one_of('boss', 'role', ['admin', 'user']));
attempt('email: "nope"', fn() => Validator::email('nope'));
attempt('password: "123"', fn() => Validator::password('123'));
attempt('date: 2026-02-30', fn() => Validator::date('2026-02-30', 'reservation_date'));
attempt('time: 25:00', fn() => Validator::time('25:00', 'reservation_time'));
attempt('in_future: 2020-01-01', fn() => Validator::in_future('2020-01-01 10:00:00', 'reservation'));
attempt('no_html: <script>', fn() => Validator::no_html('<script>alert(1)</script>', 'message'));

echo "\n=== Validator (every line should be OK) ===\n";
attempt('required: name "Soups"', fn() => Validator::required(['name' => 'Soups'], ['name']));
attempt('integer: guests 4', fn() => Validator::integer(4, 'guests', 1, 8));
attempt('email: amina@example.com', fn() => Validator::email('amina@example.com'));
attempt('password: "verdant123"', fn() => Validator::password('verdant123'));
attempt('in_future: tomorrow', fn() => Validator::in_future(date('Y-m-d H:i:s', strtotime('+1 day')), 'reservation'));
attempt('no_html: "Window seat please"', fn() => Validator::no_html('Window seat please', 'message'));

echo "\n=== CategoryService ===\n";
$categories = new CategoryService();
attempt('add: no name', fn() => $categories->add(['description' => 'x']));
attempt('add: name "M"', fn() => $categories->add(['name' => 'M']));
attempt('add: name "Starters" (exists)', fn() => $categories->add(['name' => 'Starters']));
attempt('update #1: keeps its own name "Starters"', fn() => $categories->update(['name' => 'Starters', 'description' => 'Light dishes to start your meal'], 1));
$cat = null;
attempt('add: "Soups"', function () use ($categories, &$cat) { return $cat = $categories->add(['name' => '  Soups  ', 'description' => '']); });
echo "    stored name: '{$cat['name']}', description: " . var_export($cat['description'], true) . "\n";
attempt('delete #2 (Main Courses has items)', fn() => $categories->delete(2));
attempt('delete #999', fn() => $categories->delete(999));
attempt('delete "Soups" (no items)', fn() => $categories->delete($cat['id']));

echo "\n=== MenuItemService ===\n";
$menu = new MenuItemService();
attempt('add: category #999', fn() => $menu->add(['category_id' => 999, 'name' => 'Soup', 'price' => 5]));
attempt('add: price -5', fn() => $menu->add(['category_id' => 1, 'name' => 'Soup', 'price' => -5]));
attempt('add: price "free"', fn() => $menu->add(['category_id' => 1, 'name' => 'Soup', 'price' => 'free']));
attempt('add: description with <script>', fn() => $menu->add(['category_id' => 1, 'name' => 'Soup', 'price' => 5, 'description' => '<script>alert(1)</script>']));
$item = null;
attempt('add: valid "Tomato Soup", checkbox "on"', function () use ($menu, &$item) { return $item = $menu->add(['category_id' => 1, 'name' => 'Tomato Soup', 'price' => '6.50', 'is_available' => 'on']); });
echo "    is_available: {$item['is_available']}, category_name: {$item['category_name']}\n";
$changed = null;
attempt('update: send only a new price', function () use ($menu, $item, &$changed) { return $changed = $menu->update(['price' => 7], $item['id']); });
echo "    price now: {$changed['price']}, name still: {$changed['name']}\n";
attempt('get_by_category(999)', fn() => $menu->get_by_category(999));
attempt('delete "Tomato Soup"', fn() => $menu->delete($item['id']));

echo "\n=== RestaurantTableService ===\n";
$tables = new RestaurantTableService();
attempt('add: table number 5 (exists)', fn() => $tables->add(['table_number' => 5, 'capacity' => 4]));
attempt('add: capacity 20', fn() => $tables->add(['table_number' => 10, 'capacity' => 20]));
$tbl = null;
attempt('add: table 10 for 4 people', function () use ($tables, &$tbl) { return $tbl = $tables->add(['table_number' => 10, 'capacity' => 4]); });
attempt('delete table #3 (has upcoming reservation)', fn() => $tables->delete(3));
attempt('delete table 10 (no reservations)', fn() => $tables->delete($tbl['id']));

echo "\n=== ReservationService ===\n";
$reservations = new ReservationService();
$future = date('Y-m-d', strtotime('+10 days'));
attempt('add: date in the past', fn() => $reservations->add(['user_id' => 3, 'reservation_date' => '2025-01-10', 'reservation_time' => '19:00', 'guests' => 2]));
attempt('add: 08:00 (before opening)', fn() => $reservations->add(['user_id' => 3, 'reservation_date' => $future, 'reservation_time' => '08:00', 'guests' => 2]));
attempt('add: 12 guests', fn() => $reservations->add(['user_id' => 3, 'reservation_date' => $future, 'reservation_time' => '19:00', 'guests' => 12]));
attempt('add: customer #999', fn() => $reservations->add(['user_id' => 999, 'reservation_date' => $future, 'reservation_time' => '19:00', 'guests' => 2]));

$r1 = null;
attempt('add: Amina, client sends status + table', function () use ($reservations, $future, &$r1) {
    return $r1 = $reservations->add(['user_id' => 2, 'reservation_date' => $future, 'reservation_time' => '19:30', 'guests' => 2, 'status' => 'confirmed', 'table_id' => 5]);
});
echo "    saved status: {$r1['status']}, table_id: " . var_export($r1['table_id'], true) . "\n";
attempt('add: Amina 4th upcoming reservation', fn() => $reservations->add(['user_id' => 2, 'reservation_date' => $future, 'reservation_time' => '13:00', 'guests' => 2]));

$r2 = null;
attempt('add: Haris, 2026-12-05 20:00, 4 guests', function () use ($reservations, &$r2) {
    return $r2 = $reservations->add(['user_id' => 3, 'reservation_date' => '2026-12-05', 'reservation_time' => '20:00', 'guests' => 4]);
});
attempt('assign table 1 (seats 2) to 4 guests', fn() => $reservations->update(['table_id' => 1], $r2['id']));
attempt('assign table 6 (not in use)', fn() => $reservations->update(['table_id' => 6], $r2['id']));
attempt('assign table 3 (booked at 19:00 that day)', fn() => $reservations->update(['table_id' => 3], $r2['id']));
attempt('assign table 4 and confirm', fn() => $reservations->update(['table_id' => 4, 'status' => 'confirmed'], $r2['id']));
attempt('update: status "maybe"', fn() => $reservations->update(['status' => 'maybe'], $r2['id']));
attempt('cancel Haris\'s reservation', fn() => $reservations->cancel($r2['id']));
attempt('cancel it again', fn() => $reservations->cancel($r2['id']));
attempt('get_by_user(999)', fn() => $reservations->get_by_user(999));
attempt('get_by_date("tomorrow")', fn() => $reservations->get_by_date('tomorrow'));

$reservations->delete($r1['id']);
$reservations->delete($r2['id']);

echo "\n=== UserService ===\n";
$users = new UserService();
$valid = ['first_name' => 'Test', 'last_name' => 'Customer', 'email' => 'Test.Customer@Example.com'];
attempt('add: no password', fn() => $users->add($valid));
attempt('add: password "123"', fn() => $users->add($valid + ['password' => '123']));
attempt('add: email "ADMIN@verdant.com" (exists)', fn() => $users->add(['email' => 'ADMIN@verdant.com', 'password' => 'verdant123'] + $valid));
attempt('add: role "boss"', fn() => $users->add(['role' => 'boss', 'password' => 'verdant123'] + $valid));
attempt('add: phone "call me"', fn() => $users->add(['phone' => 'call me', 'password' => 'verdant123'] + $valid));
$u = null;
attempt('add: valid customer', function () use ($users, $valid, &$u) { return $u = $users->add($valid + ['password' => 'verdant123']); });
$login = (new UserDao())->get_by_email_with_password($u['email']);
echo "    stored email: {$u['email']}\n";
echo "    role: {$u['role']}\n";
echo "    hash returned by add(): " . (isset($u['password_hash']) ? 'YES (bad)' : 'no') . "\n";
echo "    password is hashed: " . (password_verify('verdant123', $login['password_hash']) ? 'yes' : 'NO') . "\n";
attempt('update: send only a phone number', fn() => $users->update(['phone' => '+387 61 000 111'], $u['id']));
attempt('delete the test customer', fn() => $users->delete($u['id']));

echo "\n=== ContactMessageService ===\n";
$messages = new ContactMessageService();
$msg = ['name' => 'Emir Kovac', 'email' => 'emir@example.com', 'subject' => 'Opening hours', 'message' => 'Are you open on Sundays?'];
attempt('add: message "Hi"', fn() => $messages->add(['message' => 'Hi'] + $msg));
attempt('add: email "emir"', fn() => $messages->add(['email' => 'emir'] + $msg));
attempt('add: subject with <b>', fn() => $messages->add(['subject' => '<b>Hello</b>'] + $msg));
attempt('add: user #999', fn() => $messages->add(['user_id' => 999] + $msg));
$m = null;
attempt('add: visitor without account', function () use ($messages, $msg, &$m) { return $m = $messages->add($msg + ['user_id' => '']); });
attempt('delete it', fn() => $messages->delete($m['id']));

echo "\nDone.\n";
