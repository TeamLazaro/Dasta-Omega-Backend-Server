<?php

/*
 * This script renders a PDF from a template merged with data using DOMPDF
 */

ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once 'vendor/autoload.php';
require_once 'lib/util.php';



use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Data that will be fed to the template
$data = [
	'availability' => $_GET[ 'availability' ],
	'basiccost' => Util\formatToINR( $_GET[ 'basiccost' ] ),
	'basiccost_carpark' => Util\formatToINR( $_GET[ 'basiccost_carpark' ] ),
	'bhk' => $_GET[ 'bhk' ],
	'carkpark' => Util\formatToINR( $_GET[ 'carkpark' ] ),
	'carpark_premium_bonus' => Util\formatToINR( $_GET[ 'carpark_premium_bonus' ] ),
	'carpark_type' => $_GET[ 'carpark_type' ],
	'club_membership' => Util\formatToINR( $_GET[ 'club_membership' ] ),
	'collapsibleBedroomWall' => $_GET[ 'collapsibleBedroomWall' ],
	'corner_flat' => $_GET[ 'corner_flat' ],
	'cornerflat_charge' => Util\formatToINR( $_GET[ 'cornerflat_charge' ] ),
	'discount' => Util\formatToINR( $_GET[ 'discount' ] ),
	'discounted_rate' => Util\formatToINR( $_GET[ 'discounted_rate' ] ),
	'email' => $_GET[ 'email' ],
	'floor' => $_GET[ 'floor' ],
	'floorise_charge' => Util\formatToINR( $_GET[ 'floorise_charge' ] ),
	'gardenterrace' => $_GET[ 'gardenterrace' ],
	'gardenterrace_charge' => Util\formatToINR( $_GET[ 'gardenterrace_charge' ] ),
	'generator_stp' => Util\formatToINR( $_GET[ 'generator_stp' ] ),
	'gst' => Util\formatToINR( $_GET[ 'gst' ] ),
	'legal_charges' => Util\formatToINR( $_GET[ 'legal_charges' ] ),
	'livingDiningSwap' => $_GET[ 'livingDiningSwap' ],
	'maintenance_charges' => Util\formatToINR( $_GET[ 'maintenance_charges' ] ),
	'mod_collapsable_bedroom_wall' => Util\formatToINR( $_GET[ 'mod_collapsable_bedroom_wall' ] ),
	'mod_living_dining_room_swap' => Util\formatToINR( $_GET[ 'mod_living_dining_room_swap' ] ),
	'mod_pooja_room' => Util\formatToINR( $_GET[ 'mod_pooja_room' ] ),
	'mod_store_room' => Util\formatToINR( $_GET[ 'mod_store_room' ] ),
	'mod_toggle_collapsable_bedroom_wall' => $_GET[ 'mod_toggle_collapsable_bedroom_wall' ],
	'mod_toggle_living_dining_room_swap' => $_GET[ 'mod_toggle_living_dining_room_swap' ],
	'mod_toggle_pooja_room' => $_GET[ 'mod_toggle_pooja_room' ],
	'mod_toggle_store_room' => $_GET[ 'mod_toggle_store_room' ],
	'mod_toggle_car_park' => $_GET[ 'mod_toggle_car_park' ],
	'name' => $_GET[ 'name' ],
	'phoneNumber' => $_GET[ 'phoneNumber' ],
	'poojaRoom' => $_GET[ 'poojaRoom' ],
	'rate' => Util\formatToINR( $_GET[ 'rate' ] ),
	'sft' => $_GET[ 'sft' ],
	'statutory_deposit' => Util\formatToINR( $_GET[ 'statutory_deposit' ] ),
	'storeRoom' => $_GET[ 'storeRoom' ],
	'total_costofapartment' => Util\formatToINR( $_GET[ 'total_costofapartment' ] ),
	'total_grand' => Util\formatToINR( $_GET[ 'total_grand' ] ),
	'total_gross' => Util\formatToINR( $_GET[ 'total_gross' ] ),
	'unit' => $_GET[ 'unit' ],
	'user' => $_GET[ 'user' ],
];
$markup = Util\renderTemplate( 'templates/pricing-sheet.php', $data );
$dompdf->loadHtml( $markup );
$dompdf->render();
$output_filename = 'Pricing Sheets/' . date( 'Y-m-d H.i.s.' ) . '-' . $data[ 'unit' ] . '-' . $data[ 'phoneNumber' ] . '.pdf';
file_put_contents( $output_filename, $dompdf->output() );


$response = [ ];
$response[ 'message' ] = 'Generated the Pricing Sheet';
$response[ 'pricingSheet' ] = 'http://ser.om/' . $output_filename;

die( json_encode( $response ) );
