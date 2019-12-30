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
die( json_encode( $_REQUEST ) );
// First, parse the input
$input = ProgramInterface::getInput();
// Check if required input is present and valid
$id = $input[ '_id' ];





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
try {

	$person = CRM::getCustomerById( $id );

	if ( empty( $person ) ) {
		$response = [
			'statusCode' => 1,
			'message' => 'No user with the given ID was found.'
		];
		output( $response );
		exit;
	}

	$data = $input[ 'fields' ];
	// The "Last Name" field is mandatory, hence if it is empty, do not let it through
	if ( empty( $data[ 'Last Name' ] ) )
		unset( $data[ 'Last Name' ] );

	CRM::updateRecord( $person[ 'recordType' ], $id, $data );
	$response = [
		'statusCode' => 0,
		'message' => 'Successfully updated the user.'
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
