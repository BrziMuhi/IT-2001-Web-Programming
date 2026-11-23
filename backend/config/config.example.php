<?php

class Config
{
    public static function DB_HOST() { return self::get_env('DB_HOST', 'localhost'); }
    public static function DB_PORT() { return self::get_env('DB_PORT', 3306); }
    public static function DB_NAME() { return self::get_env('DB_NAME', 'verdant'); }
    public static function DB_USER() { return self::get_env('DB_USER', 'root'); }
    public static function DB_PASSWORD() { return self::get_env('DB_PASSWORD', ''); }

    public static function JWT_SECRET() { return self::get_env('JWT_SECRET', 'change-me-to-a-long-random-string'); }

    public static function OPEN_METEO_GEOCODING_URL() { return self::get_env('OPEN_METEO_GEOCODING_URL', 'https://geocoding-api.open-meteo.com'); }
    public static function OPEN_METEO_FORECAST_URL() { return self::get_env('OPEN_METEO_FORECAST_URL', 'https://api.open-meteo.com'); }
    public static function OPEN_METEO_TIMEOUT() { return self::get_env('OPEN_METEO_TIMEOUT', 5); }

    public static function get_env($name, $default)
    {
        $value = getenv($name);
        return ($value !== false && trim($value) !== '') ? $value : $default;
    }
}
