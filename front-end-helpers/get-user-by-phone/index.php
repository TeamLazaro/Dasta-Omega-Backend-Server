<?php

ini_set( 'display_errors', 0 );
ini_set( 'error_reporting', E_ALL );

header( 'Access-Control-Allow-Origin: *' );

date_default_timezone_set( 'Asia/Kolkata' );

// continue processing this script even if
// the user closes the tab, or
// hits the ESC key
ignore_user_abort( true );

// do not let this script timeout
set_time_limit( 0 );

header( 'Content-Type: application/json' );

require __DIR__ . '/lib/crm.php';

$phoneNumber = $_REQUEST[ 'phoneNumber' ];

try {

	// Search for a matching prospect
	$prospect = CRM\getProspect( $phoneNumber );
	if ( ! empty( $prospect ) ) {
		$clientResponse[ 'data' ] = [
			'id' => $prospect[ 'CONTACTID' ] ?? '',
			'name' => $prospect[ 'Full Name' ] ?? '',
			'email' => $prospect[ 'Email' ] ?? ''
		];
		die( json_encode( $clientResponse ) );
	}

	// If no prospect was found, search for a matching lead
	$lead = CRM\getLead( $phoneNumber );
	if ( ! empty( $lead ) ) {
		$clientResponse[ 'data' ] = [
			'id' => $lead[ 'LEADID' ] ?? '',
			'name' => $lead[ 'Full Name' ] ?? '',
			'email' => $lead[ 'Email' ] ?? ''
		];
		die( json_encode( $clientResponse ) );
	}

	// If no prospect or lead was found
	$clientResponse[ 'message' ] = 'No matching lead or prospect was found.';
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );

} catch ( Exception $e ) {

	// Respond with an error
	$clientResponse[ 'message' ] = $e->getMessage();
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );

}
