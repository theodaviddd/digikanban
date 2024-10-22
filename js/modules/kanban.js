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

};
/**
 * Allow drag over a drop zone
 */
window.digikanban.kanban.allowDrop = function(e) {
	e.preventDefault();
}

/**
 * Handle the drag event
 */
window.digikanban.kanban.drag = function(e) {
	e.dataTransfer.setData("text", e.target.id);
}

/**
 * Handle the drop event
 */
window.digikanban.kanban.drop = function(e) {
	e.preventDefault();
	var data = e.dataTransfer.getData("text");
	e.target.appendChild(document.getElementById(data));
}

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
        <div class="kanban-column-body" ondrop="window.digikanban.kanban.drop(event)" ondragover="window.digikanban.kanban.allowDrop(event)">
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

	// Handle the "Enter" key event to save the new name
	input.addEventListener('keypress', function(event) {
		if (event.key === 'Enter') {
			nameElement.innerText = input.value;
			nameElement.style.display = 'inline';
			input.remove();
		}
	});

	// Replace the current column name with the input
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
	// Appel PHP pour récupérer la carte de l'objet
	const objectId = $(this).parent().find('.kanban-select-option').val();
	const categoryId = $(this).closest('.kanban-column').attr('category-id');
	const token = window.saturne.toolbox.getToken();

	let objectType = $('#object_type').val();
	let url = $('#ajax_actions_url').val();

	url += '?action=add_object_to_column&object_id=' + objectId + '&category_id=' + categoryId + '&token=' + token + '&object_type=' + objectType;

	$.ajax({
		url: url,
		type: 'POST',
		processData: false,
		contentType: false,
		success: function(resp) {
			// Add response (the object card) into the column
			let kanbanColumn = $('.kanban-column[category-id="' + categoryId + '"]');
			kanbanColumn.find('.kanban-column-body').append(resp);

			// Rebind drag-and-drop events for the newly added card
			$('.info-box').attr('draggable', 'true');
			$('.info-box').on('dragstart', function(event) {
				window.digikanban.kanban.drag(event.originalEvent);
			});
		},
		error: function() {
			console.log("Failed to add object to column.");
		}
	});
}
