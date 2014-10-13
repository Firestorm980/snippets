<?php

/**
 * Set up our new iTunes feed.
 */
remove_all_actions( 'do_feed_rss2' );
add_action( 'do_feed_rss2', 'itunes_feed_rss2', 10, 1 );
function itunes_feed_rss2( $default ) {
    $new_itunes_template = get_template_directory() . '/itunes_rss.php';
    if( get_query_var( 'post_type' ) == 'message' and file_exists( $new_itunes_template ) ) {
        load_template( $new_itunes_template );
    } else {
        do_feed_rss2( $default ); // Call default function
    }
}
/**
 * Add our redirect.
 */
add_action('rss2_head', 'redirect_my_podcast');
function redirect_my_podcast(){	
	echo "<itunes:new-feed-url>http://www.emmanuelcovenant.com/feed/?post_type=message</itunes:new-feed-url>\n";
}
/**
 * Add appropriate namespace for redirect.
 */
add_action('rss2_ns', 'add_itunes_namespace_to_theme');
function add_itunes_namespace_to_theme(){	
	echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"'."\n"; 
}

?>