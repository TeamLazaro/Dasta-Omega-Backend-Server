<?php

# * - Date and Timezone
date_default_timezone_set( 'Asia/Kolkata' );

# * - Determine interface medium
require_once __DIR__ . '/program-interface.php';


if ( ProgramInterface::$isWeb ) {
	# * - Request Permissions
	header( 'Access-Control-Allow-Origin: *' );
	# * - Prevent Script Cancellation by Client
	ignore_user_abort( true );
	# * - Script Timeout
	set_time_limit( 0 );
	# * - Respond back in JSON
	header( 'Content-Type: application/json' );
}



/* ------------------------------- \
 * Utility functions
 \-------------------------------- */
function output ( $data, $code = 200 ) {
	http_response_code( $code );
	echo json_encode( $data );
}

function outputError ( $data, $code = 400 ) {

	if ( ProgramInterface::$isCommandLine )
		fwrite( STDERR, json_encode( $response ) );

	else if ( ProgramInterface::$isWeb ) {
		http_response_code( $code );
		echo json_encode( $data );
	}

}
