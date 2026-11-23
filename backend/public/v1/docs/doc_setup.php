<?php

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Verdant API',
    version: '1.0',
    description: 'REST API of the Verdant restaurant: menu, tables, reservations, users and contact messages.'
)]
#[OA\Server(url: '../../..', description: 'This backend')]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'status', type: 'integer', example: 400),
        new OA\Property(property: 'error', type: 'string', example: "Field 'name' is required.")
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Category deleted.')
    ]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Soups'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Warm homemade soups')
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/CategoryInput'),
        new OA\Schema(properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1)
        ])
    ]
)]
#[OA\Schema(
    schema: 'MenuItemInput',
    required: ['category_id', 'name', 'price'],
    properties: [
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Tomato Soup'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Fresh tomatoes and basil'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 6.5),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'assets/img/menu/tomato-soup.jpg'),
        new OA\Property(property: 'is_available', type: 'boolean', example: true)
    ]
)]
#[OA\Schema(
    schema: 'MenuItem',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/MenuItemInput'),
        new OA\Schema(properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'category_name', type: 'string', example: 'Starters')
        ])
    ]
)]
#[OA\Schema(
    schema: 'TableInput',
    required: ['table_number', 'capacity'],
    properties: [
        new OA\Property(property: 'table_number', type: 'integer', example: 7),
        new OA\Property(property: 'capacity', type: 'integer', example: 4),
        new OA\Property(property: 'is_active', type: 'boolean', example: true)
    ]
)]
#[OA\Schema(
    schema: 'Table',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/TableInput'),
        new OA\Schema(properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1)
        ])
    ]
)]
#[OA\Schema(
    schema: 'ReservationInput',
    required: ['user_id', 'reservation_date', 'reservation_time', 'guests'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'reservation_date', type: 'string', format: 'date', example: '2026-12-20'),
        new OA\Property(property: 'reservation_time', type: 'string', example: '19:30'),
        new OA\Property(property: 'guests', type: 'integer', example: 4),
        new OA\Property(property: 'special_requests', type: 'string', nullable: true, example: 'Window seat if possible'),
        new OA\Property(property: 'table_id', type: 'integer', nullable: true, example: 4, description: 'Ignored on POST; the admin assigns a table with PUT.'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'confirmed', 'cancelled', 'completed'], example: 'confirmed', description: 'Ignored on POST; every new reservation is pending.')
    ]
)]
#[OA\Schema(
    schema: 'Reservation',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/ReservationInput'),
        new OA\Schema(properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'customer_name', type: 'string', example: 'Amina Hodzic'),
            new OA\Property(property: 'table_number', type: 'integer', nullable: true, example: 4),
            new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
        ])
    ]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['first_name', 'last_name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Emir'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Kovac'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'emir@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'verdant123', description: 'At least 8 characters with a letter and a number. Required on POST, optional on PUT.'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+387 61 123 456'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user')
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'first_name', type: 'string', example: 'Amina'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hodzic'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'amina@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+387 61 111 222'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'ContactMessageInput',
    required: ['name', 'email', 'subject', 'message'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'name', type: 'string', example: 'Emir Kovac'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'emir@example.com'),
        new OA\Property(property: 'subject', type: 'string', example: 'Opening hours'),
        new OA\Property(property: 'message', type: 'string', example: 'Are you open on Sundays?')
    ]
)]
#[OA\Schema(
    schema: 'ContactMessage',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/ContactMessageInput'),
        new OA\Schema(properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'sent_at', type: 'string', format: 'date-time')
        ])
    ]
)]
#[OA\Schema(
    schema: 'WeatherDay',
    properties: [
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-11-25'),
        new OA\Property(property: 'weather_code', type: 'integer', example: 71),
        new OA\Property(property: 'description', type: 'string', example: 'Light snow'),
        new OA\Property(property: 'temperature_max', type: 'number', format: 'float', example: 5.5),
        new OA\Property(property: 'temperature_min', type: 'number', format: 'float', example: -0.4),
        new OA\Property(property: 'precipitation_probability', type: 'integer', nullable: true, example: 81)
    ]
)]
#[OA\Schema(
    schema: 'WeatherForecast',
    properties: [
        new OA\Property(property: 'city', type: 'string', example: 'Vitez'),
        new OA\Property(property: 'country', type: 'string', nullable: true, example: 'Bosnia and Herzegovina'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 44.15806),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 17.79),
        new OA\Property(property: 'days', type: 'array', items: new OA\Items(ref: '#/components/schemas/WeatherDay')),
        new OA\Property(property: 'source', type: 'string', example: 'Open-Meteo')
    ]
)]
class DocSetup
{
}
