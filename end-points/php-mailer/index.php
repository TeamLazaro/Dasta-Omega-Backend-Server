<?php

ini_set( "display_errors", 'stderr' );
ini_set( "error_reporting", E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once __DIR__ . '/lib/templating.php';
require_once __DIR__ . '/lib/mailer.php';





/*
 *
 * Extract and Parse the input
 *
 */
if ( empty( $argv[ 1 ] ) ) {
	$clientResponse[ 'message' ] = 'No input provided.';
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );
}
try {
	parse_str( $argv[ 1 ], $input );
} catch ( Exception $e ) {
	$clientResponse[ 'message' ] = 'Error in processing input. ' . $e->getMessage();
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );
}

$enquiry = $input[ 'enquiry' ];

/*
 *
 * Send a mail with the pricing sheet
 *
 */
// Prepare the envelope
$envelope = [
	'from' => [
		'email' => 'adityabhat@lazaro.in',
		'name' => 'The Omega'
	],
	'to' => [
		'email' => $enquiry[ 'email' ],
		'name' => $enquiry[ 'name' ]
	],
	'subject' => 'Pricing Sheet for #' . $enquiry[ 'unit' ],
	'body' => Templating\render( __DIR__ . '/templates/mail.php', $enquiry ),
	'attachment' => [
		'name' => 'Price sheet for #' . $enquiry[ 'unit' ] . '.pdf',
		'url' => $enquiry[ 'pricingSheet' ]
	]
];
// Send the mail
try {
	$clientResponse[ 'message' ] = Mailer\send( $envelope );
	die( json_encode( $clientResponse ) );
} catch ( Exception $e ) {
	$clientResponse[ 'message' ] = 'The mail could not be sent. ' . $e->getMessage();
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );
}
