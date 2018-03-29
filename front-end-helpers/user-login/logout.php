<?php

// Invalidate the cookie
setcookie( 'auth', '', time() - 3600, '/' );

// Re-direct back to the front-end
header( 'Location: http://fr.om/' );
