var transition = {
	// Determines proper transition property per browser
	whichTransitionEvent: function(){
		var t;
		var el = document.createElement('fakeelement');
		var transitions = {
			'OTransition':'oTransitionEnd',
			'MozTransition':'transitionend',
			'WebkitTransition':'webkitTransitionEnd',
			'transition':'transitionend'
		};
		for(t in transitions){
			if( el.style[t] !== undefined ){
				return transitions[t];
			}
		}
	}
};
// Makes it easier to bind cross browser, like $('element').one(transitionEnd, function(){});
var transitionEnd = transition.whichTransitionEvent(); 