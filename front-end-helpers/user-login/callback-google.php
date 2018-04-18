<?php

$provider = require __DIR__ . '/provider.php';

$frontendAddress = 'http://dasta.in/';
$authCode = $_GET[ 'code' ] ?? 0;

if ( $authCode ) {

	$token = $provider->getAccessToken( 'authorization_code', [
		'code' => $authCode
	] );

	try {

		// We got an access token, let's now get the owner details
		$ownerDetails = $provider->getResourceOwner( $token );

		// Use these details to create a cookie
		$userProvider = 'Google';
		$userId = $ownerDetails->getId();
		$userFirstName = $ownerDetails->getFirstName();
		$userEmail = $ownerDetails->getEmail();

		// Check if user exists
		$user = getUser( $userId );
		if ( $user ) {
			if ( ! isset( $_COOKIE[ 'auth' ] ) ) {
				$cookie = base64_encode( json_encode( [
					'expires' => time() + 60 * 60 * 9,
					'identifier' => $userId,
					'name' => $user[ 'name' ],
					'email' => $user[ 'email' ]
				] ) );
				// Set a cookie to be valid for 9 hours
				setcookie( 'auth', $cookie, time() + 60 * 60 * 9, '/' );
			}
			header( 'Location: ' . $frontendAddress . 'auth-callback/' . '?t=' . $cookie );
		} else {
			header( 'Location: ' . $frontendAddress . 'quote?r=e' );
		}

	} catch ( Exception $e ) {

		// Failed to get user details
		header( 'Location: ' . $frontendAddress . 'quote?r=' . $e->getMessage() );

	}

}



function getUser ( $id ) {

	$users = json_decode( file_get_contents( __DIR__ . '/../../data/users.json' ), true );
	foreach ( $users as $user ) {
		if ( $user[ 'identifier' ] == $id ) return $user;
	}

	return false;

}
