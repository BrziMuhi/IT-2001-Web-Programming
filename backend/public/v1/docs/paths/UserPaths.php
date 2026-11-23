<?php

use OpenApi\Attributes as OA;

class UserPaths
{
    #[OA\Get(
        path: '/users',
        summary: 'List all users',
        tags: ['users'],
        responses: [
            new OA\Response(response: 200, description: 'All users', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User')))
        ]
    )]
    public function getUsers()
    {
    }

    #[OA\Get(
        path: '/users/{id}',
        summary: 'Get one user',
        tags: ['users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersById()
    {
    }

    #[OA\Post(
        path: '/users',
        summary: 'Create a user',
        description: 'The email must be unique. The password is stored as a hash and never returned. The role is user unless another one is sent.',
        tags: ['users'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postUsers()
    {
    }

    #[OA\Put(
        path: '/users/{id}',
        summary: 'Update a user',
        description: 'Fields that are not sent keep their current value. A new password is optional.',
        tags: ['users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putUsersById()
    {
    }

    #[OA\Delete(
        path: '/users/{id}',
        summary: 'Delete a user',
        tags: ['users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteUsersById()
    {
    }
}
