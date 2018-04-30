<?php

ini_set( "display_errors", 'stderr' );
ini_set( "error_reporting", E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once __DIR__ . '/lib/crm.php';





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
 * Ingest enquiry to Zoho CRM as a new quote.
 *
 *
 * Here's what this script does:
 * 	1. Check if a prospect matching the given UID exists.
 * 	2. If it does, then add the pricing sheet as an attachment.
 * 	3. Create a quote for the prospect. END.
 *
 */
/*
 *
 * Check if a prospect with the given UID exists.
 *
 */

try {

	$uid = $enquiry[ 'uid' ];
	$prospect = CRM\getProspectByUID( $uid );

	if ( $prospect ) {
		$pricingSheetURL = $enquiry[ 'pricingSheet' ];
		CRM\uploadFileToProspect( $prospect[ 'CONTACTID' ], $pricingSheetURL );
		CRM\createQuote( $prospect, $enquiry, $enquiry[ 'quote_name' ]  );
		$clientResponse[ 'message' ] = 'Created a new quote for the prospect.';
		die( json_encode( $clientResponse ) );
	}

	$clientResponse[ 'message' ] = 'No prospect with the given UID exists.';

	die( json_encode( $clientResponse ) );

} catch ( Exception $e ) {

	$clientResponse[ 'message' ] = $e->getMessage();
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );

}
