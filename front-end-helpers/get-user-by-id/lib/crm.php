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



function getLeadById ( $id ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$record = $zohoClient->getRecordById()
					->id( $id )
					->request();
		$record = array_values( $record )[ 0 ];
	} catch ( ZohoException\NoDataException $e ) {
		$record = [ ];
	} catch ( Exception $e ) {
		$record = [ ];
	}

	if ( empty( $record ) ) {
		return null;
	}

	$existingLead = [
		'SMOWNERID' => $record->data[ 'SMOWNERID' ],
		'LEADID' => $record->data[ 'LEADID' ],
		'Phone' => $record->data[ 'Phone' ],
		'Full Name' => $record->data[ 'Full Name' ],
		'Email' => $record->data[ 'Email' ]
	];

	return $existingLead;

}

function getProspectById ( $id ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Contacts', $authToken, 'com', 0 );

	try {
		$record = $zohoClient->getRecordById()
					->id( $id )
					->request();
		$record = array_values( $record )[ 0 ];
	} catch ( ZohoException\NoDataException $e ) {
		$record = [ ];
	} catch ( Exception $e ) {
		$record = [ ];
	}

	if ( empty( $record ) ) {
		return null;
	}

	$existingProspect = [
		'SMOWNERID' => $record->data[ 'SMOWNERID' ],
		'CONTACTID' => $record->data[ 'CONTACTID' ],
		'Phone' => $record->data[ 'Phone' ],
		'Full Name' => $record->data[ 'Full Name' ],
		'Email' => $record->data[ 'Email' ]
	];

	return $existingProspect;

}
