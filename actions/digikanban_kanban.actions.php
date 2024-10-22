<?php

// Load digikanban environment
if (file_exists('../digikanban.main.inc.php')) {
	require_once __DIR__ . '/../digikanban.main.inc.php';
} elseif (file_exists('../../digikanban.main.inc.php')) {
	require_once __DIR__ . '/../../digikanban.main.inc.php';
} else {
	die('Include of digikanban main fails');
}

global $db, $user;

require_once DOL_DOCUMENT_ROOT . '/class/categorie.class.php';
require_once __DIR__ . '/../lib/digikanban_kanban.lib.php';

$categorie = new Categorie($db);



$elementArray = get_kanban_linkable_objects();


$category_id = GETPOST('category_id');
$object_id = GETPOST('object_id');
$object_type = GETPOST('object_type');
$category_name = GETPOST('category_name');

$objectLinkedClassPath = $elementArray[$object_type]['class_path'];
require_once DOL_DOCUMENT_ROOT . '/' . $objectLinkedClassPath;
$objectLinked = $elementArray[$object_type]['className'];

$categorie->fetch($category_id);
$linkedCategories = $categorie->get_filles();


$action = GETPOST('action');
$categorie = new Categorie($db);

if ($action == 'renameColumn') {
	$categorie->fetch($category_id);
	$categorie->label = $category_name;
	$categorie->update($user);
}

if ($action == 'addObject') {
	$objectLinked->fetch($object_id);
	$categorie->fetch($category_id);
	$result = $objectLinked->add_type($newObject, $categorie);
}
