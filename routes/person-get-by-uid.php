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
$uid = $input[ 'uid' ] ?: null;
if ( empty( $uid ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No UID was provided.';
	outputError( $response, 400 );
	exit;
}





/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
try {

	$person = CRM::getCustomerByUID( $uid );

	if ( empty( $person ) ) {
		$response = [
			'code' => 2,
			'message' => 'No prospect with the given UID was found.'
		];
		outputError( $response, 404 );
		exit;
	}

	$response = [
		'code' => 1,
		'message' => 'Found a prospect.',
		'data' => [
			'isAlright' => true,
			'id' => $person[ 'id' ],
			'uid' => $person[ 'UID' ],
			'name' => $person[ 'Full_Name' ],
			'firstName' => $person[ 'First_Name' ] ?? '',
			'lastName' => $person[ 'Last_Name' ],
			'coApplicantName' => $person[ 'Secondary_Contact' ],
			'phoneNumber' => $person[ 'Phone' ],
			'email' => $person[ 'Email' ] ?? ''
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
