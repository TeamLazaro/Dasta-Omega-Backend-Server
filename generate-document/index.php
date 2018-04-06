<?php

/*
 * This script renders a PDF from a template merged with data using DOMPDF
 */

ini_set( 'display_errors', 0 );
ini_set( 'error_reporting', E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once __DIR__ . '/lib/mPDF/mpdf.php';

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




try {

	$mpdf = new mPDF( 'en', 'A4', 16, 'Helvetica' );
	$mpdf->dpi = 144;
	$footer = [
		// 'L' => [
		// 	'content' => 'PAGE {PAGENO} of {nbpg}',
		// 	'font-size' => 8,
		// 	'font-style' => '',
		// 	'font-family' => 'Helvetica',
		// 	'color' => '#000000'
		// ],
		'C' => [
			'content' => '',
			'font-size' => 8,
			'font-style' => '',
			'font-family' => 'Helvetica',
			'color' => '#000000'
		],
		// 'R' => [
		// 	'content' => 'Generated on {DATE jS, F Y}',
		// 	'font-size' => 8,
		// 	'font-style' => '',
		// 	'font-family' => 'Helvetica',
		// 	'color' => '#000000'
		// ],
		'line' => 0
	];
	$mpdf->SetFooter( $footer, 'O' );
	$mpdf->AddPage();

	$stylesheet = file_get_contents( __DIR__ . '/templates/unit-quotation.css' );
	$mpdf->WriteHTML( $stylesheet, 1 );

	$markup = Templating\render( __DIR__ . '/templates/unit-quotation.php', $enquiry );
	$mpdf->WriteHTML( $markup, 2 );

	$output_directory = __DIR__ . '/../media/quotes/';
	$output_filename = date( 'Y-m-d_H.i.s' ) . '__' . $enquiry[ 'unit' ] . '__' . $enquiry[ 'phoneNumber' ] . '.pdf';
	$mpdf->Output( $output_directory . $output_filename, 'F' );

	$response[ 'message' ] = 'Generated the Pricing Sheet';
	$response[ 'pricingSheet' ] = $enquiry[ '_hostname' ] . '/media/quotes/' . $output_filename;

	die( json_encode( $response ) );

} catch ( Exception $e ) {

	$response[ 'message' ] = $e->getMessage();
	fwrite( STDERR, $response[ 'message' ] );
	exit( 1 );

}
