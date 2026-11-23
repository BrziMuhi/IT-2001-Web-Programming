<?php

Flight::route('GET /reservations', function () {
    $query = Flight::request()->query;

    if (isset($query['date'])) {
        Flight::json(Flight::reservation_service()->get_by_date($query['date']));
        return;
    }

    Flight::json(Flight::reservation_service()->get_all());
});

Flight::route('GET /reservations/@id:[0-9]+', function ($id) {
    Flight::json(Flight::reservation_service()->get_by_id($id));
});

Flight::route('POST /reservations', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reservation_service()->add($data), 201);
});

Flight::route('PUT /reservations/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reservation_service()->update($data, $id));
});

Flight::route('PUT /reservations/@id:[0-9]+/cancel', function ($id) {
    Flight::json(Flight::reservation_service()->cancel($id));
});

Flight::route('DELETE /reservations/@id:[0-9]+', function ($id) {
    Flight::reservation_service()->delete($id);
    Flight::json(['message' => 'Reservation deleted.']);
});

Flight::route('GET /users/@id:[0-9]+/reservations', function ($id) {
    Flight::json(Flight::reservation_service()->get_by_user($id));
});
