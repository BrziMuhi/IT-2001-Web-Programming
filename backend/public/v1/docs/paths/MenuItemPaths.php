<?php

use OpenApi\Attributes as OA;

class MenuItemPaths
{
    #[OA\Get(
        path: '/menu-items',
        summary: 'List all menu items or only the available ones',
        tags: ['menu-items'],
        parameters: [
            new OA\Parameter(name: 'available', in: 'query', required: false, description: '1 = only the items that can be ordered now', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'All menu items', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuItem')))
        ]
    )]
    public function getMenuItems()
    {
    }

    #[OA\Get(
        path: '/menu-items/{id}',
        summary: 'Get one menu item',
        tags: ['menu-items'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the menu item', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The menu item', content: new OA\JsonContent(ref: '#/components/schemas/MenuItem')),
            new OA\Response(response: 404, description: 'Menu item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getMenuItemsById()
    {
    }

    #[OA\Post(
        path: '/menu-items',
        summary: 'Create a menu item',
        description: 'The category must exist and the price must be between 0.01 and 1000 KM.',
        tags: ['menu-items'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MenuItemInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created menu item', content: new OA\JsonContent(ref: '#/components/schemas/MenuItem')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postMenuItems()
    {
    }

    #[OA\Put(
        path: '/menu-items/{id}',
        summary: 'Update a menu item',
        description: 'Fields that are not sent keep their current value.',
        tags: ['menu-items'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the menu item', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MenuItemInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated menu item', content: new OA\JsonContent(ref: '#/components/schemas/MenuItem')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Menu item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putMenuItemsById()
    {
    }

    #[OA\Delete(
        path: '/menu-items/{id}',
        summary: 'Delete a menu item',
        tags: ['menu-items'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the menu item', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'Menu item deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Menu item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteMenuItemsById()
    {
    }
}
