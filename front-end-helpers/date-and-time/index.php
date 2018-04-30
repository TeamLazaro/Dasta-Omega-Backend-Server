<?php

/*
 *
 * This script returns the current date and time formatted like so,
 * 	`2018-04-30_10-07-59`
 *
 */

ini_set( 'display_errors', 0 );
ini_set( 'error_reporting', E_ALL );

// Set the locale
date_default_timezone_set( 'Asia/Kolkata' );

header( 'Access-Control-Allow-Origin: *' );

// Set the response body format type
header( 'Content-Type: application/json' );

$response[ 'timestamp' ] = date( 'Y-m-d_H-i-s' );
die( json_encode( $response ) );
