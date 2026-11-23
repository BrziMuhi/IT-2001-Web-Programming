<?php

use OpenApi\Attributes as OA;

class TablePaths
{
    #[OA\Get(
        path: '/tables',
        summary: 'List all tables or the tables for a number of guests',
        tags: ['tables'],
        parameters: [
            new OA\Parameter(name: 'guests', in: 'query', required: false, description: 'Only active tables that seat at least this many guests (1-12)', schema: new OA\Schema(type: 'integer'), example: 4)
        ],
        responses: [
            new OA\Response(response: 200, description: 'All tables', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Table'))),
            new OA\Response(response: 400, description: 'Invalid number of guests', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTables()
    {
    }

    #[OA\Get(
        path: '/tables/{id}',
        summary: 'Get one table',
        tags: ['tables'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the table', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The table', content: new OA\JsonContent(ref: '#/components/schemas/Table')),
            new OA\Response(response: 404, description: 'Table not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTablesById()
    {
    }

    #[OA\Post(
        path: '/tables',
        summary: 'Create a table',
        description: 'The table number must be unique and the capacity between 1 and 12.',
        tags: ['tables'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TableInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created table', content: new OA\JsonContent(ref: '#/components/schemas/Table')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTables()
    {
    }

    #[OA\Put(
        path: '/tables/{id}',
        summary: 'Update a table',
        description: 'Fields that are not sent keep their current value.',
        tags: ['tables'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the table', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TableInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated table', content: new OA\JsonContent(ref: '#/components/schemas/Table')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Table not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTablesById()
    {
    }

    #[OA\Delete(
        path: '/tables/{id}',
        summary: 'Delete a table',
        tags: ['tables'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the table', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'Table deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The table has upcoming reservations', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Table not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTablesById()
    {
    }
}
