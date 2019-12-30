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
$phoneNumber = $input[ 'phoneNumber' ];
if ( empty( $phoneNumber ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No phone number was provided.';
	outputError( $response, 500 );
	exit;
}





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
// Build the lead data object
$data = $input;
$data[ 'project' ] = 'Dasta Concerto';
$data[ 'context' ] = $data[ 'leadSource' ] ?? 'Website';

try {

	// Create the lead
	$person = CRM::createLead( $data );

	$response = [
		'code' => 1,
		'message' => 'Lead created.',
		'data' => [
			'id' => $person[ '_id' ]
		]
	];
	output( $response );

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'code' => -1,
		'message' => $e->getMessage()
	];

	outputError( $response, 500 );

}
