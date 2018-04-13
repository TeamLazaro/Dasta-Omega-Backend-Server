<?php

ini_set( "display_errors", 'stderr' );
ini_set( "error_reporting", E_ALL );

header( 'Access-Control-Allow-Origin: *' );

date_default_timezone_set( 'Asia/Kolkata' );

// continue processing this script even if
// the user closes the tab, or
// hits the ESC key
ignore_user_abort( true );

// do not let this script timeout
set_time_limit( 0 );


// Set the response body format type
header( 'Content-Type: application/json' );


require_once __DIR__ . '/lib/crm.php';


$uid = $_REQUEST[ 'uid' ];

try {

	// Search for a matching prospect
	$prospect = CRM\getProspectByUID( $uid );
	if ( ! empty( $prospect ) ) {
		$clientResponse[ 'data' ] = [
			'uid' => $uid,
			'name' => $prospect[ 'Full Name' ] ?? '',
			'firstName' => $prospect[ 'First Name' ] ?? '',
			'lastName' => $prospect[ 'Last Name' ] ?? '',
			'email' => $prospect[ 'Email' ] ?? ''
		];
		die( json_encode( $clientResponse ) );
	}

	// If no prospect or lead was found
	$clientResponse[ 'message' ] = 'No prospect with the given UID was found.';
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );

} catch ( Exception $e ) {

	// Respond with an error
	$clientResponse[ 'message' ] = $e->getMessage();
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );

}
