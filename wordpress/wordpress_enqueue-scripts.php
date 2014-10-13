<?php
/**
 * Enqueue scripts and styles for the front end.
 *
 * Make sure to add the various files to their locations within the theme.
 */
function theme_scripts() {

	// Register scripts
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", false, null, true);
	wp_register_script('jquery_fallback', get_template_directory_uri() . "/js/jquery-fallback.js", array('jquery'), null, true);
	wp_register_script('modernizr', get_template_directory_uri() . "/js/min/modernizr.min.js", false, null, false);
	wp_register_script('theme-plugins', get_template_directory_uri() . "/js/plugins.js", array('jquery'), null, true);
	wp_register_script('theme-script',  get_template_directory_uri() . "/js/script.js", array('jquery','theme-plugins'), null, true);
	wp_localize_script('theme-script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  // For AJAX goodness.  

	// Queue scripts
	wp_enqueue_script('modernizr');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery_fallback');
	wp_enqueue_script('theme-plugins');
	wp_enqueue_script('theme-script');

	// Load our main stylesheets
	wp_enqueue_style( 'theme-style', get_stylesheet_uri() ); // Theme default CSS
	wp_enqueue_style( 'theme-icons', get_template_directory_uri() . '/css/theme-icons.css', false );
	wp_enqueue_style( 'theme-fonts', get_template_directory_uri() . '/css/families.css', false );
	wp_enqueue_style( 'theme-main', get_template_directory_uri() . '/css/main.css', array( 'theme-style', 'theme-icons', 'theme-fonts' ) );
	wp_enqueue_style( 'theme-mq', get_template_directory_uri() . '/css/mq.css', array( 'theme-main' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

?>