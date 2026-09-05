<?php

session_start();

date_default_timezone_set("Asia/Kolkata");

if (!defined('BASE_URL')) {
    if (getenv('BASE_URL')) {
        define("BASE_URL", rtrim(getenv('BASE_URL'), '/') . '/');
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/reporting') !== false) {
            define("BASE_URL", $protocol . $host . "/reporting/");
        } else {
            define("BASE_URL", $protocol . $host . "/");
        }
    } else {
        define("BASE_URL", "https://just24you.com/reporting/");
    }
}

define("PER_KM_RATE",4);
