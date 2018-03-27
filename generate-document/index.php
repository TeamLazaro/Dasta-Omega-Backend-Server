<?php

/*
 * This script renders a PDF from a template merged with data using DOMPDF
 */

ini_set( 'display_errors', 'stderr' );
ini_set( 'error_reporting', E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/lib/util.php';
require_once __DIR__ . '/lib/templating.php';



/*
 *
 * Extract and Parse the input
 *
 */
if ( empty( $argv[ 1 ] ) ) {
	$clientResponse[ 'message' ] = 'No input provided.';
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );
}
try {
	parse_str( $argv[ 1 ], $input );
} catch ( Exception $e ) {
	$clientResponse[ 'message' ] = 'Error in processing input. ' . $e->getMessage();
	fwrite( STDERR, $clientResponse[ 'message' ] );
	exit( 1 );
}

$enquiry = $input[ 'enquiry' ];



use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Data that will be fed to the template
$data = [
	'availability' => $enquiry[ 'availability' ],
	'basiccost' => Util\formatToINR( $enquiry[ 'basiccost' ] ),
	'basiccost_carpark' => Util\formatToINR( $enquiry[ 'basiccost_carpark' ] ),
	'bhk' => $enquiry[ 'bhk' ],
	'carkpark' => Util\formatToINR( $enquiry[ 'carkpark' ] ),
	'carpark_premium_bonus' => Util\formatToINR( $enquiry[ 'carpark_premium_bonus' ] ),
	'carpark_type' => $enquiry[ 'carpark_type' ],
	'club_membership' => Util\formatToINR( $enquiry[ 'club_membership' ] ),
	'collapsibleBedroomWall' => $enquiry[ 'collapsibleBedroomWall' ],
	'corner_flat' => $enquiry[ 'corner_flat' ],
	'cornerflat_charge' => Util\formatToINR( $enquiry[ 'cornerflat_charge' ] ),
	'discount' => Util\formatToINR( $enquiry[ 'discount' ] ),
	'discounted_rate' => Util\formatToINR( $enquiry[ 'discounted_rate' ] ),
	'email' => $enquiry[ 'email' ],
	'floor' => $enquiry[ 'floor' ],
	'floorise_charge' => Util\formatToINR( $enquiry[ 'floorise_charge' ] ),
	'gardenterrace' => $enquiry[ 'gardenterrace' ],
	'gardenterrace_charge' => Util\formatToINR( $enquiry[ 'gardenterrace_charge' ] ),
	'generator_stp' => Util\formatToINR( $enquiry[ 'generator_stp' ] ),
	'gst' => Util\formatToINR( $enquiry[ 'gst' ] ),
	'legal_charges' => Util\formatToINR( $enquiry[ 'legal_charges' ] ),
	'livingDiningSwap' => $enquiry[ 'livingDiningSwap' ],
	'maintenance_charges' => Util\formatToINR( $enquiry[ 'maintenance_charges' ] ),
	'mod_collapsable_bedroom_wall' => Util\formatToINR( $enquiry[ 'mod_collapsable_bedroom_wall' ] ),
	'mod_living_dining_room_swap' => Util\formatToINR( $enquiry[ 'mod_living_dining_room_swap' ] ),
	'mod_pooja_room' => Util\formatToINR( $enquiry[ 'mod_pooja_room' ] ),
	'mod_store_room' => Util\formatToINR( $enquiry[ 'mod_store_room' ] ),
	'mod_toggle_collapsable_bedroom_wall' => $enquiry[ 'mod_toggle_collapsable_bedroom_wall' ],
	'mod_toggle_living_dining_room_swap' => $enquiry[ 'mod_toggle_living_dining_room_swap' ],
	'mod_toggle_pooja_room' => $enquiry[ 'mod_toggle_pooja_room' ],
	'mod_toggle_store_room' => $enquiry[ 'mod_toggle_store_room' ],
	'mod_toggle_car_park' => $enquiry[ 'mod_toggle_car_park' ],
	'name' => $enquiry[ 'name' ],
	'phoneNumber' => $enquiry[ 'phoneNumber' ],
	'poojaRoom' => $enquiry[ 'poojaRoom' ],
	'rate' => Util\formatToINR( $enquiry[ 'rate' ] ),
	'sft' => $enquiry[ 'sft' ],
	'statutory_deposit' => Util\formatToINR( $enquiry[ 'statutory_deposit' ] ),
	'storeRoom' => $enquiry[ 'storeRoom' ],
	'total_costofapartment' => Util\formatToINR( $enquiry[ 'total_costofapartment' ] ),
	'total_grand' => Util\formatToINR( $enquiry[ 'total_grand' ] ),
	'total_gross' => Util\formatToINR( $enquiry[ 'total_gross' ] ),
	'unit' => $enquiry[ 'unit' ],
	'user' => $enquiry[ 'user' ],
];

try {

	$markup = Templating\render( __DIR__ . '/templates/unit-quotation.php', $data );
	$dompdf->loadHtml( $markup );
	$dompdf->render();
	$output_directory = __DIR__ . '/../media/quotes/';
	$output_filename = date( 'Y-m-d_H.i.s' ) . '__' . $data[ 'unit' ] . '__' . $data[ 'phoneNumber' ] . '.pdf';
	file_put_contents( $output_directory . $output_filename, $dompdf->output() );

	$response[ 'message' ] = 'Generated the Pricing Sheet';
	$response[ 'pricingSheet' ] = 'http://ser.om/media/quotes/' . $output_filename;
	// $response[ 'pricingSheet' ] = $output_directory . $output_filename;

	die( json_encode( $response ) );

} catch ( Exception $e ) {

	$response[ 'message' ] = $e->getMessage();
	fwrite( STDERR, $response[ 'message' ] );
	exit( 1 );

}
