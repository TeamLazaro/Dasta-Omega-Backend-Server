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
$enquiry = $input[ 'enquiry' ];
// Check if required input is present and valid
$uid = $enquiry[ 'uid' ];
if ( empty( $uid ) ) {
	$response[ 'code' ] = 0;
	$response[ 'message' ] = 'No UID was provided.';
	outputError( $response, 400 );
	exit;
}





/* ------------------------------------- \
 * Determine if the Person exists
 \-------------------------------------- */
try {

	// Create the lead
	$person = CRM::getCustomerByUID( $uid );

	if ( empty( $person ) ) {
		$response = [
			'code' => 404,
			'message' => 'No prospect with the given UID exists.'
		];
		outputError( $response, 404 );
	}

} catch ( Exception $e ) {

	// Respond with an error
	$response = [
		'code' => -1,
		'message' => $e->getMessage()
	];

	outputError( $response, 500 );

}



/* ------------------------------------- \
 * Interpret and Prepare the input
 \-------------------------------------- */
$pricingSheetPath = $enquiry[ 'pricingSheet' ];

$quote = [
	'name' => $enquiry[ 'quote_name' ],
	'amount' => $enquiry[ 'total_grand' ],
	'validFor' => 14
];
$person[ 'Email' ] = $enquiry[ 'email' ];

try {

	CRM::uploadAttachment( 'Contacts', $person[ 'id' ], $pricingSheetPath );
	$quoteRecord = CRM::createQuote( $person, $quote );
	CRM::uploadAttachment( 'Deals', $quoteRecord[ 'id' ], $pricingSheetPath );

	$response = [
		'code' => 200,
		'message' => 'Created a new quote for the prospect.'
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
