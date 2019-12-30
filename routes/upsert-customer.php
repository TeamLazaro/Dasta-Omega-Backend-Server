<?php





/* ------------------------------- \
 * Script Bootstrapping
 \-------------------------------- */
require_once __DIR__ . '/../lib/init.php';
# * - Error Reporting
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );





/* ------------------------------------- \
 * Pull in the dependencies
 \-------------------------------------- */
require_once __DIR__ . '/../lib/crm.php';





/* ------------------------------------- \
 * Input Validation
 \-------------------------------------- */
// First, parse the input
$input = ProgramInterface::getInput();
// Check if required input is present and valid
$enquiry = $input[ 'enquiry' ] ?: null;
if ( empty( $enquiry ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No enquiry was provided.';
	outputError( $response, 400 );
	exit;
}

// Check if required input is present and valid
$phoneNumber = $enquiry[ 'phoneNumber' ] ?: null;
if ( empty( $phoneNumber ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No phone number was provided.';
	outputError( $response, 400 );
	exit;
}





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
$name = preg_replace( '/\s+/', ' ', trim( $enquiry[ 'name' ] ?? '' ) );
$names = preg_split( '/\s/', $name );
$lastName = array_pop( $names );
$firstName = implode( ' ', $names );
$personData = [
	'First_Name' => $firstName,
	'Last_Name' => $lastName,
	'Email' => $enquiry[ 'email' ]
];
try {

	$person = CRM::getCustomerByPhoneNumber( $phoneNumber );
	$recordType = $person[ 'recordType' ] ?? 'Leads';

	if ( ! empty( $person ) ) {
		CRM::updateRecord( $recordType, $person[ 'id' ], $personData );
		$responseMessage = 'Updated the ' . ( $recordType == 'Leads' ? 'lead' : 'prospect' );
	}
	else {
		$personData[ 'Phone' ] = $enquiry[ 'phoneNumber' ] ?? '';
		$personData[ 'Lead_Source' ] = $enquiry[ 'source' ] ?? '';
		$personData[ 'Budget' ] = $enquiry[ 'bhk' ] ?? '';
		$personData[ 'Discovery_Source' ] = $enquiry[ 'discoverySource' ] ?? '';
		$person = CRM::createLead( $personData );
		$responseMessage = 'Created a new lead';
	}

	$resourcePath = $enquiry[ 'pricingSheet' ] ?? null;
	if ( ! empty( $resourcePath ) ) {
		$resourcePath = parse_url( $resourcePath, PHP_URL_PATH ) ?: $resourcePath;
		$resourcePath = __DIR__ . '/../..' . $resourcePath;
		CRM::uploadAttachment( $recordType, $person[ 'id' ], $resourcePath );
		$responseMessage .= ' and attached the pricing sheet';
	}

	$responseMessage .= '.';

	output( [
		'message' => $responseMessage
	] );

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'code' => -1,
		'message' => $e->getMessage()
	];
	outputError( $response, 500 );

}
