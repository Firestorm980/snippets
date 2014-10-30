/**
 * Checks to see if the element passed is within the viewable area for the user.
 * Dead simple and efficient.
 * 
 * @param  {Object}  el Can be a jQuery element (ex. $('#element')) or a regular DOM element (ex. document.getElementById('element')).
 * @return {Boolean}    True if the element is in the viewport.
 */
var isElementInViewport = function(el) {
	if (!el.length || el === undefined){
		return;
	}
    //special bonus for those using jQuery
    if (el instanceof jQuery) {
        el = el[0];
    }
    var rect = el.getBoundingClientRect();
    return rect.bottom > 0 &&
        rect.right > 0 &&
        rect.left < (window.innerWidth || document. documentElement.clientWidth) &&
        rect.top < (window.innerHeight || document. documentElement.clientHeight) ;
}