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
 * CORE SCRIPT CODE
 * /-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
 *
 */
require __DIR__ . '/lib/crm.php';



// Check if required input is present and valid
if ( empty( $input[ 'leadId' ] ) || empty( $input[ 'resourceURL' ] ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No lead ID or resource URL was provided.';
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
$leadId = $input[ 'leadId' ];
$resourceURL = $input[ 'resourceURL' ];

try {

	// Attach resource to the lead
	$lead = CRM\attachFileToLead( $leadId, $resourceURL );

	// Construct a response and respond back
	$response[ 'code' ] = 1;
	$response[ 'data' ] = [
		'id' => $lead->id ?? ''
	];
	$response[ 'message' ] = 'Attached resource to lead.';
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
