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
require_once __DIR__ . '/class/advJsonResponse.class.php';
require_once __DIR__ . '/class/advkanbanlist.class.php';
require_once __DIR__ . '/class/advkanbancard.class.php';
require_once __DIR__ . '/class/advKanbanTools.class.php';
if (!class_exists('Validate')) { require_once DOL_DOCUMENT_ROOT . '/core/class/validate.class.php'; }

global $langs, $db, $hookmanager, $user, $mysoc;
/**
 * @var DoliDB $db
 */
$hookmanager->initHooks('advkanbaninterface');

// Load traductions files requiredby by page
$langs->loadLangs(array("advancedkanban@advancedkanban","advancedkanban@advancedkanban", "other", 'main'));

$action = GETPOST('action');

// Security check
if (!isModEnabled("advancedkanban")) accessforbidden('Module not enabled');

$jsonResponse = new AdvJsonResponse();

// TODO : ajouter des droits et une vérification plus rigoureuse actuellement il n'y a pas de droit sur le kanban il faut peut-être en ajouter
if (!$user->hasRight('advancedkanban', 'advkanban', 'read')) {
    $jsonResponse->msg = $langs->trans('NotEnoughRights');
    $jsonResponse->result = 0;
}
elseif($action === 'addKanbanList') {
	_actionAddList($jsonResponse);
}
elseif($action === 'setKanbanValue') {
	_actionSetKanbanValue($jsonResponse);
}
elseif ($action === 'getAllBoards') {
	_actionGetAllBoards($jsonResponse);
}
elseif ($action === 'getAllItemToList') {
	_actionAddItemToList($jsonResponse);
}
elseif ($action === 'getAdvKanbanCardData') {
	_actionGetAdvKanbanCardData($jsonResponse);
}
elseif ($action === 'splitAdvKanbanCard') {
	_actionSplitAdvKanbanCard($jsonResponse);
}
elseif ($action === 'dropItemToList') {
	_actionDropItemToList($jsonResponse);
}
elseif ($action === 'changeListOrder') {
	_actionChangeListOrder($jsonResponse);
}
elseif ($action === 'assignMeToCard') {
	_actionAssignUserToCard($jsonResponse);
}
elseif ($action === 'toggleAssignMeToCard') {
	_actionAssignUserToCard($jsonResponse, false, true);
}
elseif ($action === 'removeMeFromCard') {
	_actionRemoveUserToCard($jsonResponse);
}
elseif ($action === 'removeList') {
	_actionRemoveList($jsonResponse);
}
elseif ($action === 'removeCard') {
	_actionRemoveCard($jsonResponse);
}
elseif ($action === 'getCardTags') {
    _actionGetCardTags($jsonResponse);
}
elseif ($action === 'updateCardTags') {
    _actionUpdateCardTags($jsonResponse);
}
else{
	$jsonResponse->msg = 'Action not found';
}

print $jsonResponse->getAdvJsonResponse();

$db->close();    // Close $db database opened handler

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionAddList($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");
	$validate = new Validate($db, $langs);

	if(empty($data['fk_kanban'])){
		$jsonResponse->msg = 'Need Kanban Id';
		return false;
	}

	$fk_kanban = $data['fk_kanban'];

	if(!$validate->isNumeric($fk_kanban)){
		$jsonResponse->msg = $validate->error;
		return false;
	}

	/**
	 * @var AdvKanban $kanban
	 */
	$kanban = AdvKanbanTools::getObjectByElement('advancedkanban_advkanban', $fk_kanban);
	if(!$kanban){
		$jsonResponse->msg = $langs->trans('RequireValidExistingElement');
		return false;
	}


	$kanbanList = new AdvKanbanList($db);
	$kanbanList->fk_advkanban = $kanban->id;


	$kanbanList->fk_rank = 0;
	$obj = $db->getRow('SELECT MAX(fk_rank) maxRank FROM '.MAIN_DB_PREFIX.$kanbanList->table_element . ' WHERE fk_advkanban = '.intval($kanban->id));
	if($obj){
		$kanbanList->fk_rank = intval($obj->maxRank) + 1;
	}

	if(!empty($data['label'])){
		$kanbanList->label = $data['label'];
	}else{
		$kanbanList->label = $langs->trans('NewList');
	}

	foreach ($kanbanList->fields as $field => $value) {
		if (!empty($val['validate'])
			&& is_callable(array($kanbanList, 'validateField'))
			&& !$kanbanList->validateField($kanbanList->fields, $field, $kanbanList->{$field})
		) {
			$jsonResponse->msg = $kanbanList->errorsToString();
			$jsonResponse->result = 0;
			return false;
		}
	}


	if($kanbanList->create($user) > 0){
		$jsonResponse->result = 1;

		$jsonResponse->data = $kanbanList->getKanBanListObjectFormatted();

		return true;
	}
	else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('CreateError') . ' : ' . $kanbanList->errorsToString();
		return false;
	}
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionSetKanbanValue($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanban', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");
	$validate = new Validate($db, $langs);

	if(empty($data['fk_kanban'])){
		$jsonResponse->msg = 'Need Kanban Id';
		return false;
	}

	$fk_kanban = $data['fk_kanban'];

	if(!$validate->isNumeric($fk_kanban)){
		$jsonResponse->msg = $validate->error;
		return false;
	}

	if(!empty($data['field_Key'])){
		$jsonResponse->msg = $langs->trans('RequireFieldKey');
		return false;
	}

	$fieldKey = $data['field_key'];


	if(!isset($data['field_value'])){
		$jsonResponse->msg = $langs->trans('RequireFieldValue');
		return false;
	}

	$fieldValue = $data['field_value'];

	/**
	 * @var AdvKanban $kanban
	 */
	$kanban = AdvKanbanTools::getObjectByElement('advancedkanban_advkanban', $fk_kanban);
	if(!$kanban){
		$jsonResponse->msg = $langs->trans('RequireValidExistingElement');
		return false;
	}

	if(in_array($fieldKey, array('background_url', 'label')) && !isset($kanban->fields[$fieldKey])){
		$jsonResponse->msg = $langs->trans('RequireValidFieldToModify');
		return false;
	}

	$kanban->{$fieldKey} = $fieldValue;

	if(!$kanban->validateField($kanban->fields, $fieldKey, $fieldValue)) {
		$jsonResponse->msg = $kanban->getFieldError($fieldKey);
		return false;
	}

	if($kanban->update($user) > 0){
		$jsonResponse->result = 1;
		return true;
	}
	else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('CreateError') . ' : ' . $kanban->errorsToString();
		return false;
	}
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionGetAllBoards($jsonResponse){
	global $user, $langs, $db;

	$data = GETPOST("data", "array");

	if (!$user->hasRight('advancedkanban', 'advkanban', 'read')) {
		$jsonResponse->msg = $langs->trans('NotEnoughRights');
		$jsonResponse->result = 0;
		return false;
	}

	if(empty($data['fk_kanban'])){
		$jsonResponse->msg = 'Need Kanban Id';
		return false;
	}

	$fk_kanban = $data['fk_kanban'];
	$kanban = _checkObjectByElement('advancedkanban_advkanban', $fk_kanban, $jsonResponse);
	if(!$kanban){
		return false;
	}

	$staticKanbanList = new AdvKanbanList($db);
	$kanbanLists = $staticKanbanList->fetchAll('ASC', 'fk_rank', 0, 0, array('fk_advkanban' => intval($kanban->id)));

	/**
	 * @var AdvKanbanList[] $kanbanLists
	 */

	if(is_array($kanbanLists)){
		$jsonResponse->result = 1;
		$jsonResponse->data = new stdClass();

		$jsonResponse->data->boards = array();


		// All listes stored in databases
		foreach ($kanbanLists as $kanbanList){
			$jsonResponse->data->boards[] = $kanbanList->getKanBanListObjectFormatted();
		}

		$jsonResponse->data->md5 = md5(json_encode($jsonResponse->data->boards));

		return true;
	}
	else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('Get boards error') . ' : ' . $staticKanbanList->errorsToString();
		return false;
	}
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool
 */
function _actionGetCardTags($jsonResponse) {
    global $user, $langs, $db;

    $data = GETPOST('data', 'array');
    $jsonResponse->debug = $data;

    if(empty($data['card-id'])) {
        $jsonResponse->msg = 'Need car Id';
        return false;
    }

    $fk_card = $data['card-id'];
    /** @var AdvKanbanCard $card */
    $card = _checkObjectByElement('advancedkanban_advkanbancard', $fk_card, $jsonResponse);
    if(! $card) {
        return false;
    }

    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
    $cat = new Categorie($db);

    /** @var Categorie[] $categories */
    $categories = $cat->get_all_categories('advkanbancard');

    $categoriesCard = $cat->containing($card->id, 'advkanbancard', 'id');

    $jsonResponse->data = new stdClass();
    $jsonResponse->data->tags = []; // les tags de la card

    if($categories > 0){
        foreach($categories as $category) {
            $tag = new stdClass();
            $tag->id = $category->id;
            $tag->label = $category->label;
            $tag->selected = 0;
            if(in_array($category->id, $categoriesCard)){
                $tag->selected = 1;
            }

            $jsonResponse->data->tags[] = $tag;
        }
    }

    $jsonResponse->result = 1;
    return true;
}

/**
 *
 *
 * @param AdvJsonResponse $jsonResponse
 * @return bool
 */
function _actionUpdateCardTags($jsonResponse) {
    global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

    $data = GETPOST('data', 'array');
    $jsonResponse->debug = $data;

    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
    $advkanbancard = new AdvKanbanCard($db);
    $advkanbancard->fetch($data['card-id']);
    $advkanbancard->setCategories($data['tags']);

    $jsonResponse->result = 1;
    return true;
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionRemoveList($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$jsonResponse->result = 0;

	$data = GETPOST("data", "array");

	// check kanban list data
	if(empty($data['kanban_list_id'])){
		$jsonResponse->msg = 'Need Kanban list Id';
		return false;
	}

	// toDo vérifier le status du kanban aussi

	$kanbanListId = $data['kanban_list_id'];
	$kanbanList = _checkObjectByElement('advancedkanban_advkanbanlist', $kanbanListId, $jsonResponse);
	/**
	 * @var AdvKanbanList $kanbanList
	 */
	if(!$kanbanList){
		$jsonResponse->msg = 'Invalid Kanban list load';
		return false;
	}

	if(!$user->hasRight('advancedkanban', 'advkanbancard', 'write')){
		$jsonResponse->msg = 'Not enough rights';
		return false;
	}

	if($kanbanList->delete($user) <= 0){
		$jsonResponse->msg = $langs->trans('ErrorDeletingKanbanList').' : '.$kanbanList->errorsToString();
		return false;
	}

	$jsonResponse->result = 1;
	return true;
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionRemoveCard($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$jsonResponse->result = 0;

	$data = GETPOST("data", "array");

	// check kanban item data
	if(empty($data['card_id'])){
		$jsonResponse->msg = 'Need Kanban card Id';
		return false;
	}

	// toDo vérifier le status du kanban aussi

	$kanbanCardId = $data['card_id'];
	$kanbanCard = _checkObjectByElement('advancedkanban_advkanbancard', $kanbanCardId, $jsonResponse);
	/**
	 * @var AdvKanbanCard $kanbanCard
	 */
	if(!$kanbanCard){
		$jsonResponse->msg = 'Invalid Kanban card load';
		return false;
	}

	if(!$user->hasRight('advancedkanban', 'advkanbancard', 'write')){
		$jsonResponse->msg = 'Not enough rights';
		return false;
	}

	if($kanbanCard->delete($user) <= 0){
		$jsonResponse->msg = 'Error deleting card : '.$kanbanCard->errorsToString();
		return false;
	}

	$jsonResponse->result = 1;
	return true;
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionAddItemToList($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	// check kanban list data
	if(empty($data['fk_kanbanlist'])){
		$jsonResponse->msg = 'Need Kanbanlist Id';
		return false;
	}

	// toDo vérifier le status du kanban aussi

	$fk_kanbanlist = $data['fk_kanbanlist'];
	$kanbanList = _checkObjectByElement('advancedkanban_advkanbanlist', $fk_kanbanlist, $jsonResponse);
	if(!$kanbanList){
		return false;
	}

	$kanbanCard = new AdvKanbanCard($db);
	$kanbanCard->fk_rank = $kanbanList->getMaxRankOfKanBanListItems() + 1;

	$kanbanCard->dropInKanbanList( $user,  $kanbanList, false, true);

	if(!empty($data['label'])){
		$kanbanCard->label = $data['label'];
	}else{
		$kanbanCard->label = $langs->trans('NewCard');
	}

	foreach ($kanbanCard->fields as $field => $value) {
		if (!empty($val['validate'])
			&& is_callable(array($kanbanCard, 'validateField'))
			&& !$kanbanCard->validateField($kanbanCard->fields, $field, $kanbanList->{$field})
		) {
			$jsonResponse->msg = $kanbanCard->errorsToString();
			$jsonResponse->result = 0;
			return false;
		}
	}


	if($kanbanCard->create($user) > 0){
		$jsonResponse->result = 1;
		$jsonResponse->data = $kanbanCard->getAdvKanBanItemObjectFormatted();
		return true;
	}
	else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('CreateError') . ' for add item to list : ' . $kanbanCard->errorsToString();
		return false;
	}
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionGetAdvKanbanCardData($jsonResponse){
	global $db;

	$data = GETPOST("data", "array");

	// check kanban list data
	if(empty($data['id'])){
		$jsonResponse->msg = 'Need advkanbancard Id';
		return false;
	}

	$kanbanCard = new AdvKanbanCard($db);
	$res = $kanbanCard->fetch($data['id']);
	if($res <= 0){
		$jsonResponse->msg = 'Scrumcard fetch error';
		return false;
	}

	$jsonResponse->result = 1;
	$jsonResponse->data = $kanbanCard->getScrumKanBanItemObjectStd();
	return true;
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionDropItemToList($jsonResponse){
	global $langs, $db, $user;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	if(empty($data['card-id'])){
		$jsonResponse->msg = 'Need card Id';
		return false;
	}

	$kanbanCard = new AdvKanbanCard($db);
	if($kanbanCard->fetch($data['card-id']) <= 0){
		$jsonResponse->msg = 'Invalid card';
		return false;
	}

	if(empty($data['target-list-id'])){
		$jsonResponse->msg = 'Need target list Id';
		return false;
	}
	$target_fk_advkanbanlist = $data['target-list-id'];

	// toDo vérifier le status du kanban aussi

	$kanbanList = _checkObjectByElement('advancedkanban_advkanbanlist', $target_fk_advkanbanlist, $jsonResponse);
	if(!$kanbanList){
		return false;
	}

	if(!empty($data['before-card-id'])){
		$beforeAdvKanbanCard = new AdvKanbanCard($db);
		$res = $beforeAdvKanbanCard->fetch($data['before-card-id']);
		if($res<=0){
			$jsonResponse->msg = 'Need target list Id';
			return false;
		}

		// TODO : voir pour factoriser avec AdvKanbanCard::shiftAllCardRankAfterRank()

		$newRank = $beforeAdvKanbanCard->fk_rank;

		$crumCardsAfter = $db->getRows(
			/* @Lang SQL */
			'SELECT rowid id, fk_rank '
			. ' FROM '.MAIN_DB_PREFIX.$kanbanCard->table_element
			. ' WHERE fk_advkanbanlist ='.intval($kanbanList->id)
			. ' AND fk_rank >= '.intval($beforeAdvKanbanCard->fk_rank)
			. ' ORDER BY fk_rank ASC'
		);

		if(!empty($crumCardsAfter)){
			$db->begin();
			$error = 0;
			$nextRank = intval($newRank);
			foreach ($crumCardsAfter as $item){
				$nextRank++;
				$sqlUpdate = /* @Lang SQL */
					'UPDATE '.MAIN_DB_PREFIX.$kanbanCard->table_element
					. ' SET tms=NOW(), fk_rank = '.$nextRank
					. ' WHERE rowid = '.intval($item->id)
					. ';';

				$resUp = $db->query($sqlUpdate);
				if(!$resUp){
					$error++;
					break;
				}
			}

			if(!empty($error)){
				$db->rollback();
				$jsonResponse->result = 0;
				$jsonResponse->msg = $langs->trans('UpdateError') . ' : ' .$db->error();
				return false;
			}

			$kanbanCard->fk_rank = $newRank;

            $jsonResponse->data = new stdClass();
			$resDrop = $kanbanCard->dropInKanbanList( $user,  $kanbanList, false, false, $jsonResponse->data);

			if($resDrop>0){
				$db->commit();
				$jsonResponse->result = 1;
				return true;
			}
			else{
				$db->rollback();
				$jsonResponse->result = 0;
				$jsonResponse->msg = $langs->trans('UpdateError') . ' : ' .$kanbanCard->errorsToString();
				return false;
			}
		}
		else{
			$jsonResponse->result = 0;
			$jsonResponse->msg = $langs->trans('Card position error');
			return false;
		}
	}
	else{
		$newRank = $kanbanList->getMaxRankOfKanBanListItems() + 1;

		$kanbanCard->fk_rank = $newRank;
        $jsonResponse->data = new stdClass();
        $resDrop = $kanbanCard->dropInKanbanList( $user,  $kanbanList, false, false, $jsonResponse->data);

		if($resDrop>0){
			$jsonResponse->result = 1;
			return true;
		}
		else{
			$jsonResponse->result = 0;
			$jsonResponse->msg = $langs->trans('UpdateError') . ' : ' . $kanbanCard->errorsToString();
			return false;
		}
	}
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|void
 */
function _actionChangeListOrder($jsonResponse){
	global $user, $langs, $db;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	// Get kanban
	if(empty($data['fk_kanban'])){
		$jsonResponse->msg = 'Need Kanban Id';
		return false;
	}

	$fk_kanban = $data['fk_kanban'];
	$kanban = _checkObjectByElement('advancedkanban_advkanban', $fk_kanban, $jsonResponse);
	if(!$kanban){
		return false;
	}

	// Get list
	if(empty($data['list-id'])){
		$jsonResponse->msg = 'Need list Id';
		return false;
	}

	$kanbanListId = $data['list-id'];
	$kanbanList = _checkObjectByElement('advancedkanban_advkanbanlist', $kanbanListId, $jsonResponse);
	if(!$kanbanList){
		return false;
	}

	/**
	 * @var AdvKanbanList $kanbanList
	 */
	if($fk_kanban != $kanbanList->fk_advkanban){
		$jsonResponse->msg = 'kanban scope error';
		return false;
	}

	$newRank = 0;
	$obj = $db->getRow('SELECT MAX(fk_rank) maxRank FROM '.MAIN_DB_PREFIX.$kanbanList->table_element . ' WHERE fk_advkanban = '.intval($kanban->id));
	if($obj){
		$newRank = intval($obj->maxRank) + 1;
	}

	if(!empty($data['before-list-id'])) {
		$beforeKanbanList = _checkObjectByElement('advancedkanban_advkanbanlist', $data['before-list-id'], $jsonResponse);
		if(!$beforeKanbanList){
			return false;
		}

		$newRank = $beforeKanbanList->fk_rank;
	}


	$getListsAfter = $db->getRows(
	/* @Lang SQL */
		'SELECT rowid id, fk_rank '
		. ' FROM '.MAIN_DB_PREFIX.$kanbanList->table_element
		. ' WHERE rowid != '.intval($kanbanList->id)
		. ' AND fk_rank >= '.intval($newRank)
		. ' AND fk_advkanban = '.intval($fk_kanban)
		. ' ORDER BY fk_rank ASC'
	);

	if($getListsAfter===false) {
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('Card position query error').$db->error();
		return false;
	}

	$db->begin();
	$error = 0;

	if(!empty($getListsAfter)) {
		$nextRank = intval($newRank);
		foreach ($getListsAfter as $item) {
			$nextRank++;

			$sqlUpdate = /* @Lang SQL */
				'UPDATE ' . MAIN_DB_PREFIX . $kanbanList->table_element
				. ' SET tms=NOW(), fk_rank = ' . $nextRank
				. ' WHERE rowid = ' . intval($item->id);

			$resUp = $db->query($sqlUpdate);
			if (!$resUp) {
				$error++;
				break;
			}
		}

		if (!empty($error)) {
			$db->rollback();
			$jsonResponse->result = 0;
			$jsonResponse->msg = $langs->trans('UpdateError') . ' : ' . $db->error();
			return false;
		}
	}

	// Mise à jour de la liste elle même
	$sqlUpdate = /* @Lang SQL */
		' UPDATE '.MAIN_DB_PREFIX.$kanbanList->table_element
		. ' SET  tms=NOW(), fk_rank = '.intval($newRank)
		. ' WHERE rowid = '.intval($kanbanList->id).';';

	if($db->query($sqlUpdate)){
		$db->commit();
		$jsonResponse->result = 1;
		return true;
	}
	else{
		$db->rollback();
		$jsonResponse->result = 0;
		$jsonResponse->msg = $langs->trans('UpdateError') . ' : ' .$db->error();
		return false;
	}
}

/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|CommonObject
 */
function _checkObjectByElement($elementType, $id, $jsonResponse){
	global $langs, $db;

	$validate = new Validate($db, $langs);

	if(!$validate->isNumeric($id)){
		$jsonResponse->msg = $validate->error;
		return false;
	}

	$object = AdvKanbanTools::getObjectByElement($elementType, $id);
	if(!$object){
		$jsonResponse->msg = $elementType . ' : ' . $langs->trans('RequireValidExistingElement');
		return false;
	}

	return $object;
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @return bool|CommonObject
 */
function _actionSplitAdvKanbanCard($jsonResponse){
	global $langs, $db, $user;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	if(empty($data['id'])){
		$jsonResponse->msg = 'Need card Id';
		return false;
	}

	$kanbanCard = new AdvKanbanCard($db);
	if($kanbanCard->fetch($data['id']) <= 0){
		$jsonResponse->msg =  $langs->trans('RequireValidExistingElement');
		return false;
	}

	$errors = 0;

	if(empty($data['form']) || empty($data['form']['new-item-qty-planned'])){
		$jsonResponse->msg =  $langs->trans('RequireValidSplitData');
		return false;
	}

	if(!is_array($data['form']['new-item-qty-planned'])){
		$data['form']['new-item-qty-planned'] = array(
			$data['form']['new-item-qty-planned']
		);
	}

	if(!is_array($data['form']['new-item-label'])){
		$data['form']['new-item-label'] = array(
			$data['form']['new-item-label']
		);
	}

	foreach ($data['form']['new-item-qty-planned'] as $key => $qty){
		$newCardLabel = '';
		if(!empty($data['form']['new-item-label'][$key])){
			$newCardLabel = $data['form']['new-item-label'][$key];
		}

		if(is_array($newCardLabel)){ $newCardLabel = '';}
		if(!$kanbanCard->splitCard($qty, $newCardLabel, $user)){
			$jsonResponse->msg =  $kanbanCard->errorsToString();
			$errors++;
		}
	}

	if($errors==0){
		$jsonResponse->result = 1;
	}

	return $errors==0;
}


/**
 * @param AdvJsonResponse $jsonResponse
 * @param int|bool     $userId
 * @param bool         $toggle if contact already prevent it remove it
 * @return bool|void
 */
function _actionAssignUserToCard($jsonResponse, $userId = false, $toggle = false){
	global  $user, $db, $conf, $langs;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	// Get card id
	if(empty($data['card-id'])){
		$jsonResponse->msg = 'Need card Id';
		return false;
	}

	$cardId = $data['card-id'];
	$kanbanCard = _checkObjectByElement('advancedkanban_advkanbancard', $cardId, $jsonResponse);
	if(!$kanbanCard){
		return false;
	}
	/**
	 * @var AdvKanbanCard $kanbanCard
	 */
	$res = $kanbanCard->assignUserToCard($user, $userId, $toggle);
	if($res){
		$jsonResponse->result = 1;
		$jsonResponse->data = $kanbanCard->getAdvKanBanItemObjectFormatted();
		return true;
	}else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $kanbanCard->error;
		return false;
	}
}



/**
 * @param AdvJsonResponse $jsonResponse
 * @param int|bool         $userId
 * @return bool|void
 */
function _actionRemoveUserToCard($jsonResponse, $userId = false){
	global  $user, $db, $langs;

    if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
        $jsonResponse->msg = $langs->trans('NotEnoughRights');
        $jsonResponse->result = 0;
        return false;
    }

	$data = GETPOST("data", "array");

	// Get card id
	if(empty($data['card-id'])){
		$jsonResponse->msg = 'Need card Id';
		return false;
	}

	$cardId = $data['card-id'];
	$kanbanCard = _checkObjectByElement('advancedkanban_advkanbancard', $cardId, $jsonResponse);
	if(!$kanbanCard){
		return false;
	}

	/**
	 * @var AdvKanbanCard $kanbanCard
	 */
	$res = $kanbanCard->removeUserToCard($user, $userId);
	if($res){
		$jsonResponse->result = 1;
		$jsonResponse->data = $kanbanCard->getAdvKanBanItemObjectFormatted();
		return true;
	}else{
		$jsonResponse->result = 0;
		$jsonResponse->msg = $kanbanCard->error;
		return false;
	}
}

///**
// * @param AdvKanbanCard $kanbanCard
// * @param AdvKanbanList $kanbanList
// * @return void
// */
//function _setAdvKanbanCardValuesOnDropInList(AdvKanbanCard $kanbanCard, AdvKanbanList $kanbanList){
//
//	$kanbanCard->fk_advkanbanlist = $kanbanList->id;
//
//	$kanbanCard->status = AdvKanbanCard::STATUS_READY;
//	if($kanbanList->ref_code == 'backlog'){
//		$kanbanCard->status = AdvKanbanCard::STATUS_DRAFT;
//	}
//	elseif($kanbanList->ref_code == 'done'){
//		$kanbanCard->status = AdvKanbanCard::STATUS_DONE;
//	}
//}
