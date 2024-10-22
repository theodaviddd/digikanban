// Initialize kanban object
window.digikanban.kanban = {};

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

	kanbanBoard.appendChild(newColumn);
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
 * Show the select dropdown to choose an object to add
 */
window.digikanban.kanban.showSelect = function(button) {
	const columnBody = button.closest('.kanban-column').querySelector('.kanban-column-body');
	const select = document.createElement('select');
	select.innerHTML = `
        <option value="">Sélectionner un objet</option>
        <!-- Ajouter des options ici -->
    `;

	const submitButton = document.createElement('button');
	submitButton.innerText = "Ajouter";
	submitButton.onclick = function() {
		const selectedValue = select.value;
		if (selectedValue) {
			const newCard = document.createElement('div');
			newCard.classList.add('kanban-card');
			newCard.setAttribute('draggable', 'true');
			newCard.ondragstart = window.digikanban.kanban.drag;
			newCard.innerText = selectedValue;
			columnBody.appendChild(newCard);
		}
		select.remove();
		submitButton.remove();
	};

	columnBody.appendChild(select);
	columnBody.appendChild(submitButton);
}
