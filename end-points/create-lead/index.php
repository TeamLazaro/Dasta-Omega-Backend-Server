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



// Check if required input is present and valid
if ( empty( $input[ 'phoneNumber' ] ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No phone number was provided.';
	if ( $isCommandLineInterface ) {
		fwrite( STDERR, json_encode( $response ) );
		exit( 69 );
	}
	if ( $isWebInterface ) {
		http_response_code( 500 );
		die( json_encode( $response ) );
	}
}

// Pull all the input data
$phoneNumber = $input[ 'phoneNumber' ];
// $unit = $input[ 'unit' ];
$firstName = $input[ 'firstName' ] ?? 'lazaro test';
$lastName = $input[ 'lastName' ] ?? 'hia there';
$leadStatus = $input[ 'leadStatus' ] ?? 'Fresh';
$leadSource = $input[ 'leadSource' ] ?? 'Website';
$leadData = [
	'Lead Status' => $leadStatus,
	'Lead Source' => $leadSource,
	'First Name' => $firstName,
	'Last Name' => $lastName,
	'Phone' => $phoneNumber
];

try {

	// Create the lead
	$lead = CRM\createLead( $leadData );

	// Construct a response and respond back
	$response[ 'code' ] = 1;
	$response[ 'data' ] = [
		'id' => $lead->id ?? ''
	];
	$response[ 'message' ] = 'Lead created.';
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
