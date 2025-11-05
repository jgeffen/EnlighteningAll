$(window).on('load resize', function(){
	var circleButtons = $(".circle-info-buttons .circle-button");
	var circleWidth = circleButtons.outerWidth();
	circleButtons.css("height", circleWidth);
});