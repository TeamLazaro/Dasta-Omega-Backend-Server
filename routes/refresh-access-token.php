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
 * Refresh the API access token
 \-------------------------------------- */
try {

	$credentialsFilename = __DIR__ . '/../environment/configuration/zoho.json';
	CRM::refreshAccessToken( $credentialsFilename );

	$response = [
		'code' => 200,
		'message' => 'Successfully renewed the API access token.'
	];
	output( $response );

} catch ( \Exception $e ) {

	// Respond with an error
	$response = [
		'code' => 500,
		'message' => $e->getMessage()
	];
	outputError( $response, 500 );

}
