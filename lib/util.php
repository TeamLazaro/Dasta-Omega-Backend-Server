<?php

namespace Util;

/*
 * Debug output
 */
function log ( $thing ) {
	echo '<pre style="white-space: pre-wrap">';
	var_dump( $thing );
	echo '</pre>';
}

/*
 * Imports the output / contents of a PHP script,
 * in a way such that it can be assigned to a variable
 */
function require_to_var ( $__file__, $ctx = [ ] ) {
	extract( $ctx );
	ob_start();
	require $__file__;
	return ob_get_clean();
}

/*
 * Format a number to an Indian Rupee
 */
function formatNumberToIndianRupee ( $num ) {

	$explrestunits = '';

	if( strlen( $num ) > 3 ) {
		$lastthree = substr( $num, strlen( $num ) - 3, strlen( $num ) );
		// extracts the last three digits
		$restunits = substr( $num, 0, strlen( $num ) - 3 );
		// explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
		$restunits = ( strlen( $restunits ) % 2 == 1 ) ? '0' . $restunits : $restunits;
		$expunit = str_split( $restunits, 2 );
		for( $i=0; $i < sizeof( $expunit );  $i += 1 ) {
			// creates each of the 2's group and adds a comma to the end
			if ( $i == 0 ) {
				// if is first value , convert into integer
				$explrestunits .= (int) $expunit[ $i ] . ',';
			} else {
				$explrestunits .= $expunit[ $i ] . ',';
			}
		}
		$thecash = $explrestunits . $lastthree;
	} else {
		$thecash = $num;
	}

	// writes the final format where $currency is the currency symbol.
	return $thecash;

}
