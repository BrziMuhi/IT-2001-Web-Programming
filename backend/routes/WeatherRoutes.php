<?php

Flight::route('GET /weather/forecast', function () {
    $city = Flight::request()->query['city'] ?? '';
    $days = Flight::request()->query['days'] ?? 7;
    Flight::json(Flight::open_meteo_service()->forecast($city, $days));
});
