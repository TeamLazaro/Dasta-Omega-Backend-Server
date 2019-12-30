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
if ( empty( $input[ 'phoneNumber' ] ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No phone number was provided.';
	outputError( $response, 500 );
	exit;
}





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
// Build the lead data object
$leadData = [
	'Phone' => $input[ 'phoneNumber' ],
	'Lead Source' => $input[ 'leadSource' ] ?? 'Website',
	'Lead Status' => $input[ 'leadStatus' ] ?? 'Fresh',
	'First Name' => $input[ 'firstName' ] ?? 'First',
	'Last Name' => $input[ 'lastName' ] ?? 'Last'
];

try {

	// Create the lead
	$lead = CRM::createLead( $leadData );

	// Construct a response and respond back
	$response[ 'code' ] = 1;
	$response[ 'data' ] = [
		'id' => $lead->id ?? ''
	];
	$response[ 'message' ] = 'Lead created.';
	output( $response );

} catch ( Exception $e ) {

	// Respond with an error
	$response[ 'code' ] = -1;
	$response[ 'message' ] = $e->getMessage();

	outputError( $response, 500 );

}
