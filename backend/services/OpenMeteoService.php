<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/exceptions.php';
require_once __DIR__ . '/../utils/Validator.php';

class OpenMeteoService
{
    const MAX_DAYS = 16;

    const DESCRIPTIONS = [
        0 => 'Clear sky',
        1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
        45 => 'Fog', 48 => 'Fog',
        51 => 'Light drizzle', 53 => 'Drizzle', 55 => 'Heavy drizzle', 56 => 'Freezing drizzle', 57 => 'Freezing drizzle',
        61 => 'Light rain', 63 => 'Rain', 65 => 'Heavy rain', 66 => 'Freezing rain', 67 => 'Freezing rain',
        71 => 'Light snow', 73 => 'Snow', 75 => 'Heavy snow', 77 => 'Snow grains',
        80 => 'Light showers', 81 => 'Showers', 82 => 'Heavy showers',
        85 => 'Snow showers', 86 => 'Heavy snow showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm with hail', 99 => 'Thunderstorm with hail'
    ];

    public function forecast($city, $days = 7)
    {
        Validator::required(['city' => $city], ['city']);
        Validator::length($city, 'city', 2, 100);
        Validator::no_html($city, 'city');
        Validator::integer($days, 'days', 1, self::MAX_DAYS);

        $place = $this->find_city(trim($city));

        $data = $this->request(Config::OPEN_METEO_FORECAST_URL() . '/v1/forecast', [
            'latitude' => $place['latitude'],
            'longitude' => $place['longitude'],
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max',
            'timezone' => 'auto',
            'forecast_days' => (int) $days
        ]);

        $daily = $data['daily'] ?? null;
        if (!isset($daily['time'], $daily['weather_code'], $daily['temperature_2m_max'], $daily['temperature_2m_min'])) {
            error_log('Open-Meteo forecast answered without the expected daily data: ' . substr(json_encode($data), 0, 300));
            throw new ServiceUnavailableException('The weather forecast is not available right now.');
        }

        $forecast = [];
        foreach ($daily['time'] as $i => $date) {
            $code = (int) $daily['weather_code'][$i];
            $forecast[] = [
                'date' => $date,
                'weather_code' => $code,
                'description' => self::DESCRIPTIONS[$code] ?? 'Unknown',
                'temperature_max' => $daily['temperature_2m_max'][$i],
                'temperature_min' => $daily['temperature_2m_min'][$i],
                'precipitation_probability' => $daily['precipitation_probability_max'][$i] ?? null
            ];
        }

        return [
            'city' => $place['name'],
            'country' => $place['country'] ?? null,
            'latitude' => $place['latitude'],
            'longitude' => $place['longitude'],
            'days' => $forecast,
            'source' => 'Open-Meteo'
        ];
    }

    private function find_city($city)
    {
        $data = $this->request(Config::OPEN_METEO_GEOCODING_URL() . '/v1/search', [
            'name' => $city,
            'count' => 1,
            'language' => 'en',
            'format' => 'json'
        ]);

        if (empty($data['results'][0])) {
            throw new NotFoundException("City '$city' not found.");
        }
        return $data['results'][0];
    }

    private function request($url, $params)
    {
        $url = $url . '?' . http_build_query($params);

        $context = stream_context_create(['http' => [
            'method' => 'GET',
            'timeout' => (float) Config::OPEN_METEO_TIMEOUT(),
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\n"
        ]]);

        $raw = @file_get_contents($url, false, $context);

        if ($raw === false) {
            error_log("Open-Meteo request failed (no answer or timeout): $url");
            throw new ServiceUnavailableException('The weather forecast is not available right now.');
        }

        preg_match('/\s(\d{3})\s/', $http_response_header[0] ?? '', $match);
        $status = (int) ($match[1] ?? 0);
        $data = json_decode($raw, true);

        if ($status !== 200 || !is_array($data)) {
            error_log("Open-Meteo answered with status $status: " . substr($raw, 0, 300));
            throw new ServiceUnavailableException('The weather forecast is not available right now.');
        }

        return $data;
    }
}
