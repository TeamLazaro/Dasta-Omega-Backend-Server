<?php

namespace Templating;

/*
 *
 * Renders a PHP template given a data context and returns the output as a string
 *
 */
function render ( $template, $context = [ ] ) {
	extract( $context );
	ob_start();
	require $template;
	return ob_get_clean();
}
