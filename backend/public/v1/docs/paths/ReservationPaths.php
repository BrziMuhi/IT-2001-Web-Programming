<?php

use OpenApi\Attributes as OA;

class ReservationPaths
{
    #[OA\Get(
        path: '/reservations',
        summary: 'List all reservations or the reservations of one day',
        tags: ['reservations'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', required: false, description: 'Only the reservations on this day (YYYY-MM-DD)', schema: new OA\Schema(type: 'string'), example: '2026-12-05')
        ],
        responses: [
            new OA\Response(response: 200, description: 'All reservations', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Reservation'))),
            new OA\Response(response: 400, description: 'Invalid date', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReservations()
    {
    }

    #[OA\Get(
        path: '/reservations/{id}',
        summary: 'Get one reservation',
        tags: ['reservations'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the reservation', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The reservation', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 404, description: 'Reservation not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReservationsById()
    {
    }

    #[OA\Post(
        path: '/reservations',
        summary: 'Create a reservation',
        description: 'The reservation must be in the future, between 09:00 and 22:00, for 1 to 8 guests. A customer can have at most 3 upcoming reservations. Every new reservation is pending and has no table; status and table_id are ignored.',
        tags: ['reservations'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReservationInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created reservation', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postReservations()
    {
    }

    #[OA\Put(
        path: '/reservations/{id}',
        summary: 'Update a reservation',
        description: 'Used by the admin to confirm a reservation or assign a table. The table must exist, be in use, seat all guests and be free within 2 hours of the reservation. Fields that are not sent keep their current value.',
        tags: ['reservations'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the reservation', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReservationInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated reservation', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Reservation not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putReservationsById()
    {
    }

    #[OA\Delete(
        path: '/reservations/{id}',
        summary: 'Delete a reservation',
        tags: ['reservations'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the reservation', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reservation deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Reservation not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteReservationsById()
    {
    }

    #[OA\Put(
        path: '/reservations/{id}/cancel',
        summary: 'Cancel a reservation',
        tags: ['reservations'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the reservation', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The cancelled reservation', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 400, description: 'Only pending or confirmed reservations can be cancelled', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Reservation not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putReservationsByIdCancel()
    {
    }

    #[OA\Get(
        path: '/users/{id}/reservations',
        summary: 'List the reservations of a user',
        tags: ['users', 'reservations'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', schema: new OA\Schema(type: 'integer'), example: 2)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The reservations of the user', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Reservation'))),
            new OA\Response(response: 400, description: 'Invalid id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersByIdReservations()
    {
    }
}
