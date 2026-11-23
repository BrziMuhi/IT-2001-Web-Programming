<?php

use OpenApi\Attributes as OA;

class WeatherPaths
{
    #[OA\Get(
        path: '/weather/forecast',
        summary: 'Weather forecast for a city',
        description: 'Uses the external API Open-Meteo: first the city is looked up (geocoding), then its forecast is loaded. If Open-Meteo is slow or down, the answer is 503 and the rest of the API keeps working.',
        tags: ['weather'],
        parameters: [
            new OA\Parameter(name: 'city', in: 'query', required: true, description: 'Name of the city (2-100 characters)', schema: new OA\Schema(type: 'string'), example: 'Vitez'),
            new OA\Parameter(name: 'days', in: 'query', required: false, description: 'Number of days, 1-16 (default 7)', schema: new OA\Schema(type: 'integer'), example: 3)
        ],
        responses: [
            new OA\Response(response: 200, description: 'The forecast for the city', content: new OA\JsonContent(ref: '#/components/schemas/WeatherForecast')),
            new OA\Response(response: 400, description: 'Missing or invalid city or days', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'City not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'Open-Meteo is not available right now', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWeatherForecast()
    {
    }
}
