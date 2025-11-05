/*
 Copyright (c) 2017, 2021 FenclWebDesign.com
 This script may not be copied, reproduced or altered in whole or in part.
 We check the Internet regularly for illegal copies of our scripts.
 Do not edit or copy this script for someone else, because you will be held responsible as well.
 This copyright shall be enforced to the full extent permitted by law.
 Licenses to use this script on a single web site may be purchased from FenclWebDesign.com
 @Author: Deryk
 */

/*||==========================================================||**
 **|| <---------------- Window Load Section -----------------> ||**
 **||==========================================================||*/
$(window).on('load', function() {
	/*||--------------------------------------------------------||**
	 **||			<- Initialize Tool Tips ->					||**
	 **||--------------------------------------------------------||*/
	(function() {
		var touchstart = false;
		$('[data-toggle="tooltip"]').on('touchstart mouseover', function(event) {
			if(event.type === 'touchstart') {
				touchstart = true;
			}
			if(!touchstart) {
				$(event.currentTarget).tooltip('toggle');
			}
		});
	})();

	// TOOL TIP MODAL SCRIPT
	$('[data-tool-tip]').on('click', function(event) {
		/* Prevent Default */
		event.preventDefault();

		/* Variable Defaults */
		var toolTip      = $(this).data('tool-tip');
		var toolTipTitle = $(this).find('span').html();

		/* Populate/Trigger Modal */
		$('#tool-tip-modal').find('.modal-title').html(toolTipTitle);
		$('#tool-tip-modal').find('.modal-body p').html(toolTip);
		$('#tool-tip-modal').modal();
	});

	/* Remove Modal Data on Close */
	$('#tool-tip-modal').on('hide.bs.modal', function() {
		var target = $(this);
		setTimeout(function() {
			$(target).find('.modal-body p, .modal-title').empty();
		}, 300);
	});
});






