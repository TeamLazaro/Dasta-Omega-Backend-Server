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
$recordType = $input[ 'recordType' ] ?: null;
if ( empty( $recordType ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No record type was provided.';
	outputError( $response, 400 );
	exit;
}
$recordId = $input[ 'recordId' ] ?: null;
if ( empty( $recordId ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No record id was provided.';
	outputError( $response, 400 );
	exit;
}
$resourcePath = $input[ 'resourcePath' ] ?: null;
if ( empty( $resourcePath ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No path to a resource was provided.';
	outputError( $response, 400 );
	exit;
}





/* ------------------------------------- \
 * Upload or Attach the file / URL
 \-------------------------------------- */
try {

	// Attach the resource to the record
	$apiResponse = CRM::uploadAttachment( $recordType, $recordId, $resourcePath );

	// Construct a response and respond back
	$response = [
		'code' => 1,
		'message' => 'Resource attached to record.',
		// 'data' => $apiResponse[ 'data' ],
		'apiResponse' => $apiResponse
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
