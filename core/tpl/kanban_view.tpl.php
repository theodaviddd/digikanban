<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kanban Board</title>
	<link rel="stylesheet" href="kanban.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div id="kanban-board" class="kanban-board">

	<?php
	// Cette section gère l'affichage des colonnes et des objets associés dans le Kanban
	// Itération sur les catégories (colonnes du Kanban)
	if (is_array($columns) && !empty($columns)) {
		foreach ($columns as $column) {
			print '<div class="kanban-column">';
			print '<div class="kanban-column-header">';
			print '<span class="column-name" ondblclick="window.digikanban.kanban.editColumn(this)">' . htmlspecialchars($column['label']) . '</span>';
			print '<i class="fas fa-pencil-alt edit-icon" onclick="window.digikanban.kanban.editColumn(this.previousElementSibling)"></i>';
			print '</div>';

			// Corps de la colonne où les objets sont listés
			print '<div class="kanban-column-body" id="' . strtolower(str_replace(' ', '-', $column['label'])) . '-column" ondrop="window.digikanban.kanban.drop(event)" ondragover="window.digikanban.kanban.allowDrop(event)">';
			// Vous pouvez ici ajouter du contenu lié aux objets de chaque colonne
			print '</div>';

			// Affichage du sélecteur d'objet pour ajouter de nouveaux objets dans chaque colonne
			print '<div class="add-item">';
			print '<form method="POST" action="add_object_to_kanban.php">'; // Form pour ajouter un objet
			print $objectSelector;
			print '<button type="submit" class="validate-button">Valider</button>';
			print '</form>';
			print '</div>';
			print '</div>';
		}
	}
	?>

	<div class="kanban-add-column" onclick="window.digikanban.kanban.addColumn()">
		<div class="add-column-text">+ Ajouter une colonne</div>
	</div>
</div>

<script src="kanban.js"></script>
</body>
</html>
