<?php

class Config
{
    public static function DB_HOST(): string { return 'localhost'; }
    public static function DB_PORT(): int { return 3306; }
    public static function DB_NAME(): string { return 'verdant'; }
    public static function DB_USER(): string { return 'root'; }
    public static function DB_PASSWORD(): string { return ''; }

    public static function JWT_SECRET(): string { return 'change-me-to-a-long-random-string'; }

    public static function EXTERNAL_API_KEY(): string { return 'your-api-key-here'; }
}
