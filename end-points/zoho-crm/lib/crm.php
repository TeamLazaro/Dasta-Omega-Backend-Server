<?php

namespace CRM;

ini_set( "display_errors", 0 );
ini_set( "error_reporting", E_ALL );

require_once __DIR__ . '/../../../vendor/autoload.php';

use CristianPontes\ZohoCRMClient\ZohoCRMClient;
use CristianPontes\ZohoCRMClient\Exception as ZohoException;

/*
 *
 * Declare constants
 *
 */
$authToken = 'd26aa791c15cd144fff5857ad96aeb39';



function getLead ( $phoneNumber/*, $email*/ ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$records = $zohoClient->searchRecords()
					->where( 'Phone', $phoneNumber )
					// ->orWhere( 'Email', $email )
					->request();
		$records = array_values( $records );
	} catch ( Exception | ZohoException\NoDataException $e ) {
		$records = [ ];
	}

	if ( empty( $records ) ) {
		return null;
	}

	if ( count( $records ) > 1 ) {
		throw new Exception( 'More than one lead found with the provided phone number and email.' );
	}

	$existingLead = [
		'SMOWNERID' => $records[ 0 ]->data[ 'SMOWNERID' ],
		'LEADID' => $records[ 0 ]->data[ 'LEADID' ]
	];

	return $existingLead;

}

function getProspect ( $phoneNumber/*, $email*/ ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Contacts', $authToken, 'com', 0 );

	try {
		$records = $zohoClient->searchRecords()
					->where( 'Phone', $phoneNumber )
					// ->orWhere( 'Email', $email )
					->request();
		$records = array_values( $records );
	} catch ( Exception | ZohoException\NoDataException $e ) {
		$records = [ ];
	}

	if ( empty( $records ) ) {
		return null;
	}

	if ( count( $records ) > 1 ) {
		throw new Exception( 'More than one prospect found with the provided phone number and email.' );
	}

	$existingProspect = [
		'SMOWNERID' => $records[ 0 ]->data[ 'SMOWNERID' ],
		'CONTACTID' => $records[ 0 ]->data[ 'CONTACTID' ]
	];

	return $existingProspect;

}

function uploadFileToLead ( $leadId, $pricingSheetURL ) {

	if ( empty( $leadId ) || empty( $pricingSheetURL ) ) {
		return;
	}

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->uploadFile()
				->id( $leadId )
				->attachLink( $pricingSheetURL )
				->request();
		// $apiResponse = array_values( $response );
	} catch ( Exception $e ) {
		throw new Exception( 'Could not upload file to the lead.' );
	}

	return $apiResponse;

}

function uploadFileToProspect ( $prospectId, $pricingSheetURL ) {

	if ( empty( $prospectId ) || empty( $pricingSheetURL ) ) {
		return;
	}

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Contacts', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->uploadFile()
				->id( $prospectId )
				->attachLink( $pricingSheetURL )
				->request();
		// $apiResponse = array_values( $apiResponse );
	} catch ( Exception $e ) {
		throw new Exception( 'Could not upload file to the prospect.' );
	}

	return $apiResponse;

}

function createLead ( $data ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->insertRecords()
					->addRecord( $data )
					->onDuplicateError()
					// ->triggerWorkflow()
					->request();
		$apiResponse = array_values( $apiResponse );
	} catch ( Exception $e ) {
		throw new Exception( 'Could not create a new lead.' );
	}

	return $apiResponse[ 0 ]->id;

}

function createQuote ( $prospect, $enquiry ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Deals', $authToken, 'com', 0 );


	/*
	 * Create the quote
	 */
	$quoteName = $enquiry[ 'unit' ] . ' @ ' . $enquiry[ 'discounted_rate' ];
	if ( $enquiry[ 'carpark_premium_bonus' ] ) {
		if ( $enquiry[ 'carpark_type' ] == 'c' ) {
			$quoteName .= ' + Car Park Downgrade';
		} else {
			$quoteName .= ' + Car Park Upgrade';
		}
	}
	$quoteName .= ' [' . $enquiry[ 'phoneNumber' ] . ']';

	$validTill = date( 'Y-m-d', strtotime( '+ 14 days' ) );
	$price = $enquiry[ 'total_grand' ];
	$email = $enquiry[ 'email' ];


	try {
		$apiResponse = $zohoClient->insertRecords()
				->addRecord( [
					'SMOWNERID' => $prospect[ 'SMOWNERID' ],
					'CONTACTID' => $prospect[ 'CONTACTID' ],
					'Amount' => $price,
					'Deal Name' => $quoteName,
					'Closing Date' => $validTill,
					'Stage' => 'Quote Generated',
					'Email' => $email
				] )
				->onDuplicateError()
				// ->onDuplicateUpdate()
				->triggerWorkflow()
				->request();
		$apiResponse = array_values( $apiResponse );
	} catch ( Exception $e ) {
		throw new Exception( 'Could not create the quote.' );
	}

	/*
	 * Attach the pricing sheet to the quote
	 */
	$quoteId = $apiResponse[ 0 ]->id;
	$pricingSheetURL = $enquiry[ 'pricingSheet' ];

	try {
		$apiResponse = $zohoClient->uploadFile()
			->id( $quoteId )
			->attachLink( $pricingSheetURL )
			->request();
	} catch ( Exception $e ) {
		throw new Exception( 'Could not attach the pricing sheet to the quote.' );
	}

	return $quoteId;

}
