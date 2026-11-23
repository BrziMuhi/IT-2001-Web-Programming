<?php

use OpenApi\Attributes as OA;

class CategoryPaths
{
    #[OA\Get(
        path: '/categories',
        summary: 'List all categories',
        tags: ['categories'],
        responses: [
            new OA\Response(response: 200, description: 'All categories', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Category')))
        ]
    )]
    public function getCategories()
    {
    }

    #[OA\Get(
        path: '/categories/{id}',
        summary: 'Get one category',
        tags: ['categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the category', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategoriesById()
    {
    }

    #[OA\Post(
        path: '/categories',
        summary: 'Create a category',
        description: 'The name must be unique.',
        tags: ['categories'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postCategories()
    {
    }

    #[OA\Put(
        path: '/categories/{id}',
        summary: 'Update a category',
        description: 'Fields that are not sent keep their current value.',
        tags: ['categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the category', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putCategoriesById()
    {
    }

    #[OA\Delete(
        path: '/categories/{id}',
        summary: 'Delete a category',
        tags: ['categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the category', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The category still contains menu items', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteCategoriesById()
    {
    }

    #[OA\Get(
        path: '/categories/{id}/menu-items',
        summary: 'List the menu items of a category',
        tags: ['categories', 'menu-items'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the category', schema: new OA\Schema(type: 'integer'), example: 2)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The menu items of the category', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/MenuItem'))),
            new OA\Response(response: 400, description: 'Invalid id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategoriesByIdMenuItems()
    {
    }
}
