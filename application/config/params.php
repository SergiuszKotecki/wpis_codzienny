<?php

// This variable should be fetched from yii component
$baseUri = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

return [
    'wykop_api' => [
        'redirect_url' => 'http://' . $baseUri . '/authenticate',
        'redirect_url_moderators' => 'http://' . $baseUri . '/authenticate/moderator/',
        'app_key' => '',
        'secret_key' => ''
    ]
];
