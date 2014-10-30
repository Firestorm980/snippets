<?php
if ( ! function_exists( 'paging_nav_block' ) ) :
/**
 * paging_nav_block
 *
 * Display navigation to next/previous set of posts when applicable. Used on search, archive pages.
 * 
 * @param  string $type  	Optional. The type of item we're paging through (Posts, Pages, Custom Post Type, etc.)
 * @param  object $query 	Optional. The query object to get our data from. Defaults to global query.
 * @return html 			Return output HTML below.
 */
function paging_nav_block($type = 'Posts', $query = null) {
	global $wp_query;

	// Were we passed a query? If not, use the global one.
	if ( is_null( $query ) ){
		$query = $wp_query;
	}

	// Find the max number of pages for this query
	$max_num_pages = intval($query->max_num_pages);
	// What page are we on?
	$page = intval($query->query_vars['paged']);

	// Don't print empty markup if there's only one page.
	if ( $max_num_pages < 2 )
		return;
	
	// If it's a search page, change our wording. Could be done for any page type.
	if ( is_search() ){
		$next_string = 'Next';
		$prev_string = 'Previous';
	} else {
		$next_string = 'Older '.$type;
		$prev_string = 'Newer '.$type;
	}
	?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( $type.' navigation' ); ?></h1>
			<div class="paging-navigation-links">
				<?php if ( $page < $max_num_pages ) : ?>
					<div class="nav-previous"><?php next_posts_link( __( "<span>".$next_string."</span>" ), $max_num_pages ); ?></div>
				<?php endif; ?>
				<?php if ( $page > 1 ) : ?>
					<div class="nav-next"><?php previous_posts_link( __( "<span>".$prev_string."</span>" ) ); ?></div>
				<?php endif; ?>
			</div><!-- .nav-links -->
			<div class="paging-navigation-pages">
			<?php 
				// Will echo out a set of links to specific pages (1,2,3...) in a row.
				$pagination_link_args = array(
					'base'         => '%_%',
					'format'       => '/page/%#%',
					'total'        => $max_num_pages,
					'current'      => $page,
					'show_all'     => false,
					'prev_next'    => false,
					'end_size' => 1,
					'mid_size' => 2
				);
				echo paginate_links( $pagination_link_args ); 
			?>
			</div>
		</nav><!-- .navigation -->
	<?php
}
endif;
?>