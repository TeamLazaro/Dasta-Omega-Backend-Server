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
$id = $input[ 'id' ] ?: null;
if ( empty( $id ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No Id was provided.';
	outputError( $response, 400 );
	exit;
}





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
try {

	$person = CRM::getCustomerById( $id );

	if ( empty( $person ) ) {
		$response = [
			'code' => 2,
			'message' => 'No matching lead or prospect was found.'
		];
		outputError( $response, 404 );
		exit;
	}

	$response = [
		'code' => 1,
		'message' => 'Found a ' . ( $person[ 'recordType' ] == 'Leads' ? 'lead' : 'prospect' ) . '.',
		'data' => [
			'isAlright' => true,
			'id' => $person[ 'id' ],
			'name' => $person[ 'Full_Name' ],
			'firstName' => $person[ 'First_Name' ] ?? '',
			'lastName' => $person[ 'Last_Name' ],
			'phoneNumber' => $person[ 'Phone' ],
			'email' => $person[ 'Email' ] ?? ''
		]
	];
	if ( ! empty( $person[ 'isProspect' ] ) )
		$response[ 'data' ][ 'isProspect' ] = $person[ 'isProspect' ];

	output( $response );

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'code' => -1,
		'message' => $e->getMessage()
	];
	outputError( $response, 500 );

}
