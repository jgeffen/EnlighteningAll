/*||--------------------------------------------------------||**
 **||		  <- Toggle Mobile Dropdown Menus ->			||**
 **||--------------------------------------------------------||*/
var navSmTimer;
// toggle expanding nested menu in mobile nav
$(window).on('load resize', function() {
	//Clear timer and set timeout for resize
	clearInterval(navSmTimer);
	navSmTimer = setTimeout(function() {
		//remove expand class, set height to auto and turn off event handlers
		$('#nav-sm ul li').removeClass('expand').find('> a').off();
		$('#nav-sm ul li ul').css('height', 'auto');

		//Set timeout to give enough time for measurment
		setTimeout(function() {
			//loop through menus and add event handlers
			$('#nav-sm ul li ul').parents('li').find('> a').each(function() {
				var navHeight = $(this).parents('li').children('ul').outerHeight();

				//Set dropdown links to hidden from screen readers
				links_attr($(this).parents('li').find('ul > li > a'), 'hide');

				//Set aria tags on dropdown menus
				$(this).parents('li').find('> a').attr('aria-haspopup', 'true').attr('aria-expanded', 'false');

				//Toggle Elements When Dropdown Link is Clicked
				$(this).on('click', function() {
					if($(this).parents('li').hasClass('expand')) {
						$(this).parents('li').removeClass('expand').find('> a').attr('aria-expanded', 'false');
						$(this).parents('li').find('ul').css('height', '0');
						links_attr($(this).parents('li').find('ul > li > a'), 'hide');
					} else {
						$('#nav-sm ul li ul').parents('li').removeClass('expand').find('> a').attr('aria-expanded', 'false');
						$('#nav-sm ul li').children('ul').css('height', '0');
						links_attr($('#nav-sm ul li').find('ul > li > a'), 'hide');
						$(this).parents('li').addClass('expand').find('> a').attr('aria-expanded', 'true');
						$(this).parents('li').find('ul').css('height', navHeight);
						links_attr($(this).parents('li').find('ul > li > a'), 'show');
					}

					//Prevent Defaults on a tag
					return false;
				});

				//Prevent clicks on links in the dropdown from bubbling up and closing the dropdown
				$('#nav-sm ul > li > ul > li > a').on('click', function(e) {
					e.stopPropagation();
				});
			});

			//set all dropdowns height to 0
			$('#nav-sm ul li ul').css('height', '0');
		}, 0);
	}, 500);
});

/*||--------------------------------------------------------||**
 **||		 <- Set the Height of the Desktop Nav ->		||**
 **||--------------------------------------------------------||*/

//Function for setting the Nav Height
function desktopNavHeight() {
	if(window.matchMedia('(min-width: 768px)').matches) {
		//Reset for natural height
		$('.main-container').css('height', 'auto');

		//Get the content height
		var contentHeight = $('#content-wrap').outerHeight();

		//Set the content height
		$('.main-container').css('height', contentHeight);
	} else {
		//Reset for natural height
		$('.main-container').css('height', 'auto');
	}
}

//Run the function in a loop to keep the height updated
//This was the best option since there was no event to detect content changes
$(window).on('load', function() {
	setInterval(function() {
		desktopNavHeight();
	}, 500);
});

/*||--------------------------------------------------------||**
 **|| <- Position Desktop Dropdowns for Botttom Placement ->	||**
 **||--------------------------------------------------------||*/
$(window).on('load resize', function() {
	var navItems   = $('#nav-main > ul > li');
	var totalItems = navItems.length;
	var cutOff     = totalItems - 5;

	$('#nav-main > ul > li').each(function(index) {
		if(index > cutOff) {
			$(this).addClass('align-btm');
		} else {
			$(this).removeClass('align-btm');
		}
	});
});

/*||==========================================================||**
 **|| <---------------- Window Load Section -----------------> ||**
 **||==========================================================||*/

$(window).on('load', function() {
	/*||--------------------------------------------------------||**
	 **||			  <- Toggle Mobile Navigation ->			||**
	 **||--------------------------------------------------------||*/
	//Set all top level links to be hidden by screen readers
	links_attr($('#nav-sm-menu > ul > li > a'), 'hide');

	$('.open-menu').on('click', function() {
		var offsetY = window.pageYOffset;
		$('#nav-sm').toggleClass('viz');
		$('#site').toggleClass('site-no-scroll');
		$('#site').css('top', -offsetY + 'px');
		$('#nav-overlay').addClass('overlay-active');
		links_attr($('#nav-sm-menu > ul > li > a'), 'show');

		//Prevent focus from being set if using the mouse
		if(!$(this).is(':hover')) {
			$('#nav-sm-menu > ul > li:first-child a').focus();
		}

		return false;
	});

	function closeMenu(that) {
		// SET WORKING VARIABLES
		that            = that || $('.close-menu');
		var mainWrapper = $('#site');
		var offsetY     = document.getElementById('site').style.top;
		$('#nav-sm').removeClass('viz');
		mainWrapper.removeClass('site-no-scroll');
		mainWrapper.css('top', '0px');
		$('#nav-overlay').removeClass('overlay-active');
		$(window).scrollTop(Math.abs(parseInt(offsetY)));
		links_attr($('#nav-sm-menu > ul > li > a'), 'hide');

		//Prevent focus from being set if using the mouse
		if(!$(that).is(':hover')) {
			$('.nav-bar-sm .open-menu').focus();
		}
	}

	$('.close-menu, #nav-overlay').on('click', function() {
		closeMenu($(this));

		return false;
	});

	//Use ESC key to close menu
	$(document).keyup(function(e) {
		if(e.which === 27) {
			closeMenu();
		}
	});

	/*||--------------------------------------------------------||**
	 **||	<- Desktop Dropdown Fix for Touchscreen Devices ->	||**
	 **||--------------------------------------------------------||*/
	$('.dropdown-nav > ul > li > ul').siblings('a').on('touchstart', function(e) {
		e.preventDefault();

		$('.dropdown-nav ul li a').not($(this)).parent().removeClass('active');

		if($(this).parent().hasClass('active')) {
			$(this).parent().removeClass('active');
		} else {
			e.stopPropagation();
			$(this).parent().addClass('active');
		}
	});

	$('#site').on('click touchstart', function() {
		$('.dropdown-nav ul li').removeClass('active');
	});

	$('.dropdown-nav > ul > li > ul a').on('touchstart', function(e) {
		e.stopPropagation();
	});

	/*||--------------------------------------------------------||**
	 **||  <- ADA Tabbing Through Desktop Nav with Dropdowns ->  ||**
	 **||--------------------------------------------------------||*/
	//Set all dropdown links to tabindex -1
	links_attr($('.dropdown-nav ul li ul li a'), 'hide');

	//Set pop up aria tags on dropdown menus
	$('.dropdown-nav ul li ul').parents('li').find('> a').attr('aria-haspopup', 'true').attr('aria-expanded', 'false');

	//Open dropdown menu on enter key
	$('.dropdown-nav ul li ul').parents('li').find('> a').on('click', function(e) {
		if($(this).parents('li').hasClass('active')) {
			$(this).parents('li').removeClass('active').find('> a').attr('aria-expanded', 'false');
			links_attr($(this).parents('li').find('ul > li > a'), 'hide');
		} else {
			$('.dropdown-nav ul li ul').parents('li').removeClass('active').find('> a').attr('aria-expanded', 'false');
			links_attr($('.dropdown-nav ul li').find('ul > li > a'), 'hide');
			$(this).parents('li').addClass('active').find('> a').attr('aria-expanded', 'true');
			links_attr($(this).parents('li').find('ul > li > a'), 'show');
		}

		return false;
	});

	//Remove active class(close menu) when mouse out if menu is clicked by the mouse
	$('.dropdown-nav ul li ul').parents('li').find('> a').on('mouseout', function() {
		$(this).parents('li').removeClass('active').find('> a').attr('aria-expanded', 'false');
		links_attr($(this).parents('li').find('ul > li > a'), 'hide');
	});

	/*||--------------------------------------------------------||**
	 **||		<- Add Active Class to Current Page Link ->		||**
	 **||--------------------------------------------------------||*/
	$(function() {
		// Set Collapse In for Active URL
		var pathname = location.pathname.replace(/[0-9]+\.html/g, '');
		$('#nav-main').find('a[href^="' + pathname + '"]').parents('li').addClass('current');
	});

	/*||--------------------------------------------------------||**
	 **||		<- Style Change on Notification Count ->		||**
	 **||--------------------------------------------------------||*/
	$('.notification-new-count').each(function() {
		var count = $(this).text().length;

		//Adds class to adjust the rounded corners
		if(count > 1) {
			$(this).addClass('extend');
		}

		//Dynamically sets the Min Width based on how many characters
		if(count > 2) {
			$(this).css('min-width', count * 7.3 + 16 + 'px');
		}
	});

	/*||--------------------------------------------------------||**
	 **||			<- Dropdown Animation Delay ->				||**
	 **||--------------------------------------------------------||*/

	//dropdown delay animation function
	function delayAnim(target) {
		$(target).each(function() {
			var count = .15;
			$(this).find('li').each(function() {
				count = count + .06;
				$(this).css('transition-delay', count + 's');
			});
		});
	}

	//run delay animation function
	delayAnim('#nav-main ul li');

	//on mouse enter set the delay time to .2 for smooth fade out
	$('#nav-main ul li').on('mouseover', function() {
		delayAnim(this);
	});

	//on mouse leave run the delay animation function to reset the delay times
	$('#nav-main ul li').on('mouseout', function() {
		$(this).find('li').css('transition-delay', '.4s');
	});

	/*||--------------------------------------------------------||**
	 **||			<- Mobile Nav Animation Delay ->			||**
	 **||--------------------------------------------------------||*/
	var navCount = .3;

	//nav delay function
	function navDelay() {
		$('#nav-sm #nav-sm-menu > ul > li').each(function() {
			navCount = (navCount + .04);
			$(this).css('transition-delay', navCount + 's');
		});
		navCount = .3;
	}

	//run nav delay function to set delay times
	navDelay();

	//Set delay to .1 so nav can quickly fade out on menu close
	$('.open-menu').on('click', function() {
		setTimeout(function() {
			$('#nav-sm #nav-sm-menu > ul > li').each(function() {
				$(this).css('transition-delay', '.5s');
			});
		}, 50);
	});

	//run nav delay function on menu open to reset the delay times
	$('.close-menu, #nav-overlay').on('click', function() {
		setTimeout(function() {
			navDelay();
		}, 50);
	});

	/*||--------------------------------------------------------||**
	 **||			<- Minimize Desktop Navigation ->			||**
	 **||--------------------------------------------------------||*/
	var mainRow           = $('.main-row');
	var navCol            = mainRow.find('> div:first-child');
	var contentCol        = mainRow.find('> div:last-child');
	var navColClasses     = navCol.attr('class');
	var contentColClasses = contentCol.attr('class');
	var openNavBtn        = $('header .minimize-nav');

	$('.minimize-nav').on('click', function() {
		if(mainRow.hasClass('nav-minimized')) {
			mainRow.removeClass('nav-minimized');
			openNavBtn.addClass('d-md-none').removeClass('d-md-flex');
			navCol.attr('class', navColClasses);
			contentCol.attr('class', contentColClasses);
		} else {
			mainRow.addClass('nav-minimized');
			openNavBtn.addClass('d-md-flex').removeClass('d-md-none');
			navCol.attr('class', 'd-none');
			contentCol.attr('class', 'col-12');
		}
	});
});







