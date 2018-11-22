<?php

/*
 *
 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/
 * SCRIPT SETUP
 * /-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
 *
 */
ini_set( 'display_errors', 0 );
ini_set( 'error_reporting', E_ALL );

// Determine execution environment
if ( http_response_code() === false ) {
	$scriptInterface = 'command-line';
	$isCommandLineInterface = true;
	$isWebInterface = false;
} else {
	$scriptInterface = 'web';
	$isCommandLineInterface = false;
	$isWebInterface = true;
}

// Set the timezone
date_default_timezone_set( 'Asia/Kolkata' );
// Do not let this script timeout
set_time_limit( 0 );

if ( $isCommandLineInterface ) {

	if ( empty( $argv[ 1 ] ) ) {
		$response[ 'message' ] = 'No input provided.';
		fwrite( STDERR, json_encode( $response ) );
		exit( 69 );
	}
	try {
		parse_str( $argv[ 1 ], $input );
	} catch ( Exception $e ) {
		$response[ 'message' ] = 'Error in processing input. ' . $e->getMessage();
		fwrite( STDERR, json_encode( $response ) );
		exit( 69 );
	}

}

if ( $isWebInterface ) {

	// continue processing this script even if
	// the user closes the tab, or
	// hits the ESC key
	ignore_user_abort( true );

	// Allow cross-origin requests
	header( 'Access-Control-Allow-Origin: *' );

	// Respond in JSON format
	header( 'Content-Type: application/json' );

	$input = &$_REQUEST;

}





/*
 *
 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/
 * SCRIPT CORE
 * /-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
 *
 */
require __DIR__ . '/lib/crm.php';




try {

	$phoneNumber = $input[ 'phoneNumber' ];

	if ( empty( $phoneNumber ) ) {
		$response[ 'code' ] = 0;
		throw new Exception( 'No phone number was provided.' );
	}

	// Search for a matching prospect
	$prospect = CRM\getProspect( $phoneNumber );
	if ( ! empty( $prospect ) ) {
		$response[ 'code' ] = 1;
		$response[ 'message' ] = 'Found a prospect.';
		$response[ 'data' ] = [
			'isAlright' => true,
			'id' => $prospect[ 'CONTACTID' ] ?? '',
			'name' => $prospect[ 'Full Name' ] ?? '',
			'firstName' => $prospect[ 'First Name' ] ?? '',
			'lastName' => $prospect[ 'Last Name' ] ?? '',
			'email' => $prospect[ 'Email' ] ?? ''
		];
		die( json_encode( $response ) );
	}

	// If no prospect was found, search for a matching lead
	$lead = CRM\getLead( $phoneNumber );
	if ( ! empty( $lead ) ) {
		$response[ 'code' ] = 1;
		$response[ 'message' ] = 'Found a lead.';
		$response[ 'data' ] = [
			'isAlright' => true,
			'id' => $lead[ 'LEADID' ] ?? '',
			'name' => $lead[ 'Full Name' ] ?? '',
			'firstName' => $lead[ 'First Name' ] ?? '',
			'lastName' => $lead[ 'Last Name' ] ?? '',
			'email' => $lead[ 'Email' ] ?? ''
		];
		die( json_encode( $response ) );
	}


	// If no prospect or lead was found
	$response[ 'code' ] = 2;
	$response[ 'message' ] = 'No matching lead or prospect was found.';
	die( json_encode( $response ) );

} catch ( Exception $e ) {

	// Respond with an error
	$response[ 'code' ] = -1;
	$response[ 'message' ] = $e->getMessage();
	if ( $isCommandLineInterface ) {
		fwrite( STDERR, json_encode( $response ) );
		exit( 69 );
	}
	if ( $isWebInterface ) {
		http_response_code( 500 );
		die( json_encode( $response ) );
	}

}
