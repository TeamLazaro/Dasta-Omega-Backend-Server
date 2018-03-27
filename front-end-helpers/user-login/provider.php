<?php

require __DIR__ . '/../../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

// Replace these with your token settings
// Create a project at https://console.developers.google.com/
$clientId = '1066996965552-r9ju3a0r3tkjdub4fln5kog3iagc2kcp.apps.googleusercontent.com';
$clientSecret = 'VCGAShkPXCOAnMi3IVHZKq_-';
// Change this if you are not using the built-in PHP server
$redirectUri = 'https://0979ad5c.ngrok.io/login/callback';

// Start the session
session_start();

// Initialize the provider
$provider = new Google( [
	'clientId' => $clientId,
	'clientSecret' => $clientSecret,
	'redirectUri' => $redirectUri
] );

// No HTML for demo, prevents any attempt at XSS
header( 'Content-Type', 'text/plain' );

return $provider;
