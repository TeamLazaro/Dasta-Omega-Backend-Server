<?php

ini_set( "display_errors", 1 );
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


if ( empty( $_REQUEST[ 'phoneNumber' ] ) ) {
	$clientResponse[ 'message' ] = 'No phone number was provided.';
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );
}

$phoneNumber = $_REQUEST[ 'phoneNumber' ];
// $unit = $_REQUEST[ 'unit' ];
$firstName = $_REQUEST[ 'firstName' ];
$lastName = $_REQUEST[ 'lastName' ];
$leadData = [
	'Lead Status' => 'Fresh',
	'Lead Source' => 'Web Pricing',
	'First Name' => $firstName,
	'Last Name' => $lastName,
	'Phone' => $phoneNumber
];

try {

	// Create the lead
	$lead = CRM\createLead( $leadData );

	$clientResponse[ 'data' ] = [
		'id' => $lead[ 'LEADID' ] ?? '',
		'name' => $lead[ 'Full Name' ] ?? '',
		'email' => $lead[ 'Email' ] ?? ''
	];
	$clientResponse[ 'message' ] = 'Lead created.';
	die( json_encode( $clientResponse ) );

} catch ( Exception $e ) {

	// Respond with an error
	$clientResponse[ 'message' ] = $e->getMessage();
	http_response_code( 500 );
	die( json_encode( $clientResponse ) );

}
