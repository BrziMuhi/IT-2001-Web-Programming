<?php

Flight::route('GET /tables', function () {
    $query = Flight::request()->query;

    if (isset($query['guests'])) {
        Flight::json(Flight::table_service()->get_available_for_guests($query['guests']));
        return;
    }

    Flight::json(Flight::table_service()->get_all());
});

Flight::route('GET /tables/@id:[0-9]+', function ($id) {
    Flight::json(Flight::table_service()->get_by_id($id));
});

Flight::route('POST /tables', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::table_service()->add($data), 201);
});

Flight::route('PUT /tables/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::table_service()->update($data, $id));
});

Flight::route('DELETE /tables/@id:[0-9]+', function ($id) {
    Flight::table_service()->delete($id);
    Flight::json(['message' => 'Table deleted.']);
});
