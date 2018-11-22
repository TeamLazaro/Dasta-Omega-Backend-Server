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



function attachFileToLead ( $leadId, $resourceURL ) {

	if ( empty( $leadId ) || empty( $resourceURL ) ) {
		return;
	}

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->uploadFile()
				->id( $leadId )
				->uploadFromPath( $resourceURL )
				->request();
		// $apiResponse = array_values( $response );
	} catch ( Exception $e ) {
		throw new \Exception( 'Could not upload file to the lead.' );
	}

	return $apiResponse;

}
