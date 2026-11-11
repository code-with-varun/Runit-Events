<?php
// config/app.php

$isProduction = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'runito.in') !== false);
$baseUrl = $isProduction ? 'https://runito.in/events' : 'http://localhost:8000';

return [
    'name' => 'Runit Events Hub',
    'tagline' => 'Enterprise Event Registration & Webinar Platform',
    'base_url' => $baseUrl,
    'cert_prefix' => 'CERT',
    'currency' => 'INR',
    'currency_symbol' => '₹',
    'support_email' => 'support@runitevents.com',
    'org_name' => 'Runit Events Hub'
];

