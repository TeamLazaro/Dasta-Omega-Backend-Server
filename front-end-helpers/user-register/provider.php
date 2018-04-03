<?php

require __DIR__ . '/../../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

// Replace these with your token settings
// Create a project at https://console.developers.google.com/
$clientId = '126828542038-kn6jjbg3ln6t6h93mjohg299a164gi3i.apps.googleusercontent.com';
$clientSecret = '0uMrUjXIIVjHq9zd1MAGGv9X';
// Change this if you are not using the built-in PHP server
$redirectUri = 'http://dasta.omega.lazaro.in/register/google/callback';

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
