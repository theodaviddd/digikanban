<?php
/* Copyright (C) 2022-2023 EVARISK <technique@evarisk.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *   	\file       view/kanban/kanban_card.php
 *		\ingroup    digikanban
 *		\brief      Page to create/edit/view kanban
 */

// Load digikanban environment
if (file_exists('../digikanban.main.inc.php')) {
	require_once __DIR__ . '/../digikanban.main.inc.php';
} elseif (file_exists('../../digikanban.main.inc.php')) {
	require_once __DIR__ . '/../../digikanban.main.inc.php';
} else {
	die('Include of digikanban main fails');
}

// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/images.lib.php';

require_once '../class/kanban.class.php';
require_once '../lib/digikanban_kanban.lib.php';

// Global variables definitions
global $conf, $db, $hookmanager, $langs, $user, $langs;

// Load translation files required by the page
saturne_load_langs();

// Get parameters
$id                  = GETPOST('id', 'int');
$ref                 = GETPOST('ref', 'alpha');
$action              = GETPOST('action', 'aZ09');
$subaction           = GETPOST('subaction', 'aZ09');
$confirm             = GETPOST('confirm', 'alpha');
$cancel              = GETPOST('cancel', 'aZ09');
$contextpage         = GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'kanbancard'; // To manage different context of search
$backtopage          = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');

// Initialize objects
// Technical objets
$object         = new Kanban($db);
$extrafields    = new ExtraFields($db);

// View objects
$form = new Form($db);

$elementArray = get_kanban_linkable_objects();
$hookmanager->initHooks(array('kanbancard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$searchAll = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
	if (GETPOST('search_'.$key, 'alpha')) $search[$key] = GETPOST('search_'.$key, 'alpha');
}

if (empty($action) && empty($id) && empty($ref)) $action = 'view';

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

$permissiontoread   = $user->rights->digikanban->kanban->read;
$permissiontoadd    = $user->rights->digikanban->kanban->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->digikanban->kanban->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);

// Security check - Protection if external user
saturne_check_access($permissiontoread, $object);

/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook)) {
	$error = 0;

	$backurlforlist = dol_buildpath('/digikanban/view/kanban/kanban_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) $backtopage = $backurlforlist;
			else $backtopage = dol_buildpath('/digikanban/view/kanban_card.php', 1).'?id='.($id > 0 ? $id : '__ID__');
		}
	}

	// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
	include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';
}

/*
 * View
 */

$title    = $langs->trans(ucfirst($object->element));
$help_url = 'FR:Module_digikanban';
$moreJS   = ['/saturne/js/includes/hammer.min.js'];

saturne_header(1,'', $title, $help_url, '', 0, 0, $moreJS);

// Part to create
if ($action == 'create') {
	print load_fiche_titre($langs->trans('NewKanban'), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" id="createQuestionForm" enctype="multipart/form-data">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	print dol_get_fiche_head();

	print '<table class="border centpercent tableforfieldcreate kanban-table">'."\n";

	require_once DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_add.tpl.php';

	$linkableObjectsList = [];
	foreach($elementArray as $linkableElementType => $linkableElement) {
		$linkableObjectsList[$linkableElement['post_name']] = img_picto('', $linkableElement['picto']) . ' ' . $langs->trans($linkableElement['langs']);
	}

	//show dolibarr selector with $linkableObjectsList
	print '<tr><td class="fieldrequired minwidth400">'.$langs->trans('ObjectType').'</td><td>';
	print $form->selectarray('object_type', $linkableObjectsList, GETPOST('object_type', 'alpha'), 1, 0, 0, '', '', false);
	print '</td></tr>';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	print dol_get_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button wpeo-button" name="add" value="'.dol_escape_htmltag($langs->trans("Create")).'">';
	print '&nbsp; ';
	print ' &nbsp; <input type="button" id ="actionButtonCancelCreate" class="button" name="cancel" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print '</form>';

	dol_set_focus('input[name="label"]');
}

// Part to edit record
//if (($id || $ref) && $action == 'edit') {
//	print load_fiche_titre($langs->trans("ModifyQuestion"), '', $object->picto);
//
//	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
//	print '<input type="hidden" name="token" value="'.newToken().'">';
//	print '<input type="hidden" name="action" value="update">';
//	print '<input type="hidden" name="id" value="'.$object->id.'">';
//	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
//	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
//
//	print dol_get_fiche_head();
//
//	print '<table class="border centpercent tableforfieldedit kanban-table">'."\n";
//
//	// Ref -- Ref
//	print '<tr><td class="fieldrequired">' . $langs->trans("Ref") . '</td><td>';
//	print $object->ref;
//	print '</td></tr>';
//
//	//Label -- Libellé
//	print '<tr><td class="fieldrequired minwidth400">'.$langs->trans("Label").'</td><td>';
//	print '<input class="flat" type="text" size="36" name="label" id="label" value="'.$object->label.'">';
//	print '</td></tr>';
//
//	// Type -- Type
//	print '<tr><td class="fieldrequired"><label class="" for="type">' . $langs->trans("QuestionType") . '</label></td><td>';
//	print saturne_select_dictionary('type','c_kanban_type', 'ref', 'label', $object->type);
//	print '</td></tr>';
//
//	//Description -- Description
//	print '<tr><td><label class="" for="description">' . $langs->trans("Description") . '</label></td><td>';
//	$doleditor = new DolEditor('description', $object->description, '', 90, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_SOCIETE, ROWS_3, '90%');
//	$doleditor->Create();
//	print '</td></tr>';
//
//	// EnterComment -- Saisir les commentaires
//	print '<tr class="oddeven"><td class="minwidth400">';
//	print $langs->trans("EnterComment");
//	print '</td>';
//	print '<td>';
//	print '<input type="checkbox" id="enter_comment" name="enter_comment"' . ($object->enter_comment ? ' checked=""' : '') . '"> ';
//	print $form->textwithpicto('', $langs->trans('EnterCommentTooltip'));
//	print '</td></tr>';
//
//	// AuthorizeAnswerPhoto -- Utiliser les réponses de photos
//	print '<tr class="oddeven"><td class="minwidth400">';
//	print $langs->trans("AuthorizeAnswerPhoto");
//	print '</td>';
//	print '<td>';
//	print '<input type="checkbox" id="authorize_answer_photo" name="authorize_answer_photo"' . ($object->authorize_answer_photo ? ' checked=""' : '') . '"> ';
//	print $form->textwithpicto('', $langs->trans('AuthorizeAnswerPhotoTooltip'));
//	print '</td></tr>';
//
//	// ShowPhoto -- Utiliser les photos
//	print '<tr class="oddeven"><td class="minwidth400">';
//	print $langs->trans("ShowPhoto");
//	print '</td>';
//	print '<td>';
//	print '<input type="checkbox" id="show_photo" name="show_photo"' . ($object->show_photo ? ' checked=""' : '') . '"> ';
//	print $form->textwithpicto('', $langs->trans('ShowPhotoTooltip'));
//	print '</td></tr>';
//
//	// Photo OK -- Photo OK
//	print '<tr class="' . ($object->show_photo ? ' linked-medias photo_ok' : ' linked-medias photo_ok hidden' ) . '" style="' . ($object->show_photo ? ' ' : ' display:none') . '"><td><label for="photo_ok">' . $langs->trans("PhotoOk") . '</label></td><td class="linked-medias-list">'; ?>
<!--	<input hidden multiple class="fast-upload--><?php //echo getDolGlobalInt('SATURNE_USE_FAST_UPLOAD_IMPROVEMENT') ? '-improvement' : ''; ?><!--" id="fast-upload-photo-ok" type="file" name="userfile[]" capture="environment" accept="image/*">-->
<!--	<input type="hidden" class="fast-upload-options" data-from-subtype="photo_ok" data-from-subdir="photo_ok"/>-->
<!--	<label for="fast-upload-photo-ok">-->
<!--		<div class="wpeo-button button-square-50">-->
<!--			<i class="fas fa-camera"></i><i class="fas fa-plus-circle button-add"></i>-->
<!--		</div>-->
<!--	</label>-->
<!--	<input type="hidden" class="favorite-photo" id="photo_ok" name="photo_ok" value="--><?php //echo (dol_strlen($object->photo_ok) > 0 ? $object->photo_ok : GETPOST('favorite_photo_ok')) ?><!--"/>-->
<!--	<div class="wpeo-button button-square-50 open-media-gallery add-media modal-open" value="0">-->
<!--		<input type="hidden" class="modal-options" data-modal-to-open="media_gallery" data-from-id="--><?php //echo $object->id ?><!--" data-from-type="kanban" data-from-subtype="photo_ok" data-from-subdir="photo_ok"/>-->
<!--		<i class="fas fa-folder-open"></i><i class="fas fa-plus-circle button-add"></i>-->
<!--	</div>-->
<!--	--><?php
//	$relativepath = 'digikanban/medias/thumbs';
//	print saturne_show_medias_linked('digikanban', $conf->digikanban->multidir_output[$conf->entity] . '/kanban/'. $object->ref . '/photo_ok', 'small', '', 0, 0, 0, 50, 50, 0, 0, 0, 'kanban/'. $object->ref . '/photo_ok', $object, 'photo_ok', 1, $permissiontodelete);
//	print '</td></tr>';
//
//	print '<tr></tr>';
//
//	// Photo KO -- Photo KO
//	print '<tr class="' . ($object->show_photo ? ' linked-medias photo_ko' : ' linked-medias photo_ko hidden' ) . '" style="' . ($object->show_photo ? ' ' : ' display:none') . '"><td><label for="photo_ko">' . $langs->trans("PhotoKo") . '</label></td><td class="linked-medias-list">'; ?>
<!--	<input hidden multiple class="fast-upload--><?php //echo getDolGlobalInt('SATURNE_USE_FAST_UPLOAD_IMPROVEMENT') ? '-improvement' : ''; ?><!--" id="fast-upload-photo-ko" type="file" name="userfile[]" capture="environment" accept="image/*">-->
<!--	<input type="hidden" class="fast-upload-options" data-from-subtype="photo_ko" data-from-subdir="photo_ko"/>-->
<!--	<label for="fast-upload-photo-ko">-->
<!--		<div class="wpeo-button button-square-50">-->
<!--			<i class="fas fa-camera"></i><i class="fas fa-plus-circle button-add"></i>-->
<!--		</div>-->
<!--	</label>-->
<!--	<input type="hidden" class="favorite-photo" id="photo_ko" name="photo_ko" value="--><?php //echo (dol_strlen($object->photo_ko) > 0 ? $object->photo_ko : GETPOST('favorite_photo_ko')) ?><!--"/>-->
<!--	<div class="wpeo-button button-square-50 open-media-gallery add-media modal-open" value="0">-->
<!--		<input type="hidden" class="modal-options" data-modal-to-open="media_gallery" data-from-id="--><?php //echo $object->id ?><!--" data-from-type="kanban" data-from-subtype="photo_ko" data-from-subdir="photo_ko"/>-->
<!--		<i class="fas fa-folder-open"></i><i class="fas fa-plus-circle button-add"></i>-->
<!--	</div>-->
<!--	--><?php
//	print saturne_show_medias_linked('digikanban', $conf->digikanban->multidir_output[$conf->entity] . '/kanban/'. $object->ref . '/photo_ko', 'small', '', 0, 0, 0, 50, 50, 0, 0, 0, 'kanban/'. $object->ref . '/photo_ko', $object, 'photo_ko', 1, $permissiontodelete);
//	print '</td></tr>';
//
//	// Tags-Categories
//	if ($conf->categorie->enabled) {
//		print '<tr><td>'.$langs->trans("Categories").'</td><td>';
//		$categoryArborescence = $form->select_all_categories('kanban', '', 'parent', 64, 0, 1);
//		$c = new Categorie($db);
//		$cats = $c->containing($object->id, 'kanban');
//		$arrayselected = array();
//		if (is_array($cats)) {
//			foreach ($cats as $cat) {
//				$arrayselected[] = $cat->id;
//			}
//		}
//		print img_picto('', 'category', 'class="pictofixedwidth"').$form::multiselectarray('categories', $categoryArborescence, (GETPOSTISSET('categories') ? GETPOST('categories', 'array') : $arrayselected), '', 0, 'minwidth100imp maxwidth500 widthcentpercentminusxx');
//		print '<a class="butActionNew" href="' . DOL_URL_ROOT . '/categories/index.php?type=kanban&backtopage=' . urlencode($_SERVER['PHP_SELF'] . '?action=create') . '" target="_blank"><span class="fa fa-plus-circle valignmiddle paddingleft" title="' . $langs->trans('AddCategories') . '"></span></a>';
//		print "</td></tr>";
//	}
//
//	// Other attributes
//	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';
//
//	print '</table>';
//
//	print dol_get_fiche_end();
//
//	print '<div class="center"><input type="submit" class="button button-save wpeo-button" name="save" value="'.$langs->trans("Save").'">';
//	print ' &nbsp; <input type="submit" class="button button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
//	print '</div>';
//
//	print '</form>';
//}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$res = $object->fetch_optionals();

	saturne_get_fiche_head($object, 'card', $title);
	saturne_banner_tab($object);

	$formconfirm = '';

	// Lock confirmation
	if (($action == 'lock' && (empty($conf->use_javascript_ajax) || !empty($conf->dol_use_jmobile))) || (!empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile))) {
		$formconfirm .= $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('LockObject', $langs->transnoentities('The' . ucfirst($object->element))), $langs->trans('ConfirmLockObject', $langs->transnoentities('The' . ucfirst($object->element))), 'confirm_lock', '', 'yes', 'actionButtonLock', 350, 600);
	}

	// Clone confirmation
	if (($action == 'clone' && (empty($conf->use_javascript_ajax) || !empty($conf->dol_use_jmobile))) || (!empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile))) {
		// Define confirmation messages
		$formkanbanclone = [
			['type' => 'text', 'name' => 'clone_label', 'label' => $langs->trans('NewLabelForClone', $langs->transnoentities('The' . ucfirst($object->element))), 'value' => $langs->trans('CopyOf') . ' ' . $object->ref, 'size' => 24],
			['type' => 'checkbox', 'name' => 'clone_photos', 'label' => $langs->trans('ClonePhotos'), 'value' => 1],
			['type' => 'checkbox', 'name' => 'clone_categories', 'label' => $langs->trans('CloneCategories'), 'value' => 1],
		];
		$formconfirm .= $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('CloneObject', $langs->transnoentities('The' . ucfirst($object->element))), $langs->trans('ConfirmCloneObject', $langs->transnoentities('The' . ucfirst($object->element)), $object->ref), 'confirm_clone', $formkanbanclone, 'yes', 'actionButtonClone', 350, 600);
	}

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('Delete') . ' ' . $langs->transnoentities('The'  . ucfirst($object->element)), $langs->trans('ConfirmDeleteObject', $langs->transnoentities('The' . ucfirst($object->element))), 'confirm_delete', '', 'yes', 1);
	}

	// Call Hook formConfirm
	$parameters = ['formConfirm' => $formconfirm];
	$reshook    = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) {
		$formconfirm .= $hookmanager->resPrint;
	} elseif ($reshook > 0) {
		$formconfirm = $hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;

	print '<div class="clearboth"></div>';

	// Buttons for actions

	print dol_get_fiche_end();

	print '<div class="fichecenter"><div class="fichehalfright">';

	$maxEvent = 10;

	$morehtmlcenter = dolGetButtonTitle($langs->trans('SeeAll'), '', 'fa fa-bars imgforviewmode', dol_buildpath('/saturne/view/saturne_agenda.php', 1) . '?id=' . $object->id . '&module_name=digikanban&object_type=' . $object->element);

	// List of actions on element
	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
	$formactions = new FormActions($db);
	$somethingshown = $formactions->showactions($object, $object->element . '@' . $object->module, '', 1, '', $MAXEVENT, '', $morehtmlcenter);

	print '</div></div>';
}

// End of page
llxFooter();
$db->close();
