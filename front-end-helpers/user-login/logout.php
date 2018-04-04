<?php

// Invalidate the cookie
setcookie( 'auth', '', time() - 3600, '/' );

// Re-direct back to the front-end, else leave a message
if ( ! empty( $_GET[ 'redirect' ] ) ) {
	header( 'Location: ' . $_GET[ 'redirect' ] );
} else {
	echo "You've been logged out.";
}
