$(window).on('load', function(){
	//Unwrap the nested ul and li tags to remove all dropdown menus but retain the links
	$("#footer-nav li ul").each(function(){
		$(this).siblings('a').remove();
		$(this).unwrap();
		$(this).contents().unwrap();
	});
	
	
	//Spit Footer Nav List into Columns
	var footerLinks = $("#footer-nav ul"),
		linkLength = footerLinks.find('li').length,
		linkSplit = 2; //This determins how many columns
		linksInRow = Math.ceil(linkLength / linkSplit);
	
	for (var i=0; i < linkSplit; i++){
        var listItems = footerLinks.find('li').slice(0, linksInRow),
        	newUl = $('<ul/>').append(listItems);
        	newCol = $('<div class="col-sm-6 col-lg-4 equal-col">').append(newUl);
        $("#footer-nav .container .row").append(newCol);
    }
    
    //Remove old ul from original list
    $("#footer-nav .container .row > ul").remove();
});