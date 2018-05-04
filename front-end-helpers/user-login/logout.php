<?php

// Invalidate the cookie
setcookie( 'auth', '', time() - 999, '/' );

// Re-direct back to the front-end, else leave a message
// if ( ! empty( $_GET[ 'redirect' ] ) ) {
// 	header( 'Location: ' . $_GET[ 'redirect' ] );
// } else {
// 	echo "You've been logged out.";
// }
$redirectTo = $_GET[ 'redirect' ] ?? 'http://dasta.in/';

?>
<!DOCTYPE html>
<html>

<head>
  	<meta http-equiv="refresh" content="0; URL=<?php echo $redirectTo; ?>" />
	<title>Logging out of Dasta.....</title>
</head>

<body>

	<script type="text/javascript">

		window.location = "<?php echo $redirectTo; ?>";

	</script>

</body>

</html>
