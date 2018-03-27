<?php

// header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Credentials: true' );
header( 'Access-Control-Allow-Origin: http://fr.om' );

die( json_encode( $_COOKIE ) );
// die( '{"x":5}' );

?>
