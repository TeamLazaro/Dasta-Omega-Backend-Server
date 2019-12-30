<?php

/*
 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
 *  Determine execution environment
 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
 */
class ProgramInterface {

	public static $is;
	public static $isCommandLine;
	public static $isWeb;

	public static function determine () {
		if ( http_response_code() === false ) {
			self::$is = 'command-line';
			self::$isCommandLine = true;
			self::$isWeb = false;
			define( 'PROGRAM_INTERFACE', 'command-line' );
			define( 'IS_COMMANDLINE_INTERFACE', true );
			define( 'IS_WEB_INTERFACE', false );
		} else {
			self::$is = 'web';
			self::$isCommandLine = false;
			self::$isWeb = true;
			define( 'PROGRAM_INTERFACE', 'web' );
			define( 'IS_COMMANDLINE_INTERFACE', false );
			define( 'IS_WEB_INTERFACE', true );
		}
	}

	public static function getInput () {
		// Parse the command-line arguments
		if ( self::$isCommandLine ) {

			$argv = $GLOBALS[ 'argv' ] ?? [ ];
			if ( empty( $argv[ 1 ] ) ) {
				$response[ 'message' ] = 'No input provided.';
				fwrite( STDERR, json_encode( $response ) );
				exit( 400 );
			}
			try {
				parse_str( $argv[ 1 ], $input );
			} catch ( Exception $e ) {
				$response[ 'message' ] = 'Error in processing input. ' . $e->getMessage();
				fwrite( STDERR, json_encode( $response ) );
				exit( 400 );
			}

		}
		// Parse the HTTP request
		else if ( self::$isWeb )
			$input = &$_REQUEST;

		return $input;

	}

}
ProgramInterface::determine();
