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
function renderTemplate ( $__file__, $ctx = [ ] ) {
	extract( $ctx );
	ob_start();
	require $__file__;
	return ob_get_clean();
}

/*
 * Format a number to the Indian Rupee currency
 */
function formatToINR ( $number ) {

	$number = round( $number, 2 );

	[ $integerPart, $fractionalPart ] = array_merge( explode( '.', $number ), [ '' ] );

	$lastThreeDigits_integerPart = substr( $integerPart, -3 );
	$allButlastThreeDigits_integerPart = substr( $integerPart, 0, -3 );

	$formattedNumber = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $allButlastThreeDigits_integerPart );

	if ( ! empty( $allButlastThreeDigits_integerPart ) ) {
		$formattedNumber .= ',';
	}
	$formattedNumber .= $lastThreeDigits_integerPart;

	// // Add in the fractional part, if there is one
	if ( ! empty( $fractionalPart ) ) {
		$formattedNumber .= '.' . $fractionalPart;
	}

	return $formattedNumber;

}
