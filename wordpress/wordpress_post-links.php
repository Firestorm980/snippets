<?php
/**
 * post_link_attributes description
 *
 * Adds a class to the post links. Also would allow change of code of link.
 */
function post_link_attributes($output) {
    $code = 'class=""';

    $newOutput = str_replace('<a href=', '<a '.$code.' href=', $output);
    $newOutput = str_replace('">', '"><span>', $newOutput);
    $newOutput = str_replace('</a>', '</span></a>', $newOutput);

    return $newOutput;
}
add_filter('next_post_link', 'post_link_attributes');
add_filter('previous_post_link', 'post_link_attributes');

/**
 * posts_link_attributes description
 *
 * Adds a class to the posts links.
 */
function posts_link_attributes() {
    return 'class=""';
}
add_filter('next_posts_link_attributes', 'posts_link_attributes');
add_filter('previous_posts_link_attributes', 'posts_link_attributes');

?>