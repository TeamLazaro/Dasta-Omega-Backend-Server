<?php

$provider = require __DIR__ . '/provider.php';

$authUrl = $provider->getAuthorizationUrl();
$provider->getState();

header( 'Location: ' . $authUrl );

exit;
