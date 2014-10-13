<?php
/**
 * Creates a custom nav walker so that we can add the subheadings to top level links in the main menu.
 * From: http://www.kriesi.at/archives/improve-your-wordpress-navigation-menu-output
 */
class description_walker extends Walker_Nav_Menu{
	function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output){
        // check, whether there are children for the given ID and append it to the element with a (new) ID
        $element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]);

        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

	function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0){
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		// Adds descriptive text to the top navigation
		$prepend = '<span class="menu-item-navigation-label">';
		$append = '</span>';
		$description  = ! empty( $item->description ) ? '<span class="menu-item-description">'.esc_attr( $item->description ).'</span>' : '';

		// Prevent descriptions on lower level navigation
		if($depth != 0){
			$description = $append = $prepend = "";
		}

		$item_output = $args->before;

		// Add our sub menu markup for off-canvas navigation
		if( $item->hasChildren ){
			$item_output .= "\n".'<button type="button" class="button button-menu button-open-menu">';
			$item_output .=	"\n".'<i class="icon arrow_right_alt" aria-hidden="true"></i>';
			$item_output .=	"\n".'<span class="screen-reader-text">Open sub navigation for '.apply_filters( 'the_title', $item->title, $item->ID ).'</span>';
			$item_output .= "\n".'</button>';
		}

		// Create the link
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
		$item_output .= $description.$args->link_after;
		$item_output .= '</a>';

		// Add our sub menu markup for off-canvas navigation
		if( $item->hasChildren ){
			$item_output .= "\n".'<div class="sub-level">';
			$item_output .=	"\n".'<button type="button" class="button button-menu button-close-menu">';
			$item_output .=	"\n".'<i class="icon arrow_left_alt" aria-hidden="true"></i>';
			$item_output .=	"\n".'<span class="screen-reader-text">Close sub navigation for '.apply_filters( 'the_title', $item->title, $item->ID ).'</span>';
			$item_output .=	"\n".'</button>';
			$item_output .=	"\n".'<div class="sub-level-info">';
			$item_output .= "\n".'<span class="item-label">'.apply_filters( 'the_title', $item->title, $item->ID ).'</span>';
			$item_output .= !empty( $item->description ) ? '<span class="item-description">'.esc_attr( $item->description ).'</span>' : '';
			$item_output .= "\n".'</div>';
		}

		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el(&$output, $item, $depth = 0, $args = array()){
		// Add proper closing tags if this item has a sub menu
		if( $item->hasChildren ){
			$output .= '</div></li>';
		}
	}
}
?>