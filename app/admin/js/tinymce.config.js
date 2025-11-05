document.addEventListener('DOMContentLoaded', function() {
	// Defer Scripts
	$.when(
		// Load Scripts
		typeof tinymce !== 'object' && $.getScript('/library/packages/tinymce/tinymce.min.js'),
		$.Deferred(function(deferred) {
			$(deferred.resolve);
		})
	).done(function() {
		tinymce.init({
			selector: 'textarea:not(.disable-mce)',
			browser_spellcheck: true,
			cache_suffix: '?v=6.1.2',
			base_url: '/library/packages/tinymce',
			document_base_url: '/',
			paste_data_images: false,
			convert_urls: false,
			theme: 'silver',
			height: 700,
			plugins: [
				'advlist', 'autolink', 'link', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak', 'emoticons',
				'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime', 'nonbreaking',
				'save', 'directionality', 'template', 'image', 'components'
			],
			toolbar1: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | styleselect | link unlink anchor | forecolor | image | print code',
			external_plugins: { components: '/user/assets/tinymce/components/plugin.js' },
			valid_elements: '*[*]',
			invalid_elements: 'body,head,title,meta,nav,header,footer,embed,audio,video,frame,frameset,hgroup,listing,big,marquee,spacer,nobr,script,center,iframe,applet,font,basefont,dir,isindex,menu,s,strike,style',
			valid_children: '+*[*]'
				+ ',body[p,ol,ul,h2,h3,h4,h5,h6,hr,blockquote]'
				+ ',-body[head|title|meta|nav|header|footer|embed|audio|video|frame|frameset|hgroup|listing|big|marquee|spacer|nobr|script|center|iframe|applet|font|basefont|dir|isindex|menu|s|strike|style|img]'
				+ ',p[span|strong|b|em|a|i|u|sup|sub|img|#text]'
				+ ',h2[span|strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',h3[span|strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',h4[span|strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',h5[span|strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',h6[span|strong|b|em|a|i|u|esup|sub|br|img|#text]'
				+ ',blockquote[h2|h3|h4|h5|h6|p|span|strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',ul[li|img]'
				+ ',-ul[ul|ol]'
				+ ',ol[li|img]'
				+ ',-ol[ul|ol]'
				+ ',span[strong|b|em|a|i|u|sup|sub|br|img|#text]'
				+ ',li[span|strong|b|em|a|i|u|sup|sub|br|ul|ol|img|small|#text]'
				+ ',-li[li]'
				+ ',a[span|strong|b|em|i|u|sup|sub|br|img|#text]'
				+ ',strong[span|em|a|u|sup|sub|br|img|#text]'
				+ ',b[span|em|a|u|sup|sub|br|img|#text]',
			forced_br_newlines: false,
			force_p_newlines: true,
			forced_root_block: 'p',
			verify_html: true,
			relative_urls: false,
			style_formats: [
				{ title: 'Paragraph', format: 'p' },
				{ title: 'Header 2', format: 'h2' },
				{ title: 'Header 3', format: 'h3' },
				{ title: 'Header 4', format: 'h4' },
				{ title: 'Header 5', format: 'h5' },
				{ title: 'Header 6', format: 'h6' }
			],
			formats: {
				underline: { inline: 'u', exact: true }
			},
			image_dimensions: false,
			image_class_list: [
				{ title: 'Landscape Image Left', value: 'inset left border' },
				{ title: 'Portrait Image Left', value: 'inset-tall left border' },
				{ title: 'Graphic/Logo Left', value: 'inset-tall left' },
				{ title: 'Landscape Image Right', value: 'inset right border' },
				{ title: 'Portrait Image Right', value: 'inset-tall right border' },
				{ title: 'Graphic/Logo Right', value: 'inset-tall right' },
				{ title: 'Landscape Image Center', value: 'inset-center border' },
				{ title: 'Panoramic Center', value: 'img-fluid my-4 mx-auto d-block' },
				{ title: 'Graphic/Logo Center', value: 'inset-center' }
			],
			file_picker_types: 'image',
			file_picker_callback: function(callback) {
				/* Set Up Input Element */
				$('<input/>', {
					name: 'image',
					type: 'file',
					accept: 'image/x-png,image/jpeg'
				}).on('change', function() {
					/* Calculate Total Size */
					var totalSize = [].slice.call(this.files).map(function(file) {
						return file.size || file.fileSize;
					}).reduce(function(a, b) {
						return a + b;
					}, 0);

					/* Check Upload Size */
					if(totalSize > settings.maxFilesize.B) {
						/* Alert Error */
						alert('Total size exceeds ' + settings.maxFilesize.B + '!');
					} else {
						// Variable Defaults
						var progressBar;

						// Handle Upload
						$.ajax({
							url: '/ajax/admin/tinymce/upload',
							dataType: 'JSON',
							contentType: false,
							processData: false,
							data: new FormData($('<form/>').append($(this))[0]),
							type: 'POST',
							beforeSend: function() {
								$.ajax('/ajax/admin/tinymce/progress-bar', {
									method: 'post',
									dataType: 'html',
									async: true,
									success: function(response) {
										// Variable Defaults
										progressBar = $(response);

										// Init Progress Bar
										progressBar.on('hidden.bs.modal', function() {
											$(this).remove();
										}).on('show.bs.modal', function() {
											console.log('Show');
											$('.modal-backdrop').css('z-index', 1340);
										}).appendTo('body').modal();
									},
									error: function(xhr) {
										// Handle Error
										displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
									}
								});
							},
							complete: function() {
								// Hide Progress Bar
								progressBar.modal('hide');
							},
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Return URL
										callback(response.url);
										break;
									case 'error':
									default:
										// Handle Error
										displayMessage(response.message || 'An unknown error has occurred', 'alert');
								}
							},
							xhr: function() {
								var myXhr = $.ajaxSettings.xhr();

								if(myXhr.upload) {
									myXhr.upload.addEventListener('progress', function(event) {
										// Variable Defaults
										var progress = (event.loaded / event.total * 100).toFixed(0) + '%';

										// Update Progress Bar
										if(progressBar) {
											progressBar.find('.progress-bar').css('width', progress);
											progressBar.find('#progress-label').html(progress);
										}
									}, false);
								}

								return myXhr;
							}
						});
					}
				}).click();
			},
			menu: {
				file: { title: 'File', items: 'print' },
				edit: { title: 'Edit', items: 'undo redo | cut copy paste | link | selectall' },
				view: { title: 'View', items: 'fullscreen code' },
				insert: { title: 'Insert', items: 'image link | charmap emoticons hr | nonbreaking toc | insertdatetime | layout columns buttons' },
				format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats align | forecolor | removeformat' }
			},
			paste_auto_cleanup_on_paste: true,
			paste_preprocess: function(pl, o) {
				o.content = o.content.replace(/ id="(.*?)"/ig, '').replace(/ class="(.*?)"/ig, '').replace(/ style="(.*?)"/ig, '').replace(/<img(.*?)>/ig, '');
			},
			remove_trailing_brs: false,
			protect: [
				/<div class="clear"><\/div>/g
			],
			element_format: 'html',
			content_css: '/css/styles-main.min.css',
			promotion: false
		});

		// Init TinyMCE for Member Posts
		tinymce.init({
			selector: '.member-post-mce',
			theme: 'silver',
			cache_suffix: '?v=6.1.2',
			base_url: '/library/packages/tinymce',
			browser_spellcheck: true,
			document_base_url: '/',
			element_format: 'html',
			forced_root_block: 'p',
			formats: {
				bold: { inline: 'strong' },
				italic: { inline: 'em' },
				underline: { inline: 'u' }
			},
			height: 362,
			keep_styles: false,
			menubar: false,
			mobile: { toolbar_mode: 'scrolling' },
			plugins: 'emoticons',
			protect: [/<div class="clear"><\/div>/g],
			relative_urls: false,
			toolbar: 'bold italic underline emoticons',
			valid_elements: 'p,br,strong/b,em/i,u',
			verify_html: true,
			statusbar: false
		});
	});
});