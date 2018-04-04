<?php

$provider = require __DIR__ . '/provider.php';

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
		$users = json_decode( file_get_contents( __DIR__ . '/../../data/users.json' ), true );
		$userIds = array_column( $users, 'identifier' );
		if ( in_array( $userId, $userIds ) ) {
			if ( ! isset( $_COOKIE[ 'auth' ] ) ) {
				$cookie = base64_encode( json_encode( [
					'timestamp' => time(),
					'identifier' => $userId,
				] ) );
				setcookie( 'auth', $cookie, time() + 3600, '/' );
			}
			$queryString = http_build_query( [
				'role' => 'executive',
				'name' => $userFirstName
			], null, '&',  PHP_QUERY_RFC3986 );
			header( 'Location: http://139.59.80.92/pricing?' . $queryString );
		} else {
			header( 'Location: http://139.59.80.92/pricing?r=e' );
		}

	} catch ( Exception $e ) {

		// Failed to get user details
		header( 'Location: http://139.59.80.92/pricing?r=' . $e->getMessage() );

	}

}
