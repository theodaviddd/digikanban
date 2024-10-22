// Initialize kanban object
window.digikanban.kanban = {};

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return {void}
 */
window.digikanban.kanban.init = function() {
	window.digikanban.kanban.event();
};

/**
 * La méthode contenant tous les événements pour le control.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return {void}
 */
window.digikanban.kanban.event = function() {
	$(document).on('change', '.kanban-select-option', window.digikanban.kanban.selectOption);
	$(document).on('click', '.validate-button:not(.butActionRefused)', window.digikanban.kanban.addObjectToColumn);

	$('.info-box').attr('draggable', 'true');

	// Enable drag-and-drop for all box-flex-item elements
	$('.kanban-column-body').sortable({
		connectWith: '.kanban-column-body', // Allow dragging between columns
		placeholder: 'kanban-placeholder',  // CSS class for placeholder when dragging
		handle: '.info-box',           // Limit dragging to cards only
		tolerance: 'pointer',               // Make dragging smoother
		over: function() {
			// Add dragging class for visual feedback
			$(this).css('cursor', 'grabbing');
		},
		stop: function(event, ui) {
			// Trigger an AJAX call to save the new order of the cards after drop
			window.digikanban.kanban.saveCardOrder();
		},


	});
};
/**
 * Save the new card order after drag-and-drop.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @returns {void}
 */
window.digikanban.kanban.saveCardOrder = function() {
	let objectType = $('#object_type').val();
	let cardOrder = [];
	$('.kanban-column').each(function() {
		let columnId = $(this).attr('category-id');
		let cards = [];

		$(this).find('.info-box').each(function() {
			cards.push($(this).find('.checkforselect').attr('value'));
		});

		cardOrder.push({
			columnId: columnId,
			cards: cards
		});
	});
	let url = $('#ajax_actions_url').val();

	let token = window.saturne.toolbox.getToken();
	$.ajax({
		url: url + "?action=move_object&token=" + token + '&object_type=' + objectType,
		type: "POST",
		data: JSON.stringify({
			order: cardOrder
		}),
		contentType: "application/json",
		success: function(response) {
			console.log("Card order saved successfully.");
		},
		error: function() {
			console.log("Error saving card order.");
		}
	});
};

/**
 * Add a new column to the kanban board
 */
window.digikanban.kanban.addColumn = function() {
	const kanbanBoard = document.getElementById('kanban-board');
	const newColumn = document.createElement('div');
	newColumn.classList.add('kanban-column');

	newColumn.innerHTML = `
        <div class="kanban-column-header">
            <span class="column-name" ondblclick="window.digikanban.kanban.editColumn(this)">Nouvelle colonne</span>
            <i class="fas fa-pencil-alt edit-icon" onclick="window.digikanban.kanban.editColumn(this.previousElementSibling)"></i>
        </div>
        <div class="kanban-column-body">
        </div>
        <div class="add-item">
            <button onclick="window.digikanban.kanban.showSelect(this)">+ Ajouter un objet</button>
        </div>
    `;

	const addColumnElement = document.querySelector('.kanban-add-column');
	kanbanBoard.insertBefore(newColumn, addColumnElement);
}

/**
 * Edit the column name when clicking on the column name or pencil icon
 */
window.digikanban.kanban.editColumn = function(nameElement) {
	const currentName = nameElement.innerText;
	const input = document.createElement('input');
	input.type = 'text';
	input.value = currentName;
	input.classList.add('column-name-input');

	input.addEventListener('keypress', function(event) {
		if (event.key === 'Enter') {
			nameElement.innerText = input.value;
			nameElement.style.display = 'inline';
			input.remove();
		}
	});

	nameElement.style.display = 'none';
	nameElement.parentNode.insertBefore(input, nameElement);
	input.focus();
}

/**
 * Triggers when element is selected in the select box
 */
window.digikanban.kanban.selectOption = function() {
	const validateButton = $(this).parent().find('.validate-button');
	validateButton.removeClass('butActionRefused')
	validateButton.removeAttr('disabled')
}

window.digikanban.kanban.addObjectToColumn = function() {
	const objectId = $(this).parent().find('.kanban-select-option').val();
	const categoryId = $(this).closest('.kanban-column').attr('category-id');
	const token = window.saturne.toolbox.getToken();

	let objectType = $('#object_type').val();
	let url = $('#ajax_actions_url').val();

	window.saturne.loader.display($(this).parent().find('.kanban-select-option'));
	url += '?action=add_object_to_column&object_id=' + objectId + '&category_id=' + categoryId + '&token=' + token + '&object_type=' + objectType;
	$.ajax({
		url: url,
		type: 'POST',
		processData: false,
		contentType: false,
		success: function(resp) {
			let kanbanColumn = $('.kanban-column[category-id="' + categoryId + '"]');
			kanbanColumn.find('.kanban-column-body').append(resp);
			window.digikanban.kanban.refreshSelector()

			$('.wpeo-loader').removeClass('wpeo-loader');
		},
		error: function() {
			console.log("Failed to add object to column.");
		}
	});

}

window.digikanban.kanban.refreshSelector = function() {
	let token = window.saturne.toolbox.getToken();
	let querySeparator = window.saturne.toolbox.getQuerySeparator(document.URL);
	let form = $('.kanban-board').find('form');
	$.ajax({
		url: document.URL + querySeparator + 'token=' + token,
		type: 'POST',
		processData: false,
		contentType: false,
		success: function(resp) {
			form.each(function() {
				let selectorId = $(this).find('select').attr('id')
				$(this).replaceWith($(resp).find('#' + selectorId).closest('form')); // Remplacer uniquement si le nouveau sélecteur existe
			});
		},
		error: function() {
			console.log("Failed to refresh the selectors.");
		}
	});
};

