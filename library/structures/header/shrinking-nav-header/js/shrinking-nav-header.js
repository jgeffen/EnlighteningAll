/*||--------------------------------------------------------||**
**|| <- Stick Header to Top & Retract Header on Scroll  ->	||**
**||--------------------------------------------------------||*/
$(window).on('load scroll', function(){
	var headerWrapper	= $("#header-wrapper"),
		headerPosition	= $("#header-spacer").offset().top,
		scrollPos		= $(window).scrollTop();
	
	if (scrollPos > headerPosition){
		headerWrapper.addClass("stuck");	
	} else if (scrollPos < headerPosition) {
		headerWrapper.removeClass("stuck");
	}
	
	if (scrollPos > (headerPosition + 75)) {
		$("#header-wrapper").addClass("retract");
	} else {
		$("#header-wrapper").removeClass("retract");
	}
});


/*||--------------------------------------------------------||**
**||			  <- Set Size for Header Spacer ->			||**
**||--------------------------------------------------------||*/
var headerTimer = "";

function resizeHeaderSpacer() {
	var headerWrapper = $("#header-wrapper");
	var headerHeight = headerWrapper.outerHeight();
	var headerSpacer = $("#header-spacer");
	var spacerHeight = headerSpacer.outerHeight();
	if(headerHeight != spacerHeight || !headerWrapper.hasClass("stuck")) {
		headerSpacer.css('height', headerHeight + 'px');
	}
}

$(window).on('load resize', function(){
	resizeHeaderSpacer();
});

$(window).on('scroll', function() {
	clearTimeout(headerTimer);
	headerTimer = setTimeout(function(){
		if ($(window).scrollTop() < 1) {
			resizeHeaderSpacer();	
		}
	}, 300);
});


/*||------------------------------------------------------------||**
**||			<- Add dropdown class for targeting ->			||**
**||------------------------------------------------------------||*/
$(window).on('load', function() {
	$(".nav-bar-lg > ul > li > ul").closest("li").addClass("dropdown");
});


/*||------------------------------------------------------------||**
**|| 	  <- Split Dropdowns into Multiple Columns ->			||**
**||------------------------------------------------------------||*/
$(window).on('load', function(){
	//Make sure desktop nav is displayed for measurements
	$(".nav-bar-lg").removeClass("d-none");
	
	$(".nav-bar-lg > ul > li > ul").each(function(){
		
		var linksCount 			= $(this).find("li").length;
		var dropWidth			= $(this).outerWidth();
		var dropPaddingTop		= parseInt($(this).css('padding-top'));
		var dropPaddingBottom	= parseInt($(this).css('padding-bottom'));
		
		if(linksCount > 10) {
			var links				= $(this).find("li");
			var linksHalf			= Math.ceil(links.length / 2);
			var linksFirstHalf		= links.slice(0, linksHalf);
			var linksLastHalf		= links.slice(linksHalf, linksCount);
			var heightCounterFirst	= dropPaddingTop;
			var heightCounterLast	= dropPaddingTop;
			
			//Loop through the first half array and set the link positioning
			for(var i = 0; i < linksFirstHalf.length; i++) {
				linksFirstHalf.eq(i).css({
					'position': 'absolute',
					'top': heightCounterFirst + 'px',
					'left': '0'
				});
				heightCounterFirst += linksFirstHalf.eq(i).outerHeight();
			}
			
			//Loop through the last half array and set the link positioning
			for(var j = 0; j < linksLastHalf.length; j++) {
				linksLastHalf.eq(j).css({
					'position': 'absolute',
					'top': heightCounterLast + 'px',
					'right': '0'
				});
				heightCounterLast += linksLastHalf.eq(j).outerHeight();
			}
			
			//Get the height for the ul
			if(heightCounterFirst > heightCounterLast) {
				var ulHeight = heightCounterFirst;	
			} else {
				var ulHeight = heightCounterLast;	
			}
			
			//Set the ul width and height
			$(this).css({
				'width': dropWidth * 2,
				'height': ulHeight + dropPaddingBottom
			});
			
			//Remove all of the original li's
			links.remove();
			
			//Append the new arrays of links
			$(this).append(linksFirstHalf).append(linksLastHalf);
			
			//Set width for the new links
			$(this).find("li").css({
				'width': '50%'
			});
		}
	});
	
	//Add d-none class back to desktop nav
	$(".nav-bar-lg").addClass("d-none");
});


/*||------------------------------------------------------------||**
**||   <- Prevent Dropdown Menus from Hanging Off the Page ->	||**
**||------------------------------------------------------------||*/
var unsuspend = '';

$(window).on('load resize', function(){
	//Suspend all navigation transitions and transforms
	clearTimeout(unsuspend);
	$(".nav-bar-lg *").css({
		'transition': 'none',
		'-webkit-transition': 'none',
		'-moz-transition': 'none',
		'-ms-transition': 'none',
		'-o-transition': 'none',
		'transform': 'none',
		'-webkit-transform': 'none',
		'-moz-transform': 'none',
		'-ms-transform': 'none',
		'-o-transform': 'none'
	});
	
	$(".nav-bar-lg ul ul").each(function(){
		
		//Set the left position back to default on resize for measurements
		$(this).css('left', '0');
		
		//Take measurments and declare variables	
		var thisDropdown		= $(this),
			dropOffset			= $(this).offset(),
			dropLeft			= dropOffset.left,
			dropWidth			= $(this).width(),
			dropRight			= dropLeft + dropWidth,
			windowWidth			= $(window).width(),
			windowPadded		= windowWidth - 30,
			overHang			= dropRight - windowWidth,
			marginCorrection 	= (overHang + 30) * -1; //The 30 is to add extra padding, this can be adjusted to preference
		
		//Apply the new corrected left value
		setTimeout(function(){
			if(dropRight > windowPadded) {
				$(thisDropdown).css('left', marginCorrection);
			}
		}, 0);
		
	});
	
	//Remove the transition & tranform suspensions
	unsuspend = setTimeout(function(){
		$(".nav-bar-lg *").css({
			'transition': '',
			'-webkit-transition': '',
			'-moz-transition': '',
			'-ms-transition': '',
			'-o-transition': '',
			'transform': '',
			'-webkit-transform': '',
			'-moz-transform': '',
			'-ms-transform': '',
			'-o-transform': ''
		});
	},500);
});



/*||--------------------------------------------------------||**
**||	<- Desktop Dropdown Fix for Touchscreen Devices ->	||**
**||--------------------------------------------------------||*/
$(window).on('load', function(){
	$('.nav-bar-lg > ul > li > ul').siblings('a').on("touchstart", function(e){
		e.preventDefault();
		
		$('.nav-bar-lg ul li a').not($(this)).parent().removeClass("active");
		
		if($(this).parent().hasClass("active")) {
			$(this).parent().removeClass("active");
		} else {
			e.stopPropagation();
			$(this).parent().addClass("active");
		}
	});
	
	$('#main-wrapper').on("touchstart", function(){
		$('.nav-bar-lg ul li').removeClass("active");
	});
	
	$('.nav-bar-lg > ul > li > ul a').on('touchstart', function(e){
		e.stopPropagation();
	});
});



/*||--------------------------------------------------------||**
**||			<- Dropdown Animation Delay ->				||**
**||--------------------------------------------------------||*/
$(window).on('load', function(){
	//dropdown delay animation function
	function delayAnim(target) {
		$(target).each(function(){
			var count = .15;
			$(this).find('li').each(function(){
				count = count + .05;
				$(this).css('transition-delay', count + 's');
			});
		});
	}
	
	//run delay animation function
	delayAnim('.nav-bar-lg ul li');
	
	//on mouse enter set the delay time to .2 for smooth fade out
	$('.nav-bar-lg ul li').on('mouseover', function(){
		delayAnim(this);
	});
	
	//on mouse leave run the delay animation function to reset the delay times
	$('.nav-bar-lg ul li').on('mouseout', function(){
		$(this).find('li').css('transition-delay', '.4s');
	});
});



/*||--------------------------------------------------------||**
**||		<- ADA Tabbing Through Desktop Nav ->			||**
**||--------------------------------------------------------||*/
$(window).on('load', function(){
	//Set all dropdown links to tabindex -1
	links_attr($(".nav-bar-lg ul li ul li a"), 'hide');
	
	//Set pop up aria tags on dropdown menus
	$(".nav-bar-lg ul li ul").parents("li").find("> a").attr('aria-haspopup', 'true').attr('aria-expanded', 'false');
	
	//Open dropdown menu on enter key
	$(".nav-bar-lg ul li ul").parents("li").find("> a").on('click',function(e) {
	    if ($(this).parents("li").hasClass("active")) {
			$(this).parents("li").removeClass("active").find("> a").attr('aria-expanded', 'false');
			links_attr($(this).parents("li").find("ul > li > a"), 'hide');
		} else {
			$(".nav-bar-lg ul li ul").parents("li").removeClass("active").find("> a").attr('aria-expanded', 'false');
			links_attr($(".nav-bar-lg ul li").find("ul > li > a"), 'hide');
			$(this).parents("li").addClass("active").find("> a").attr('aria-expanded', 'true');
			links_attr($(this).parents("li").find("ul > li > a"), 'show');
		}
	    
	    return false;
	});
	
	//Remove active class(close menu) when mouse out if menu is clicked by the mouse
	$(".nav-bar-lg ul li ul").parents("li").find("> a").on("mouseout", function(){
		$(this).parents("li").removeClass("active").find("> a").attr('aria-expanded', 'false');
		links_attr($(this).parents("li").find("ul > li > a"), 'hide');
	});
});



/*||--------------------------------------------------------||**
**||			<- Highlight Current Page Link ->			||**
**||--------------------------------------------------------||*/
$(window).on('load', function(){
	$('nav li a[href="' + location.pathname + '"]').addClass('current');
	$('nav li a[data-href="' + location.pathname + '"]').addClass('current');
});