<?php
/**
 * my_mce_buttons_2
 *
 * Restores subscript and superscript options to the TinyMCE editor in the admin.
 */
function my_mce_buttons_2($buttons) {	
	$buttons[] = 'superscript';
	$buttons[] = 'subscript';
	return $buttons;
}
add_filter('mce_buttons_2', 'my_mce_buttons_2');
?>