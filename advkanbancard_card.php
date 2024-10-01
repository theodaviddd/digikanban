<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       advkanbancard_card.php
 *		\ingroup    advancedkanban
 *		\brief      Page to create/edit/view advkanbancard
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
dol_include_once('/advancedkanban/class/advkanbancard.class.php');
dol_include_once('/advancedkanban/lib/advancedkanban_advkanbancard.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("advancedkanban@advancedkanban", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref        = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm    = GETPOST('confirm', 'alpha');
$cancel     = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'advkanbancardcard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');

// Initialize technical objects
$object = new AdvKanbanCard($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->advancedkanban->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('advkanbancardcard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val)
{
	if (GETPOST('search_'.$key, 'alpha')) $search[$key] = GETPOST('search_'.$key, 'alpha');
}

if (empty($action) && empty($id) && empty($ref)) $action = 'view';

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.


$permissiontoread = $user->hasRight('advancedkanban', 'advkanban', 'read');
$permissiontoadd = $user->rights->advancedkanban->advkanbancard->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->advancedkanban->advkanbancard->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$permissionnote = $user->rights->advancedkanban->advkanbancard->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->advancedkanban->advkanbancard->write; // Used by the include of actions_dellink.inc.php
$upload_dir = $conf->advancedkanban->multidir_output[isset($object->entity) ? $object->entity : 1];

// Security check - Protection if external user
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->statut == $object::STATUS_DRAFT) ? 1 : 0);
//$result = restrictedArea($user, 'advkanbancard', $object->id, '', '', 'fk_soc', 'rowid', $isdraft);

//if (!$permissiontoread) accessforbidden();


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	$error = 0;

	$backurlforlist = dol_buildpath('/advancedkanban/advkanbancard_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) $backtopage = $backurlforlist;
			else $backtopage = dol_buildpath('/advancedkanban/advkanbancard_card.php', 1).'?id='.($id > 0 ? $id : '__ID__');
		}
	}

	$triggermodname = 'ADVANCEDKANBAN_SCRUMCARD_MODIFY'; // Name of trigger action code to execute when we modify record




	// Action to add record
	if ($action == 'add' && !empty($permissiontoadd)) {
		foreach ($object->fields as $key => $val) {

			if ($object->fields[$key]['type'] == 'duration') {
				if (GETPOST($key.'hour') == '' && GETPOST($key.'min') == '') {
					continue; // The field was not submited to be saved
				}
			} else {
				if (!GETPOSTISSET($key)) {
					continue; // The field was not submited to be saved
				}
			}
			// Ignore special fields
			if (in_array($key, array('rowid', 'entity', 'import_key'))) {
				continue;
			}
			if (in_array($key, array('date_creation', 'tms', 'fk_user_creat', 'fk_user_modif'))) {
				if (!in_array(abs($val['visible']), array(1, 3))) {
					continue; // Only 1 and 3 that are case to create
				}
			}

			// Set value to insert
			if (in_array($object->fields[$key]['type'], array('text', 'html'))) {
				$value = GETPOST($key, 'restricthtml');
			} elseif ($object->fields[$key]['type'] == 'date') {
				$value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int')); // for date without hour, we use gmt
			} elseif ($object->fields[$key]['type'] == 'datetime') {
				$value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), GETPOST($key.'sec', 'int'), GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'), 'tzuserrel');
			} elseif ($object->fields[$key]['type'] == 'duration') {
				$value = 60 * 60 * GETPOST($key.'hour', 'int') + 60 * GETPOST($key.'min', 'int');
			} elseif (preg_match('/^(integer|price|real|double)/', $object->fields[$key]['type'])) {
				$value = price2num(GETPOST($key, 'alphanohtml')); // To fix decimal separator according to lang setup
			} elseif ($object->fields[$key]['type'] == 'boolean') {
				$value = ((GETPOST($key) == '1' || GETPOST($key) == 'on') ? 1 : 0);
			} elseif ($object->fields[$key]['type'] == 'reference') {
				$tmparraykey = array_keys($object->param_list);
				$value = $tmparraykey[GETPOST($key)].','.GETPOST($key.'2');
			} else {
				if ($key == 'lang') {
					$value = GETPOST($key, 'aZ09') ?GETPOST($key, 'aZ09') : "";
				} else {
					$value = GETPOST($key, 'alphanohtml');
				}
			}
			if (preg_match('/^integer:/i', $object->fields[$key]['type']) && $value == '-1') {
				$value = ''; // This is an implicit foreign key field
			}
			if (!empty($object->fields[$key]['foreignkey']) && $value == '-1') {
				$value = ''; // This is an explicit foreign key field
			}

			//var_dump($key.' '.$value.' '.$object->fields[$key]['type']);
			$object->$key = $value;
			if ($val['notnull'] > 0 && $object->$key == '' && !is_null($val['default']) && $val['default'] == '(PROV)') {
				$object->$key = '(PROV)';
			}
			if ($val['notnull'] > 0 && $object->$key == '' && is_null($val['default'])) {
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv($val['label'])), null, 'errors');
			}

			// Validation of fields values
			if (getDolGlobalInt('MAIN_FEATURES_LEVEL') >= 1 || getDolGlobalString('MAIN_ACTIVATE_VALIDATION_RESULT')) {
				if (!$error && !empty($val['validate']) && is_callable(array($object, 'validateField'))) {
					if (!$object->validateField($object->fields, $key, $value)) {
						$error++;
					}
				}
			}
		}


		// récupération du bon ID en fonction du type d'élement lié
		$fk_element = GETPOST('fk_element', 'int');
		$element_type = GETPOST('element_type', 'aZ09');
		if (empty($fk_element) && !empty($element_type) && $element_type != '-1'){
			if (GETPOSTISSET($element_type.'_fk_element')) {
				$object->fk_element = intval(GETPOST($element_type.'_fk_element', 'int'));
			}
		}

		// Fill array 'array_options' with data from add form
		if (!$error) {
			$ret = $extrafields->setOptionalsFromPost(null, $object);
			if ($ret < 0) {
				$error++;
			}
		}

		if (!$error) {
			$result = $object->create($user);
			if ($result > 0) {
				// Creation OK
				if (isModEnabled('categorie') && method_exists($object, 'setCategories')) {
					$categories = GETPOST('categories', 'array:int');
					$object->setCategories($categories);
				}
				$urltogo = $backtopage ? str_replace('__ID__', $result, $backtopage) : $backurlforlist;
				$urltogo = preg_replace('/--IDFORBACKTOPAGE--/', $object->id, $urltogo); // New method to autoselect project after a New on another form object creation
				header("Location: ".$urltogo);
				exit;
			} else {
				$error++;
				// Creation KO
				if (!empty($object->errors)) {
					setEventMessages(null, $object->errors, 'errors');
				} else {
					setEventMessages($object->error, null, 'errors');
				}
				$action = 'create';
			}
		} else {
			$action = 'create';
		}
	}
	// Action to update record
	elseif ($action == 'update' && !empty($permissiontoadd)) {
		foreach ($object->fields as $key => $val) {
			// Check if field was submited to be edited
			if ($object->fields[$key]['type'] == 'duration') {
				if (!GETPOSTISSET($key.'hour') || !GETPOSTISSET($key.'min')) {
					continue; // The field was not submited to be saved
				}
			} elseif ($object->fields[$key]['type'] == 'boolean') {
				if (!GETPOSTISSET($key)) {
					$object->$key = 0; // use 0 instead null if the field is defined as not null
					continue;
				}
			} else {
				if (!GETPOSTISSET($key)) {
					continue; // The field was not submited to be saved
				}
			}
			// Ignore special fields
			if (in_array($key, array('rowid', 'entity', 'import_key'))) {
				continue;
			}
			if (in_array($key, array('date_creation', 'tms', 'fk_user_creat', 'fk_user_modif'))) {
				if (!in_array(abs($val['visible']), array(1, 3, 4))) {
					continue; // Only 1 and 3 and 4, that are cases to update
				}
			}

			// Set value to update
			if (preg_match('/^(text|html)/', $object->fields[$key]['type'])) {
				$tmparray = explode(':', $object->fields[$key]['type']);
				if (!empty($tmparray[1])) {
					$value = GETPOST($key, $tmparray[1]);
				} else {
					$value = GETPOST($key, 'restricthtml');
				}
			} elseif ($object->fields[$key]['type'] == 'date') {
				$value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int')); // for date without hour, we use gmt
			} elseif ($object->fields[$key]['type'] == 'datetime') {
				$value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), GETPOST($key.'sec', 'int'), GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'), 'tzuserrel');
			} elseif ($object->fields[$key]['type'] == 'duration') {
				if (GETPOST($key.'hour', 'int') != '' || GETPOST($key.'min', 'int') != '') {
					$value = 60 * 60 * GETPOST($key.'hour', 'int') + 60 * GETPOST($key.'min', 'int');
				} else {
					$value = '';
				}
			} elseif (preg_match('/^(integer|price|real|double)/', $object->fields[$key]['type'])) {
				$value = price2num(GETPOST($key, 'alphanohtml')); // To fix decimal separator according to lang setup
			} elseif ($object->fields[$key]['type'] == 'boolean') {
				$value = ((GETPOST($key, 'aZ09') == 'on' || GETPOST($key, 'aZ09') == '1') ? 1 : 0);
			} elseif ($object->fields[$key]['type'] == 'reference') {
				$value = array_keys($object->param_list)[GETPOST($key)].','.GETPOST($key.'2');
			} else {
				if ($key == 'lang') {
					$value = GETPOST($key, 'aZ09');
				} else {
					$value = GETPOST($key, 'alphanohtml');
				}
			}
			if (preg_match('/^integer:/i', $object->fields[$key]['type']) && $value == '-1') {
				$value = ''; // This is an implicit foreign key field
			}
			if (!empty($object->fields[$key]['foreignkey']) && $value == '-1') {
				$value = ''; // This is an explicit foreign key field
			}

			$object->$key = $value;
			if (!empty($val['notnull']) && $val['notnull'] > 0 && $object->$key == '' && is_null($val['default'])) {
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv($val['label'])), null, 'errors');
			}

			// Validation of fields values
			if (getDolGlobalInt('MAIN_FEATURES_LEVEL') >= 1 || getDolGlobalString('MAIN_ACTIVATE_VALIDATION_RESULT')) {
				if (!$error && !empty($val['validate']) && is_callable(array($object, 'validateField'))) {
					if (!$object->validateField($object->fields, $key, $value)) {
						$error++;
					}
				}
			}

			if (isModEnabled('categorie')) {
				$categories = GETPOST('categories', 'array');
				if (method_exists($object, 'setCategories')) {
					$object->setCategories($categories);
				}
			}
		}

		// récupération du bon ID en fonction du type d'élement lié
		$fk_element = GETPOST('fk_element', 'int');
		$element_type = GETPOST('element_type', 'aZ09');
		if (empty($fk_element) && !empty($element_type) && $element_type != '-1'){
			if (GETPOSTISSET($element_type.'_fk_element')) {
				$object->fk_element = intval(GETPOST($element_type.'_fk_element', 'int'));
			}
		}

		// Fill array 'array_options' with data from add form
		if (!$error) {
			$ret = $extrafields->setOptionalsFromPost(null, $object, '@GETPOSTISSET');
			if ($ret < 0) {
				$error++;
			}
		}

		if (!$error) {
			$result = $object->update($user);
			if ($result > 0) {
				$action = 'view';
			} else {
				$error++;
				// Creation KO
				setEventMessages($object->error, $object->errors, 'errors');
				$action = 'edit';
			}
		} else {
			$action = 'edit';
		}
	}
	else{
		// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
		include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';
	}



	if($action == 'confirm_setdone' && $confirm == 'yes') {
		$object->setStatusCommon($user, $object::STATUS_DONE, 0, 'SCRUMCARD_DONE');
	}

	// Actions when linking object each other
	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

	// Action to move up and down lines of object
	//include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

	// Action to build doc
	include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

	if ($action == 'set_thirdparty' && $permissiontoadd)
	{
		$object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
	}
	if ($action == 'classin' && $permissiontoadd)
	{
		$object->setProject(GETPOST('projectid', 'int'));
	}

	// Actions to send emails
	$triggersendname = 'ADVANCEDKANBAN_SCRUMCARD_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_SCRUMCARD_TO';
	$trackid = 'advkanbancard'.$object->id;
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';
}




/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("AdvKanbanCard");
$help_url = '';

$arrayofjs = array(
	'advancedkanban/js/advancekanban.js',
	'advancedkanban/js/liveedit.js'
);
$arrayofcss = array(
	'advancedkanban/css/liveedit.css',
	'advancedkanban/css/advanced-kanban.css'
);
llxHeader('', $title, $help_url, '', 0, 0, $arrayofjs, $arrayofcss);


// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("AdvKanbanCard")), '', $object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	print dol_get_fiche_head(array(), '');

	// Set some default values
	//if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

	print '<table class="border centpercent tableforfieldcreate">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	print dol_get_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="add" value="'.dol_escape_htmltag($langs->trans("Create")).'">';
	print '&nbsp; ';
	print '<input type="'.($backtopage ? "submit" : "button").'" class="button button-cancel" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'"'.($backtopage ? '' : ' onclick="javascript:history.go(-1)"').'>'; // Cancel for create does not post form if we don't know the backtopage
	print '</div>';

	print '</form>';
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("AdvKanbanCard"), '', $object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	print dol_get_fiche_head();

	print '<table class="border centpercent tableforfieldedit">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

	//Catégories (tags)
	print "\r\n<!-- Line ".__LINE__." category form -->\r\n";
    require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
    print '<tr><td>'.$langs->trans('Categories').'</td>';
    print '<td colspan="3">';
    $cat = new Categorie($db);
    $categories = $cat->containing($object->id, 'advkanbancard');
    $arrayselected = array();
    $cate_arbo = $form->select_all_categories('advkanbancard', '', 'parent', 64, 0, 1);
    foreach ($categories as $c) {
        $arrayselected[] = $c->id;
    }
    print img_picto('', 'category').Form::multiselectarray('categories', $cate_arbo, $arrayselected, '', 0, '', 0, '95%', '', 'category');
    print '</td></tr>';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	print dol_get_fiche_end();

	print '<div class="center"><input type="submit" class="button button-save" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals();

	$head = advkanbancardPrepareHead($object);
	print dol_get_fiche_head($head, 'card', $langs->trans("AdvKanbanCard"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteAdvKanbanCard'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
	}

	// Clone confirmation
	if ($action == 'clone') {
		// Create an array for form
		$formquestion = array();
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}

	// Confirmation of action xxxx
	if ($action == 'xxx')
	{
		$formquestion = array();
		/*
		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			// 'text' => $langs->trans("ConfirmClone"),
			// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
			// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
			// array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
		);
		*/
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
	}

	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) $formconfirm .= $hookmanager->resPrint;
	elseif ($reshook > 0) $formconfirm = $hookmanager->resPrint;

	// Print form confirm
	print $formconfirm;


	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="'.dol_buildpath('/advancedkanban/advkanbancard_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	$morehtmlref = '<div class="refidno">';
	$morehtmlref.= $object->label . '<br>';
//	$morehtmlref.= $object->showOutputField($object->fields['fk_stage'], 'fk_stage', $object->fk_stage);
	$morehtmlref .= '</div>';

	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">'."\n";

	// Common attributes
	$keyforbreak='description';	// We change column just before this field
	unset($object->fields['fk_stage']);				// Hide field already shown in banner
	unset($object->fields['label']);					// Hide field already shown in banner
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	// Other attributes. Fields from hook formObjectOptions and Extrafields.
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

    // Categories (tags)
    if(isModEnabled('categorie') {
        print '<tr><td class="valignmiddle">'.$langs->trans('Categories').'</td><td>';
        print $form->showCategories($object->id, 'advkanbancard', 1);
        print '</td></tr>';
    }

	print '</table>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div>';

	print dol_get_fiche_end();


	/*
	 * Lines
	 */

	if (!empty($object->table_element_line))
	{
		// Show object lines
		$result = $object->getLinesArray();

		print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline') ? '#addline' : '#line_'.GETPOST('lineid', 'int')).'" method="POST">
		<input type="hidden" name="token" value="' . newToken().'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline').'">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="id" value="' . $object->id.'">
		';

		if (!empty($conf->use_javascript_ajax) && $object->status == 0) {
			include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
		}

		print '<div class="div-table-responsive-no-min">';
		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline'))
		{
			print '<table id="tablelines" class="noborder noshadow" width="100%">';
		}

		if (!empty($object->lines))
		{
			$object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
		}

		// Form to add new line
		if ($object->status == 0 && $permissiontoadd && $action != 'selectlines')
		{
			if ($action != 'editline')
			{
				// Add products/services form
				$object->formAddObjectLine(1, $mysoc, $soc);

				$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			}
		}

		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline'))
		{
			print '</table>';
		}
		print '</div>';

		print "</form>\n";
	}


	// Buttons for actions

	if ($action != 'presend' && $action != 'editline') {
		print '<div class="tabsAction">'."\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			// Send
			if (empty($user->socid)) {
				print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&token='.newToken().'&action=presend&mode=init#formmailbeforetitle');
			}

			// Modify
			print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);

			// When draft
			if ($object->status == $object::STATUS_DRAFT) {
				print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}

			// When valid
			if ($object->status == $object::STATUS_READY) {
				print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
				print dolGetButtonAction($langs->trans('SetToDone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_setdone&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}

			// When done
			if($object->status == $object::STATUS_DONE) {
				print dolGetButtonAction($langs->trans('SetBackToReady'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_reopen&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}


			// Clone
			print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=clone&object=advkanbancard&token='.newToken(), '', $permissiontoadd);

			// Delete (need delete permission, or if draft, just need create/modify permission)
			print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
		}
		print '</div>'."\n";
	}


	// Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	if ($action != 'presend')
	{
		print '<div class="fichecenter"><div class="fichehalfleft">';
		print '<a name="builddoc"></a>'; // ancre

		$includedocgeneration = 0;

		// Documents
		if ($includedocgeneration) {
			$objref = dol_sanitizeFileName($object->ref);
			$relativepath = $objref.'/'.$objref.'.pdf';
			$filedir = $conf->advancedkanban->dir_output.'/'.$object->element.'/'.$objref;
			$urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
			$genallowed = $user->hasRight('advancedkanban', 'advkanban', 'read'); // If you can read, you can build the PDF to read content
			$delallowed = $user->rights->advancedkanban->advkanbancard->write; // If you can create/edit, you can remove a file on card
			print $formfile->showdocuments('advancedkanban:AdvKanbanCard', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
		}

		// Show links to link elements
		$linktoelem = $form->showLinkToObjectBlock($object, null, array('advkanbancard'));
		$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


		print '</div><div class="fichehalfright"><div class="ficheaddleft">';

		$MAXEVENT = 10;

		$morehtmlright = '<a href="'.dol_buildpath('/advancedkanban/advkanbancard_agenda.php', 1).'?id='.$object->id.'">';
		$morehtmlright .= $langs->trans("SeeAll");
		$morehtmlright .= '</a>';

		// List of actions on element
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions = new FormActions($db);
		$somethingshown = $formactions->showactions($object, $object->element.'@'.$object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlright);

		print '</div></div></div>';
	}

	//Select mail models is same action as presend
	if (GETPOST('modelselected')) $action = 'presend';

	// Presend form
	$modelmail = 'advkanbancard';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->advancedkanban->dir_output;
	$trackid = 'advkanbancard'.$object->id;

	include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}

//Recall to apply JS in the right order
// Fix a strange behavior from dolibarr
?>
    <script>
        $( document ).ready(function () {
            $('#categories').select2();
        });
    </script>
<?php

// End of page
llxFooter();
$db->close();
