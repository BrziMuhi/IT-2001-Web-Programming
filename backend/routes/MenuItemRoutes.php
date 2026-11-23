<?php

Flight::route('GET /menu-items', function () {
    $query = Flight::request()->query;

    if (isset($query['available'])) {
        Flight::json(Flight::menu_item_service()->get_available());
        return;
    }

    Flight::json(Flight::menu_item_service()->get_all());
});

Flight::route('GET /menu-items/@id:[0-9]+', function ($id) {
    Flight::json(Flight::menu_item_service()->get_by_id($id));
});

Flight::route('POST /menu-items', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::menu_item_service()->add($data), 201);
});

Flight::route('PUT /menu-items/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::menu_item_service()->update($data, $id));
});

Flight::route('DELETE /menu-items/@id:[0-9]+', function ($id) {
    Flight::menu_item_service()->delete($id);
    Flight::json(['message' => 'Menu item deleted.']);
});
