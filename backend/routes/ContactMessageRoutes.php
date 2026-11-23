<?php

Flight::route('GET /contact-messages', function () {
    Flight::json(Flight::contact_message_service()->get_all());
});

Flight::route('GET /contact-messages/@id:[0-9]+', function ($id) {
    Flight::json(Flight::contact_message_service()->get_by_id($id));
});

Flight::route('POST /contact-messages', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::contact_message_service()->add($data), 201);
});

Flight::route('PUT /contact-messages/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::contact_message_service()->update($data, $id));
});

Flight::route('DELETE /contact-messages/@id:[0-9]+', function ($id) {
    Flight::contact_message_service()->delete($id);
    Flight::json(['message' => 'Message deleted.']);
});

Flight::route('GET /users/@id:[0-9]+/contact-messages', function ($id) {
    Flight::json(Flight::contact_message_service()->get_by_user($id));
});
