<?php
// config/database.php

$isProduction = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'runito.in') !== false);

if ($isProduction) {
    return [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'u935057905_runit_aios',
        'username' => 'u935057905_runit_aios',
        'password' => 'Maruthuvk@2026',
        'charset' => 'utf8mb4'
    ];
}

return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'runit_events_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

