<?php
/*
 *
 * This script fetches a user based on its (unique) id from the system
 *
 */

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

// header( 'Content-Type: application/json' );

require __DIR__ . '/lib/crm.php';

$id = $_REQUEST[ 'id' ];

try {

	// Search for a matching prospect
	$prospect = CRM\getProspectById( $id );
	if ( ! empty( $prospect ) ) {
		$clientResponse[ 'data' ] = [
			'id' => $prospect[ 'CONTACTID' ] ?? '',
			'name' => $prospect[ 'Full Name' ] ?? '',
			'phoneNumber' => $prospect[ 'Phone' ] ?? '',
			'email' => $prospect[ 'Email' ] ?? ''
		];
		die( json_encode( $clientResponse ) );
	}

	// If no prospect was found, search for a matching lead
	$lead = CRM\getLeadById( $id );
	if ( ! empty( $lead ) ) {
		$clientResponse[ 'data' ] = [
			'id' => $lead[ 'LEADID' ] ?? '',
			'name' => $lead[ 'Full Name' ] ?? '',
			'phoneNumber' => $lead[ 'Phone' ] ?? '',
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
