jQuery(function ($) {

	/** utilisé par advKanbanCard */

	// Toggle display
	$('body').on('change', '.adv-kanban-form-toggle-trigger', function(e) {
		$('.adv-kanban-form-toggle-target[data-toggle-trigger="' + $(this).attr('id') + '"]').attr('data-display', 0);
		$('.adv-kanban-form-toggle-target[data-toggle-trigger="' + $(this).attr('id') + '"][data-toggle-trigger-value="' + $(this).val() + '"]').attr('data-display', 1);
	});

	// Reset imput
	$('body').on('change', '.adv-kanban-form-reset-trigger[data-reset-target][data-reset-value]', function(e) {
		let resetTarget = $($(this).attr('data-reset-target'));
		if( resetTarget != undefined ){
			resetTarget.val($(this).attr('data-reset-value'));
		}
	});

	// Set imput value
	$('body').on('change', '.adv-kanban-form-cloneval-trigger[data-cloneval-target]', function(e) {
		let clonevalTarget = $($(this).attr('data-cloneval-target'));
		if( clonevalTarget != undefined ){
			clonevalTarget.val($(this).attr('data-cloneval-value'));
		}
	});
});
