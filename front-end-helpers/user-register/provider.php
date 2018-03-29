<?php

require __DIR__ . '/../../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

// Replace these with your token settings
// Create a project at https://console.developers.google.com/
$clientId = '1066996965552-1g2sjgilmcsrvpehm2tpqofd5vm2rct9.apps.googleusercontent.com';
$clientSecret = '2eqrrzCmn0HRalWawFgXdWiW';
// Change this if you are not using the built-in PHP server
$redirectUri = 'http://ser.om/register/google/callback';

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
