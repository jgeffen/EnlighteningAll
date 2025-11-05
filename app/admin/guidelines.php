<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// Set Title
	$page_title = 'Admin Panel Guidelines & Tips';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title">
		<h1>Admin Panel Guidelines & Tips</h1>
	</div>
	
	<div class="content-module">
		<h2>Best Practices</h2>
		
		<hr>
		
		<h3>Copying and Pasting Text into the Editor</h3>
		
		<p>When copying and pasting text into the admin panel it's possible for HTML to get carried over from the source. This HTML can cause unintended styles, positioning, sizing and many other page layout issues. This will commonly happen when copying and pasting text from Microsoft Word, Outlook, websites & other word processing applications.
			<b>The best practice is to use Microsoft Notepad or TextEdit(on Mac) to type up your text</b> and then copy and paste it into the admin panel. Notepad is a "plain text" editor that will not add HTML elements to your text. When using TextEdit(on Mac) be sure to select "Make Plain Text" under the format menu. If the text you need is already in Microsoft Word, Outlook, etc., the best practice is to copy and paste it into Notepad or TextEdit first and then copy and paste it from Notepad/TextEdit to the admin panel editor. Pasting the text into Notepad first will clear the HTML from the text, converting it to "plain text". When using TextEdit(on Mac) you will need to select all of the text and then choose "Make Plain Text" under the format menu to convert the text to "plain text".
		</p>
		
		<hr>
		
		<h3>Apply All Text Formatting and Styles in the Admin Panel Editor</h3>
		
		<p>
			<b>It is best practice to only apply text formatting and styling in the admin panel editor</b> after you have pasted in the plain text. This includes formatting such as font, font size, font weight, color, bold, italic, bullets, etc. If you follow the above instructions to use Notepad or TextEdit(in plain text mode) you should not have the option to apply any text formatting or styles in those applications, this is a good indicator that your text is "plain text".
		</p>
		
		<hr>
		
		<h3>Adding Additional Images into the Content</h3>
		
		<p>The admin panel editor allows you to add additional images throughout the page content.
			<b>The best practice is to save the images to your computer and then use the "Insert/edit image" button to add those additional images to the page.</b> When inserting or editing an image you will select a Class for the image, this class will determine the sizing, positioning and style for the image.
			<b>Do not copy and paste images into the editor or resize the image manually</b>. When pasting an image into the editor it will commonly carry over HTML styles that will interfere with the styles already being applied by the selected image Class. Manually resizing the image is not necessary, the selected Class will apply the correct sizing and position for the image. Manually resizing the image will apply inline styles that will interfere with the natural sizing provided by the selected image Class.
		</p>
		
		<hr>
		
		<h3>Disable Browser Plugins/Extensions</h3>
		
		<p>
			<b>It is best practice to disable any browser plugins/extensions</b> that may effect the page content before using the admin panel. Some plugins/extensions (such as the Grammarly extension) may inject HTML into the page content or modify the HTML of the page unintentionally, which may cause layout issues on the front end of the website.
		</p>
		
		<hr>
		
		<h3>Only Use HTML in the Source Code Editor</h3>
		
		<p>When adding/editing content in the admin panel editor you have the option to edit the source code. The source code should only contain HTML,
			<b>do not add code from any other programming languages such as PHP or Javascript</b>. Adding code from other programming languages could completely break the page and lead to security risks for the entire site.
		</p>
		
		<br>
		
		<h2>Admin Panel Tips</h2>
		
		<hr>
		
		<h3>Sorting Pages</h3>
		
		<p>Most pages in the admin panel can be sorted by dragging and dropping the page name in the list view. This will allow you to choose which order the pages will be listed on the live site.</p>
		
		<hr>
		
		<h3>Form Submissions</h3>
		
		<p>All form submission are stored in the admin panel, you can access this data in the menu under "Forms". Payment submissions are also stored in the admin panel, you can also find these in the main menu under "FORMS"</p>
		
		<hr>
		
		<h3>Adding Gallery Images</h3>
		
		<p>To add images to a gallery you must first create the gallery page. Once you have created the gallery page you can add images by clicking the "Options" button in the gallery list page and then select "Images" from the dropdown menu. You can also add images from the edit gallery page, scroll down to the bottom of the edit gallery page and click the "Manage Images" button.</p>
		
		<hr>
		
		<h3>Saving Your Work</h3>
		
		<p>When adding a new page or editing a page on the website you will always need to click the "Save" button at the bottom of the page to publish your work. Be sure that everything is complete before clicking save since clicking the "Save" button will immediately publish those changes to the live website. It is recommended to work on text for new pages in Windows Notepad or Mac TextEdit and save a your work as a plain text .txt file, then paste that plain text into the admin panel editor once it's complete. This will prevent loss of data by keeping a local copy of your text in a plain text file. Any changes made in the admin panel before clicking "Save" will be lost if the window is closed.</p>
		
		<hr>
		
		<h3>Page Categories</h3>
		
		<p>Categories must be created before you can add items into that category. First, select "Add Category" from the category page. Once you have created the category page you will then find that category as an option when creating a new item. For example, Galleries require an existing category before a new Gallery can be added in that category.</p>
	</div>
</main>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>
