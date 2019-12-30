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
$id = $input[ '_id' ];





/* ------------------------------------- \
 * Check if the Person exists or not
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

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'statusCode' => 1,
		'message' => $e->getMessage()
	];
	outputError( $response, 500 );
	exit;

}



/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
// Extract the changeset, replacing all spaces in the field names with an underscore
// 	( for compatibility with the frontend )
$formattedFieldNames = array_map( function ( $name ) {
	return preg_replace( '/\s+/', '_', $name );
}, array_keys( $input[ 'fields' ] ) );
$data = array_combine( $formattedFieldNames, array_values( $input[ 'fields' ] ) );
// The "Last Name" field is mandatory (on Zoho's end)
// 	hence if it is empty, do not let it through
if ( empty( trim( $data[ 'Last_Name' ] ) ) )
	unset( $data[ 'Last_Name' ] );



/* ------------------------------------- \
 * Update the Person
 \-------------------------------------- */
try {

	$record = CRM::updateRecord( $person[ 'recordType' ], $id, $data );
	die( json_encode( $record ) );

	$response = [
		'statusCode' => 0,
		'message' => 'Successfully updated the user.'
	];
	output( $response );

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'statusCode' => 1,
		'message' => $e->getMessage()
	];
	outputError( $response, 500 );

}
