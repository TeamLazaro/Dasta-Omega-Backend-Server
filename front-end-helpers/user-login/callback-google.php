<?php

$provider = require __DIR__ . '/provider.php';

$authCode = $_GET[ 'code' ] ?? 0;

if ( $authCode ) {

	$token = $provider->getAccessToken( 'authorization_code', [
		'code' => $authCode
	] );

	// not now
	// $_SESSION[ 'token' ] = serialize( $token );

	try {

		// We got an access token, let's now get the owner details
		$ownerDetails = $provider->getResourceOwner( $token );

		// Use these details to create a new profile
		$userId = $ownerDetails->getId();
		$userProvider = 'Google';

		setcookie( 'auth', 'blah', time() + 3600, '/' );

		header( 'Location: http://fr.om/' );

	} catch ( Exception $e ) {

		// Failed to get user details
		header( 'Location: http://fr.om/?r=e' );

	}

}
