<?php


//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification


$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path = dirname(__FILE__) . '/';

// Include and load Dolibarr environment variables
$res = 0;
if (!$res && file_exists($path . "main.inc.php")) $res = @include($path . "main.inc.php");
if (!$res && file_exists($path . "../main.inc.php")) $res = @include($path . "../main.inc.php");
if (!$res && file_exists($path . "../../main.inc.php")) $res = @include($path . "../../main.inc.php");
if (!$res && file_exists($path . "../../../main.inc.php")) $res = @include($path . "../../../main.inc.php");
if (!$res) die("Include of master fails");

require_once __DIR__ . '/lib/advancedkanban.lib.php';
require_once __DIR__ . '/class/advKanbanTools.class.php';

global $langs, $db, $hookmanager, $user, $mysoc;
/**
 * @var DoliDB $db
 */
$hookmanager->initHooks('advkanbanliveupdateinterface');

// Load traductions files requiredby by page
$langs->loadLangs(array("advancedkanban@advancedkanban", "other", 'main'));

$action = GETPOST('action');

// Security check
if (!isModEnabled("advancedkanban")) accessforbidden('Module not enabled');


// AJOUT DE LIGNE DANS LES DOCUMENTS
if ($action === 'liveFieldUpdate') {
	// output
	$jsonResponse = new stdClass();
	_actionLiveUpdate($jsonResponse);
	print json_encode($jsonResponse); // , JSON_PRETTY_PRINT
}

$db->close();    // Close $db database opened handler

/**
 * @param stdClass $jsonResponse
 * @return bool|void
 */
function _actionLiveUpdate(&$jsonResponse){
	global $user, $langs;

	$jsonResponse = new stdClass();
	$jsonResponse->result = 0;
	$jsonResponse->msg = '';
	$jsonResponse->newToken = newToken();

	$element = GETPOST("element", 'aZ09');
	$fk_element = GETPOST("fk_element", "int");
	$field = GETPOST("field", "alphanohtml");
	$value = GETPOST('value', 'alphanohtml');
	$jsonResponse->value = $value;
	$jsonResponse->displayValue = $value;
	$forceUpdate = GETPOST('forceUpdate', 'int');

	// Todo use object display value like update form
	$TAllowedObjects = array(
		'advancedkanban_advkanbanlist' => array(
			'allowedFields' => array('label')
		)
	);

	// TODO use fields object rights
	$TWriteRight = array(
		'advancedkanban_advkanbanlist' => $user->hasRight('advancedkanban', 'advkanban', 'write'),
	);


	// Test rights
	if ($user->socid > 0 || empty($TWriteRight[$element])) {
		$jsonResponse->msg = $langs->transnoentities('NotEnoughRights');
		$jsonResponse->result = -1;
		return false;
	}

	// Test element
	if (empty($TAllowedObjects[$element])) {
		$jsonResponse->msg = $langs->transnoentities('NotAllowedObject');
		$jsonResponse->result = -1;
		return false;
	}

	$object = AdvKanbanTools::getObjectByElement($element, $fk_element);
	if($object < 0){
		$jsonResponse->msg = $langs->transnoentities('ErrorFetchingObject');
		$jsonResponse->result = -1;
		return false;
	}

	if($object == 0){
		$jsonResponse->msg = $langs->transnoentities('ElementNotFound');
		$jsonResponse->result = -1;
		return false;
	}

	if(empty(($object->fields[$field]))){
		$jsonResponse->msg = $langs->transnoentities('TargetFieldNotTargetable').' '.$field;
		$jsonResponse->result = -1;
		return false;
	}

	if(empty(($object->fields[$field]))){
		$jsonResponse->msg = $langs->transnoentities('TargetFieldNotTargetable');
		$jsonResponse->result = -1;
		return false;
	}

	if (!empty($object->fields[$field]['validate'])
		&& is_callable(array($object, 'validateField'))
		&& !$object->validateField($object->fields, $field, $value)
	) {
		$jsonResponse->msg = $object->errorsToString();
		$jsonResponse->result = -1;
		return false;
	}


	$fieldTypeArray = explode(':', $object->fields[$field]['type']);
	$typeField = reset($fieldTypeArray);

	if(in_array($typeField , array('real'))){

		// Dans le cas des heures stockées en float
		if(strpos($value, 'h') !== false || strpos($value, 'H') !== false || strpos($value, ':') !== false  ){
			$value = str_replace("h", "H", $value);
			$value = str_replace(":", "H", $value);
			$pos = strpos($value, 'H');
			$h = floatval(substr($value, 0, $pos));
			$m = floatval(substr($value, $pos+1));
			$value = $h+round($m/60,6);
		}
	}


	if(in_array($typeField , array('integer', 'real'))){
		$value = price2num($value);
		$jsonResponse->value = price($value);
	}

	// prevent useless save value
	if(!$forceUpdate && $object->$field == $value){
		$jsonResponse->result = 0;
		return;
	}

	$object->$field = $value;
	if(is_callable(array($object, 'showOutputField'))){
		$jsonResponse->displayValue = $object->showOutputField($object->fields[$field], $field, $object->{$field});
	}


	if($object->updateCommon($user) > 0){
//		$jsonResponse->msg = $langs->trans('Updated'); // remove because create spam
		$jsonResponse->result = 1;
		return true;
	}
	else{
		$jsonResponse->result = -1;
		$jsonResponse->msg = $object->errorsToString();
		return false;
	}
}
