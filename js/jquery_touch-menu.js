/**
 * Why do we need this?
 *
 * iOS *used* to do this by itself during :hover. With the update, that's changed.
 * Not only that, but this benefits Android devices as well that weren't getting friendly menus.
 * @note: This code may eventually need a fix for Windows Mobile.
 */
jQuery(document).ready(function($) {

	// Touch Navigation
	touchMenu.init();
	$(document).on('touchstart', '#primaryNavigation .nav-menu a', touchMenu.linkClick);
	$(document).on('click', touchMenu.stop);

});

var touchMenu = {
	// Start us up!
	init: function(){
		// Set all of our navigation links to have data that keeps track of its state.
		// Also close all sub menus and set all links state's to false.
		$('#primaryNavigation .nav-menu a').each(function(){
			var $this = $(this);
			$this.data('tapped',false);
			$this.next('.sub-level').removeClass('hover-open');
		});
	},
	// We've tapped on a link.
	// @note: Classes and breakpoint would need to be changed on a case-by-case basis.
	linkClick: function(event){
		var 
			$this = $(this),
			$subLevel = $this.next('.sub-level'),
			$siblingLinks = $this.parent('li').siblings().children('a'),
			hasSublevel = ( $subLevel.length ) ? true : false,
			hasBeenTapped = ( $this.data('tapped') === false ) ? false : true;

		// If this link has subnavigation, this is the 1st time it's been tapped, and we're seeing the right view...
		if (hasSublevel && !hasBeenTapped && Modernizr.mq('(min-width: 830px)')){
			event.preventDefault(); // Don't open the link
			event.stopPropagation(); // Don't bubble

			$siblingLinks.data('tapped', false); // Reset the links around it
			$siblingLinks.next('.sub-level').removeClass('hover-open'); // Close the other sub menus around it

			$this.data('tapped',true); // Set this link to say it's been tapped once
			$subLevel.addClass('hover-open'); // Open its sub nav so we can get to it
		}
		// Otherwise, it's the second time we've tapped it or it doesn't have a sub menu, so do as normal.
	},
	// We clicked something that wasn't a link. Reset.
	stop: function(event){
		touchMenu.init();
	}
};