<?php

use OpenApi\Attributes as OA;

class ContactMessagePaths
{
    #[OA\Get(
        path: '/contact-messages',
        summary: 'List all contact messages',
        tags: ['contact-messages'],
        responses: [
            new OA\Response(response: 200, description: 'All contact messages', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ContactMessage')))
        ]
    )]
    public function getContactMessages()
    {
    }

    #[OA\Get(
        path: '/contact-messages/{id}',
        summary: 'Get one contact message',
        tags: ['contact-messages'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the contact message', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The contact message', content: new OA\JsonContent(ref: '#/components/schemas/ContactMessage')),
            new OA\Response(response: 404, description: 'Message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getContactMessagesById()
    {
    }

    #[OA\Post(
        path: '/contact-messages',
        summary: 'Create a contact message',
        description: 'Visitors without an account can send a message; user_id is optional. HTML is not allowed.',
        tags: ['contact-messages'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ContactMessageInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created contact message', content: new OA\JsonContent(ref: '#/components/schemas/ContactMessage')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postContactMessages()
    {
    }

    #[OA\Put(
        path: '/contact-messages/{id}',
        summary: 'Update a contact message',
        description: 'Fields that are not sent keep their current value.',
        tags: ['contact-messages'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the contact message', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ContactMessageInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated contact message', content: new OA\JsonContent(ref: '#/components/schemas/ContactMessage')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putContactMessagesById()
    {
    }

    #[OA\Delete(
        path: '/contact-messages/{id}',
        summary: 'Delete a contact message',
        tags: ['contact-messages'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the contact message', schema: new OA\Schema(type: 'integer'), example: 1)
        ],
        responses: [
            new OA\Response(response: 200, description: 'Message deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteContactMessagesById()
    {
    }

    #[OA\Get(
        path: '/users/{id}/contact-messages',
        summary: 'List the contact messages of a user',
        tags: ['users', 'contact-messages'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', schema: new OA\Schema(type: 'integer'), example: 2)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The messages of the user', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ContactMessage'))),
            new OA\Response(response: 400, description: 'Invalid id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersByIdContactMessages()
    {
    }
}
