<?php

/*
 * This script renders a PDF from a template merged with data using DOMPDF
 */

ini_set( 'display_errors', 'On' );
ini_set( 'error_reporting', E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once 'vendor/autoload.php';
require_once 'lib/util.php';



use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Data that will be fed to the template
$data = [
	'availability' => $_GET[ 'availability' ],
	'basiccost' => Util\formatNumberToIndianRupee( $_GET[ 'basiccost' ] ),
	'basiccost_carpark' => Util\formatNumberToIndianRupee( $_GET[ 'basiccost_carpark' ] ),
	'bhk' => $_GET[ 'bhk' ],
	'carkpark' => Util\formatNumberToIndianRupee( $_GET[ 'carkpark' ] ),
	'carpark_premium_bonus' => Util\formatNumberToIndianRupee( $_GET[ 'carpark_premium_bonus' ] ),
	'carpark_type' => $_GET[ 'carpark_type' ],
	'club_membership' => Util\formatNumberToIndianRupee( $_GET[ 'club_membership' ] ),
	'collapsibleBedroomWall' => $_GET[ 'collapsibleBedroomWall' ],
	'corner_flat' => $_GET[ 'corner_flat' ],
	'cornerflat_charge' => Util\formatNumberToIndianRupee( $_GET[ 'cornerflat_charge' ] ),
	'discount' => Util\formatNumberToIndianRupee( $_GET[ 'discount' ] ),
	'discounted_rate' => Util\formatNumberToIndianRupee( $_GET[ 'discounted_rate' ] ),
	'email' => $_GET[ 'email' ],
	'floor' => $_GET[ 'floor' ],
	'floorise_charge' => Util\formatNumberToIndianRupee( $_GET[ 'floorise_charge' ] ),
	'gardenterrace' => $_GET[ 'gardenterrace' ],
	'gardenterrace_charge' => Util\formatNumberToIndianRupee( $_GET[ 'gardenterrace_charge' ] ),
	'generator_stp' => Util\formatNumberToIndianRupee( $_GET[ 'generator_stp' ] ),
	'gst' => Util\formatNumberToIndianRupee( $_GET[ 'gst' ] ),
	'legal_charges' => Util\formatNumberToIndianRupee( $_GET[ 'legal_charges' ] ),
	'livingDiningSwap' => $_GET[ 'livingDiningSwap' ],
	'maintenance_charges' => Util\formatNumberToIndianRupee( $_GET[ 'maintenance_charges' ] ),
	'mod_a' => Util\formatNumberToIndianRupee( $_GET[ 'mod_a' ] ),
	'mod_b' => Util\formatNumberToIndianRupee( $_GET[ 'mod_b' ] ),
	'mod_c' => Util\formatNumberToIndianRupee( $_GET[ 'mod_c' ] ),
	'mod_d' => Util\formatNumberToIndianRupee( $_GET[ 'mod_d' ] ),
	'mod_toggle_a' => $_GET[ 'mod_toggle_a' ],
	'mod_toggle_b' => $_GET[ 'mod_toggle_b' ],
	'mod_toggle_c' => $_GET[ 'mod_toggle_c' ],
	'mod_toggle_d' => $_GET[ 'mod_toggle_d' ],
	'mod_toggle_e' => $_GET[ 'mod_toggle_e' ],
	'name' => $_GET[ 'name' ],
	'phoneNumber' => $_GET[ 'phoneNumber' ],
	'poojaRoom' => $_GET[ 'poojaRoom' ],
	'rate' => Util\formatNumberToIndianRupee( $_GET[ 'rate' ] ),
	'sft' => $_GET[ 'sft' ],
	'status' => $_GET[ 'status' ],
	'statutory_deposit' => Util\formatNumberToIndianRupee( $_GET[ 'statutory_deposit' ] ),
	'storeRoom' => $_GET[ 'storeRoom' ],
	'total_costofapartment' => Util\formatNumberToIndianRupee( $_GET[ 'total_costofapartment' ] ),
	'total_grand' => Util\formatNumberToIndianRupee( $_GET[ 'total_grand' ] ),
	'total_gross' => Util\formatNumberToIndianRupee( $_GET[ 'total_gross' ] ),
	'unit' => $_GET[ 'unit' ],
	'user' => $_GET[ 'user' ],
];
$markup = Util\require_to_var( 'templates/pricing-sheet.php', $data );
$dompdf->loadHtml( $markup );
$dompdf->render();
$output_filename = 'Pricing Sheets/' . date( 'Y-m-d H.i.s.' ) . '-' . $data[ 'unit' ] . '-' . $data[ 'phoneNumber' ] . '.pdf';
file_put_contents( $output_filename, $dompdf->output() );


$response = [ ];
$response[ 'message' ] = 'Generated the Pricing Sheet';
$response[ 'pricingSheet' ] = 'http://ser.om/' . $output_filename;

// $response = [
// 	'who' => 'the omelette',
// 	'what' => 'let\'s fry this one.'
// ];
// $httpResponseCodes = [ 200, 500 ];
// http_response_code( $httpResponseCodes[ rand( 0, count( $httpResponseCodes ) - 1 ) ] );
// http_response_code( 500 );

die( json_encode( $response ) );
