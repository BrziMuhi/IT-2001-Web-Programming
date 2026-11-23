<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/MenuItemService.php';
require_once __DIR__ . '/services/RestaurantTableService.php';
require_once __DIR__ . '/services/ReservationService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/ContactMessageService.php';
require_once __DIR__ . '/services/OpenMeteoService.php';

Flight::register('category_service', 'CategoryService');
Flight::register('menu_item_service', 'MenuItemService');
Flight::register('table_service', 'RestaurantTableService');
Flight::register('reservation_service', 'ReservationService');
Flight::register('user_service', 'UserService');
Flight::register('contact_message_service', 'ContactMessageService');
Flight::register('open_meteo_service', 'OpenMeteoService');

Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);
    }

    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => $status, 'error' => $message]))
        ->send();
});

Flight::map('notFound', function () {
    $method = Flight::request()->method;
    $url = strtok(Flight::request()->url, '?');

    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => 404, 'error' => "Endpoint $method $url does not exist."]))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['message' => 'The backend is running.']);
});

require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/MenuItemRoutes.php';
require_once __DIR__ . '/routes/TableRoutes.php';
require_once __DIR__ . '/routes/ReservationRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/ContactMessageRoutes.php';
require_once __DIR__ . '/routes/WeatherRoutes.php';

Flight::start();
