<?php

ini_set( "display_errors", 'stderr' );
ini_set( "error_reporting", E_ALL );

date_default_timezone_set( 'Asia/Kolkata' );

require_once __DIR__ . '/lib/crm.php';

function logo ( $thing ) {
	echo '<pre>';
	var_dump( $thing );
	echo '</pre>';
}


// $records = CRM\getRecords( 'Leads', [ 'Phone' => 8618095064 ] );
// $records = CRM\getRecords( 'Calls' );
$records = CRM\getRecords( 'Calls', [ 'Subject' => '+918618095064' ] );
// $records = CRM\getRecordById( 'Calls', '2929500000002685032' );
// $records = CRM\getFields( 'Calls' );
logo( $records );
// $calls = CRM\getCalls();
// logo( $calls );
// $response = CRM\associateCallWithLead( '2929500000002690023', '2929500000002706027' );
// logo( $response );
