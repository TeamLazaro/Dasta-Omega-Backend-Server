<?php

ini_set( "display_errors", 'stderr' );
ini_set( "error_reporting", E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

// require_once __DIR__ . '/lib/crm.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use CristianPontes\ZohoCRMClient\ZohoCRMClient;
use CristianPontes\ZohoCRMClient\Exception as ZohoException;





/*
 *
 * Declare constants
 *
 */
$authToken = 'd26aa791c15cd144fff5857ad96aeb39';



function getLeads () {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$records = $zohoClient->getRecords()
					->selectColumns( 'LEADID', 'Phone' )
					->fromIndex( 0 )
					->toIndex( 199 )
					->request();
		$records = array_values( $records );
	} catch ( ZohoException\NoDataException $e ) {

	} catch ( Exception $e ) {
		$records = [ ];
	}

	if ( empty( $records ) ) {
		return null;
	}

	return $records;

}

function updateLead ( $id, $data ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Leads', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->updateRecords()
					->addRecord( array_merge( $data, [ 'Id' => $id ] ) )
					->onDuplicateUpdate()
					->triggerWorkflow()
					->request();
		$apiResponse = array_values( $apiResponse )[ 0 ];
		if ( ! empty( $apiResponse->error ) ) {
			if ( ! empty( $apiResponse->error->description ) ) {
				throw new \Exception( $apiResponse->error->description );
			}
		}
	} catch ( Exception $e ) {
		throw new \Exception( 'Could not update the lead.' );
	}

	return $apiResponse;

}

function getProspects () {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Contacts', $authToken, 'com', 0 );

	try {
		$records = $zohoClient->getRecords()
					->selectColumns( 'CONTACTID', 'Phone' )
					->fromIndex( 0 )
					->toIndex( 199 )
					->request();
		$records = array_values( $records );
	} catch ( ZohoException\NoDataException $e ) {

	} catch ( Exception $e ) {
		$records = [ ];
	}

	if ( empty( $records ) ) {
		return null;
	}

	return $records;

}

function updateProspect ( $id, $data ) {

	global $authToken;
	$zohoClient = new ZohoCRMClient( 'Contacts', $authToken, 'com', 0 );

	try {
		$apiResponse = $zohoClient->updateRecords()
					->addRecord( array_merge( $data, [ 'Id' => $id ] ) )
					->onDuplicateUpdate()
					->triggerWorkflow()
					->request();
		$apiResponse = array_values( $apiResponse )[ 0 ];
		if ( ! empty( $apiResponse->error ) ) {
			if ( ! empty( $apiResponse->error->description ) ) {
				throw new \Exception( $apiResponse->error->description );
			}
		}
	} catch ( Exception $e ) {
		throw new \Exception( 'Could not update the prospect.' );
	}

	return $apiResponse;

}

function logo ( $thing ) {
	echo '<pre>';
	var_dump( $thing );
	echo '</pre>';
}


/*
 * Fetch leads
 */
// $leads = getLeads();
// $leadsWithoutCallingCodePrefix = array_filter( $leads, function ( $lead ) {
// 	$phoneNumber = $lead->data[ 'Phone' ];
// 	// Filter out phone numbers that begin with a `+` symbol, and
// 	if ( strpos( $phoneNumber, '+' ) === 0 ) {
// 		return false;
// 	}
// 	// phone numbers that are not 10 digits long
// 	if ( strlen( $phoneNumber ) != 10 ) {
// 		return false;
// 	}
// 	return true;
// } );

/*
 * Update leads
 */
// foreach ( $leadsWithoutCallingCodePrefix as $lead ) {
// 	$leadId = $lead->data[ 'LEADID' ];
// 	$phoneNumber = '+91' . $lead->data[ 'Phone' ];
// 	try {
// 		$apiResponse = updateLead( $leadId, [ 'Phone' => $phoneNumber ] );
// 	} catch ( Exception $e ) {
// 		logo( $e->getMessage() );
// 	}
// 	logo( $apiResponse );
// 	// logo( $leadId );
// 	// logo( $phoneNumber );
// }

// logo( $leadsWithoutCallingCodePrefix );

/*
 * Fetch prospects
 */
$allProspects = getProspects();
$prospectsWithoutCallingCodePrefix = array_filter( $allProspects, function ( $prospect ) {
	$phoneNumber = $prospect->data[ 'Phone' ];
	// Filter out phone numbers that begin with a `+` symbol, and
	if ( strpos( $phoneNumber, '+' ) === 0 ) {
		return false;
	}
	// phone numbers that are not 10 digits long
	if ( strlen( $phoneNumber ) != 10 ) {
		return false;
	}
	return true;
} );

/*
 * Update prospects
 */
// foreach ( $prospectsWithoutCallingCodePrefix as $prospect ) {
// 	$prospectId = $prospect->data[ 'CONTACTID' ];
// 	$phoneNumber = '+91' . $prospect->data[ 'Phone' ];
// 	try {
// 		$apiResponse = updateProspect( $prospectId, [ 'Phone' => $phoneNumber ] );
// 	} catch ( Exception $e ) {
// 		logo( $e->getMessage() );
// 	}
// 	logo( $apiResponse );
// 	// logo( $prospectId );
// 	// logo( $phoneNumber );
// }

// logo( $allProspects );
logo( $prospectsWithoutCallingCodePrefix );
