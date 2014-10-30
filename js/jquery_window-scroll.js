jQuery(document).ready(function($) {

	windowScroll.init(); // Start us up

	// When we scroll, just change this variable.
	// By monitoring it on our own, we help out performance.
	$(window).scroll(function(){
		windowScroll.didScroll = true; 
	});

});


var windowScroll = {
	didScroll: false, // The var we're always checking.
	init: function(){
		// This allows your functions to fire immediately to check position. 
		// Great if the elements you're tracking are in the viewable area.
		windowScroll.hasScrolled();

		// Check if we scrolled periodically.
		// This can be 'throttled' by changing the interval time. The smaller the interval, the more it checks. May impact performance.
		setInterval( function(){
			if (globalVars.didScroll) {
				windowScroll.hasScrolled();
				globalVars.didScroll = false;
			}
		}, 150);
	},
	hasScrolled: function(){
		// Do all of your math and checking functions through here.
	}
};