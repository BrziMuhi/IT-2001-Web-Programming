<?php

require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/MenuItemDao.php';
require_once __DIR__ . '/../dao/RestaurantTableDao.php';
require_once __DIR__ . '/../dao/ReservationDao.php';
require_once __DIR__ . '/../dao/ContactMessageDao.php';

header('Content-Type: text/plain; charset=utf-8');

function check($condition, $message)
{
    echo ($condition ? '[OK]   ' : '[FAIL] ') . $message . "\n";
}

$users = new UserDao();
$categories = new CategoryDao();
$menu = new MenuItemDao();
$tables = new RestaurantTableDao();
$reservations = new ReservationDao();
$messages = new ContactMessageDao();

echo "=== get_all() ===\n";
echo "Users:            " . count($users->get_all()) . "\n";
echo "Categories:       " . count($categories->get_all()) . "\n";
echo "Menu items:       " . count($menu->get_all()) . "\n";
echo "Tables:           " . count($tables->get_all()) . "\n";
echo "Reservations:     " . count($reservations->get_all()) . "\n";
echo "Contact messages: " . count($messages->get_all()) . "\n";

echo "\n=== finders ===\n";
$admin = $users->get_by_email('admin@verdant.com');
check($admin !== null, 'UserDao::get_by_email finds the admin');
check(!isset($admin['password_hash']), 'get_by_email does NOT return the password hash');
$login = $users->get_by_email_with_password('admin@verdant.com');
check(password_verify('admin123', $login['password_hash']), 'get_by_email_with_password: hash matches "admin123"');

$item = $menu->get_by_id(1);
check(isset($item['category_name']), "MenuItemDao::get_by_id joins the category name ({$item['category_name']})");
check($menu->count_by_category(2) > 0, 'MenuItemDao::count_by_category(2) = ' . $menu->count_by_category(2));
check($tables->get_by_table_number(5) !== null, 'RestaurantTableDao::get_by_table_number(5)');

$res = $reservations->get_by_id(1);
check(isset($res['customer_name']), "ReservationDao::get_by_id joins the customer ({$res['customer_name']}, table {$res['table_number']})");
echo "Reservation conflicts for table 3 on 2026-12-05 at 20:00: "
    . count($reservations->get_conflicts(3, '2026-12-05', '20:00:00')) . " (expected 1)\n";

echo "\n=== add / update / delete ===\n";
$new = $categories->add(['name' => 'Test Category', 'description' => 'Created by dao_test', 'hacker' => 'DROP TABLE']);
check(isset($new['id']) && !isset($new['hacker']), "add() returns the new id ({$new['id']}) and ignores unknown keys");

$categories->update(['description' => 'Updated'], $new['id']);
check($categories->get_by_id($new['id'])['description'] === 'Updated', 'update() changes the description');
check($categories->update([], $new['id']) === 0, 'update() with nothing to change does nothing');

check($categories->delete($new['id']) === 1, 'delete() removes the row');
check($categories->get_by_id($new['id']) === null, 'get_by_id() returns null after delete');

echo "\n=== foreign keys ===\n";
try {
    $reservations->add(['user_id' => 999, 'reservation_date' => '2026-12-31', 'reservation_time' => '19:00', 'guests' => 2]);
    check(false, 'reservation for user 999 should be refused');
} catch (PDOException $e) {
    check(true, 'reservation for user 999 is refused by the database');
}

echo "\nDone.\n";
