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
 * Ingest enquiry to Zoho CRM as a new lead, or additional data to an existing lead or prospect.
 *
 *
 * Here's what this script does:
 * 	1. Check if a prospect matching the given details exists.
 * 	2.1. If it does, then update the lead with new information.
 * 	2.2. Then add the pricing sheet as an attachment. END.
 * 	3. If it does not, then check if a lead matching the given details exists.
 * 	4.1. If it does, then update the lead with new information.
 * 	4.2. Then add the pricing sheet as an attachment. END.
 * 	5. If it does not, then create a new lead with the given details.
 * 	6. Append the pricing sheet as an attachment. END.
 *
 */
/*
 *
 * Check if a prospect or lead already exists,
 * and append the pricing sheet to that record
 *
 */
$phoneNumber = $enquiry[ 'phoneNumber' ];
$pricingSheetURL = $enquiry[ 'pricingSheet' ];

try {

	// Split the full-name into a first name and a last name
	$name = preg_replace( '/\s+/', ' ', trim( $enquiry[ 'name' ] ) );
	$names = preg_split( '/\s/', $name );
	$lastName = array_pop( $names );
	$firstName = implode( ' ', $names );

	$personInfo = [
		'First Name' => $firstName,
		'Last Name' => $lastName,
		'Email' => $enquiry[ 'email' ],
		// 'Phone' => $enquiry[ 'phoneNumber' ]
	];

	$prospect = CRM\getProspect( $phoneNumber );

	if ( $prospect ) {
		CRM\updateProspect( $prospect[ 'CONTACTID' ], $personInfo );
		CRM\uploadFileToProspect( $prospect[ 'CONTACTID' ], $pricingSheetURL );
		$clientResponse[ 'message' ] = 'Attached pricing sheet to existing prospect on Zoho.';
		if ( $enquiry[ '_user' ] == 'executive' ) {
			CRM\createQuote( $prospect, $enquiry );
			$clientResponse[ 'message' ] = 'Created a new quote for the existing prospect on Zoho.';
		}
		die( json_encode( $clientResponse ) );
	}
	// If prospect did not exist
	$lead = CRM\getLead( $phoneNumber );
	if ( $lead ) {
		CRM\updateLead( $lead[ 'LEADID' ], $personInfo );
		CRM\uploadFileToLead( $lead[ 'LEADID' ], $pricingSheetURL );
		$clientResponse[ 'message' ] = 'Attached pricing sheet to existing lead on Zoho.';
		die( json_encode( $clientResponse ) );
	}

	/*
	 *
	 * Create a new lead since there's no matching prospect or lead,
	 * and append the pricing sheet to that record
	 *
	 */

	$leadInfo = array_merge( $personInfo, [
		'Lead Source' => $enquiry[ 'source' ],
		'Phone' => $enquiry[ 'phoneNumber' ],
		'Budget' => $enquiry[ 'bhk' ],
		'Discovery Source' => $enquiry[ 'discoverySource' ]
	] );

	$leadId = CRM\createLead( $leadInfo );
	CRM\uploadFileToLead( $leadId, $enquiry[ 'pricingSheet' ] );

	$clientResponse[ 'message' ] = 'Created a new lead and attached the pricing sheet.';

	die( json_encode( $clientResponse ) );

} catch ( Exception $e ) {

	$clientResponse[ 'message' ] = $e->getMessage();
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );

}
