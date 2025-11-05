
/*||--------------------------------------------------------||**
**||		  <- Toggle Mobile Dropdown Menus ->			||**
**||--------------------------------------------------------||*/
var navSmTimer;
// toggle expanding nested menu in mobile nav
$(window).on('load resize', function(){
	//Clear timer and set timeout for resize
	clearInterval(navSmTimer);
	navSmTimer = setTimeout(function(){
		//remove expand class, set height to auto and turn off event handlers
		$("#nav-sm ul li").removeClass("expand").find("> a").off();
		$("#nav-sm ul li ul").css("height", "auto");
		
		//Set timeout to give enough time for measurment
		setTimeout(function(){
			//loop through menus and add event handlers
			$("#nav-sm ul li ul").parents("li").find("> a").each(function() {
				var navHeight = $(this).parents("li").children("ul").outerHeight();
				
				//Set dropdown links to hidden from screen readers
				links_attr($(this).parents("li").find("ul > li > a"), 'hide');
				
				//Set aria tags on dropdown menus
				$(this).parents("li").find("> a").attr('aria-haspopup', 'true').attr('aria-expanded', 'false');
				
				//Toggle Elements When Dropdown Link is Clicked
				$(this).on('click', function() {
					if ($(this).parents("li").hasClass("expand")) {
						$(this).parents("li").removeClass("expand").find("> a").attr('aria-expanded', 'false');
						$(this).parents("li").find("ul").css("height", "0");
						links_attr($(this).parents("li").find("ul > li > a"), 'hide');
					} else {
						$("#nav-sm ul li ul").parents("li").removeClass("expand").find("> a").attr('aria-expanded', 'false');
						$("#nav-sm ul li").children("ul").css("height", "0");
						links_attr($("#nav-sm ul li").find("ul > li > a"), 'hide');
						$(this).parents("li").addClass("expand").find("> a").attr('aria-expanded', 'true');
						$(this).parents("li").find("ul").css("height", navHeight);
						links_attr($(this).parents("li").find("ul > li > a"), 'show');
					}
					
					//Prevent Defaults on a tag
					return false;
				});
			});
			
			//set all dropdowns height to 0
			$("#nav-sm ul li ul").css("height", "0");
		}, 0);
	}, 500);
});


/*||--------------------------------------------------------||**
**||			  <- Toggle Mobile Navigation ->			||**
**||--------------------------------------------------------||*/
$(window).on('load', function() {	
	//Set all top level links to be hidden by screen readers
	links_attr($("#nav-sm-menu > ul > li > a"), 'hide');
	
	$(".open-menu").on('click', function() {
		var offsetY = window.pageYOffset;
		$("#nav-sm").toggleClass("viz");
		$("#main-wrapper").toggleClass("noscroll")
		$("#main-wrapper").css("top", -offsetY + "px");
		$("#nav-overlay").addClass("overlay-active");
		links_attr($("#nav-sm-menu > ul > li > a"), 'show');
		
		//Prevent focus from being set if using the mouse
		if (!$(this).is(':hover')) {
			 $("#nav-sm-menu > ul > li:first-child a").focus();
		}
		
		return false;
	});
	
	function closeMenu(that) {
		// SET WORKING VARIABLES
		that = that || $(".close-menu");
		var mainWrapper =	$('#main-wrapper');
		var offsetY = document.getElementById("main-wrapper").style.top;
		$("#nav-sm").removeClass("viz");
		mainWrapper.removeClass("noscroll");
		mainWrapper.css("top", "0px");
		$("#nav-overlay").removeClass("overlay-active");
		$(window).scrollTop(Math.abs(parseInt(offsetY)));
		links_attr($("#nav-sm-menu > ul > li > a"), 'hide');
		
		//Prevent focus from being set if using the mouse
		if (!$(that).is(':hover')) {
			 $(".nav-bar-sm .open-menu").focus();
		}
	}
	
	$(".close-menu, #nav-overlay").on('click', function() {
		closeMenu($(this));
		
		return false;
	});
	
	//Close mobile menu when modal is opened
	$("body").on('click', 'a[data-modal], a[data-toggle="modal"]', function() {
		closeMenu($(this));
	});
	
	//Use ESC key to close menu
	$(document).keyup(function(e){
		if (e.which === 27) {
			closeMenu();
		}
	});
});



/*||--------------------------------------------------------||**
**||			<- Mobile Nav Animation Delay ->			||**
**||--------------------------------------------------------||*/
$(window).on('load', function(){
	var navCount = .3;
	
	//nav delay function
	function navDelay() {
		$('#nav-sm #nav-sm-menu > ul > li').each(function(){
			navCount = (navCount + .05);
			$(this).css('transition-delay', navCount + 's');
		});
		navCount = .4;
	}
	
	//run nav delay function to set delay times
	navDelay();
	
	//Set delay to .1 so nav can quickly fade out on menu close
	$(".open-menu").on('click', function() {
		setTimeout(function(){
			$('#nav-sm #nav-sm-menu > ul > li').each(function(){
				$(this).css('transition-delay', '.5s');
			})
		}, 50);
	});
	
	//run nav delay function on menu open to reset the delay times
	$(".close-menu, #nav-overlay").on('click', function() {
		setTimeout(function(){	
			navDelay();
		}, 50);
	});
});




