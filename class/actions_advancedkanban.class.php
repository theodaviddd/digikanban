<?php
/* Copyright (C) 2021 Maxime Kohlhaas <maxime@m-development.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    advancedkanban/class/actions_advancedkanban.class.php
 * \ingroup advancedkanban
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsAdvancedKanban
 */
require_once __DIR__ . '/../backport/v19/core/class/commonhookactions.class.php';
class ActionsAdvancedKanban extends \advancedkanban\RetroCompatCommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}




	/**
	 * elementList Method Hook Call
	 *
	 * @param array $parameters parameters
	 * @param Object &$object Object to use hooks on
	 * @param string &$action Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param object $hookmanager class instance
	 * @return void
	 */
	public function emailElementlist($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;
		$langs->load('advancedkanban@advancedkanban');

//		$img = '<img src="'.dol_buildpath('advancedkanban/img/object_advkanban.png',1).'" >';
//		$this->results['advkanban'] = $img.' '.$langs->trans('AdvKanbanMailModel');

		return 0;
	}



	/**
	 * @param $parameters
	 * @param $object
	 * @param $hookmanager
	 * @return int
	 */
	public function constructCategory($parameters, $object, $hookmanager) {
		global $langs;
		$langs->load('advancedkanban@advancedkanban');
        $this->results = ['advkanbancard' => [
            'id' => 10489114,
            'code' => 'advkanbancard',
            'cat_fk' => 'advkanbancard',
            'cat_table' => 'advkanbancard',
            'obj_class' => 'AdvKanbanCard',
            'obj_table' => 'advancedkanban_advkanbancard',
        ]];
        return 0;
    }

	/**
	 * Overloading the doMassActions function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions(&$parameters, &$model, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db, $massaction;
		$contextArray = explode(':', $parameters['context']);
		$massActionConfirm = GETPOST('massaction_confirm');
		$element = GETPOST('new_card_element');

		// MASS ACTION
		if (
			(in_array('thirdpartylist', $contextArray)
				|| in_array('propallist', $contextArray)
				|| in_array('orderlist', $contextArray)
				|| in_array('invoicelist', $contextArray)
				|| in_array('molist', $contextArray)
				|| in_array('ticketlist', $contextArray)
				|| in_array('projectlist', $contextArray)
			)
			&& $user->hasRight('advancedkanban', 'advkanban', 'write')
			&& $massActionConfirm == 'addtoadvancedkanban'
		) {
			$langs->load('advancedkanban@advancedkanban');
			include_once __DIR__ . '/advKanbanTools.class.php';
			include_once __DIR__ . '/advkanbanlist.class.php';
			include_once __DIR__ . '/advkanbancard.class.php';


			$cardStatic = new AdvKanbanCard($db);
			$cardStatic->getCompatibleElementList();

			if(!isset($cardStatic->compatibleElementList[$element])){
				$this->error = 'Invalid element';
				return -1;
			}

			$kanbanId = GETPOST('fk_advkanban');
			$kanbanLabel = GETPOST('cardlabel');
			$toSelect = $parameters['toselect'];

			if($kanbanId<=0) {
				$this->error = $langs->trans('KanbanInvalid');
				return -1;
			}

			if(empty($toSelect)) {
				$this->error = $langs->trans('SelectAtLeastOneDocument');
				return 0;
			}


			$kanban = AdvKanbanTools::getObjectByElement('advancedkanban_advkanban', $kanbanId);
			if(!$kanban){
				$this->error = 'Fail loading AdvKanban';
				return -1;
			}

			$backLogList = new AdvKanbanList($db);
			$resFetch = $backLogList->fetchFromKanbanAndListRefCode($kanbanId, 'backlog');
			if($resFetch<0){
				$this->error = $backLogList->errorsToString();
				return -1;
			}
			elseif($resFetch >0){
				$countAdded = 0;
				foreach ($toSelect as $selectedId){

					$card = new AdvKanbanCard($db);
					$card->label = !empty($kanbanLabel)?$kanbanLabel:'';

					$card->fk_element = $selectedId;
					$card->element_type = $element;

					$card->fk_advkanbanlist = $backLogList->id;

					if(!$card->validateField($card->fields, 'fk_element', $card->fk_element)){
						$this->error = 'Fail creating AdvKanbanCard : '.$card->getFieldError('fk_element');
						return -1;
					}

					if(!$card->validateField($card->fields, 'fk_advkanbanlist', $card->fk_advkanbanlist)){
						$this->error = 'Fail creating AdvKanbanCard : '.$card->getFieldError('fk_advkanbanlist');
						return -1;
					}

					if(!$card->validateField($card->fields, 'label', $card->label)){
						$this->error = 'Fail creating AdvKanbanCard : '.$card->getFieldError('label');
						return -1;
					}

					//Gestion du rang
					$card->fk_rank = $backLogList->getMaxRankOfKanBanListItems()+1;

					$res = $card->create($user);
					if($res<=0) {
						$this->errors[] = $card->errorsToString();
						return -1;
					}

					$countAdded++;
				}

				if($countAdded){
					setEventMessage($langs->trans('xAdvKanbanCardCreatedIn',$countAdded).' '.$kanban->getNomUrl());
				}


			}
		}
	}

	/*
	 * Overloading the addMoreMassActions function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$model, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db;

		$ret = '';

		$contextArray = explode(':', $parameters['context']);
		$massAction = GETPOST('massaction');

		// MASS ACTION
		if (
			(	in_array('thirdpartylist', $contextArray)
				|| in_array('propallist', $contextArray)
				|| in_array('orderlist', $contextArray)
				|| in_array('invoicelist', $contextArray)
				|| in_array('molist', $contextArray)
				|| in_array('ticketlist', $contextArray)
				|| in_array('projectlist', $contextArray)
			)
			&& $user->hasRight('advancedkanban', 'advkanban', 'write')
		) {
			$langs->load("advancedkanban@advancedkanban");
			$selected = '';
			if($massAction == 'addtoadvancedkanban'){
				$selected = ' selected="selected" ';
			}
			$ret .= '<option value="addtoadvancedkanban" '.$selected.' >' . $langs->trans('massaction_add_to_advkanban') . '</option>';

			$this->resprints = $ret;
		}
	}


	/**
	 * Overloading the doPreMassActions function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doPreMassActions($parameters, &$model, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db;

		$ret = '';

		$massAction = GETPOST('massaction');
		$massActionConfirm = GETPOST('massaction_confirm');
		$contextArray = explode(':', $parameters['context']);

		// MASS ACTION
		if (
			(	in_array('thirdpartylist', $contextArray)
				|| in_array('propallist', $contextArray)
				|| in_array('orderlist', $contextArray)
				|| in_array('invoicelist', $contextArray)
				|| in_array('molist', $contextArray)
				|| in_array('ticketlist', $contextArray)
				|| in_array('projectlist', $contextArray)
			)
			&& $user->hasRight('advancedkanban', 'advkanban', 'write')
			&& $massAction == 'addtoadvancedkanban'
			&& $massActionConfirm != 'addtoadvancedkanban'
		) {
			include_once __DIR__ . '/../class/advkanban.class.php';
			include_once __DIR__ . '/../class/advkanbanlist.class.php';
			$kanbanList = new AdvKanbanList($db);
			$kanbanList->fields['fk_advkanban']['type'] = 'integer:AdvKanban:advancedkanban/class/advkanban.class.php:1:(status:IN:'.AdvKanban::STATUS_VALIDATED.')';
			$this->resprints = '<div style="padding: 10px 0 20px 0;" >';
			$this->resprints.= '<fieldset style="max-width: 800px;    margin-left: auto;    margin-right: auto;" >';
			$this->resprints.= '<legend>'.$langs->trans('SelectAnKanbanAndGiveLabel').'</legend>';
			$this->resprints.= '<table style="width: 100%;">';

			$this->resprints.= '<tr>';
			$this->resprints.= '	<td><label>'.$langs->trans('NewKanbanCardLabel').'</label></td>';
			$this->resprints.= '	<td><input type="text" name="cardlabel" placeholder="'.$langs->trans('NewCard').'" ></td>';
			$this->resprints.= '</tr>';

			$this->resprints.= '<tr>';
			$this->resprints.= '	<td><label>'.$langs->trans('SelectAnKanban').'</label></td>';
			$this->resprints.= '	<td>'.$kanbanList->showInputField([], 'fk_advkanban', '', '', '', '', 0, 1).'</td>';
			$this->resprints.= '</tr>';
			$this->resprints.= '</table>';

			if(in_array('thirdpartylist', $contextArray)){
				$element = 'societe';
			}elseif(in_array('propallist', $contextArray)){
				$element = 'propal';
			}elseif(in_array('orderlist', $contextArray)){
				$element = 'commande';
			}elseif(in_array('invoicelist', $contextArray)){
				$element = 'facture';
			}elseif(in_array('molist', $contextArray)){
				$element = 'mo';
			}elseif(in_array('ticketlist', $contextArray)){
				$element = 'ticket';
			}elseif(in_array('projectlist', $contextArray)){
				$element = 'projet';
			}

			$this->resprints.= '<input type="hidden" name="new_card_element" value="'.$element.'" />';
			$this->resprints.= '<input type="hidden" name="massaction" value="addtoadvancedkanban" />';

			$this->resprints.= '	<div style="margin-top: 20px;" >';
			$this->resprints.= '	 	<button class="button pull-right" type="submit" name="massaction_confirm" value="addtoadvancedkanban">'.$langs->trans('Apply').'</button>';
			$this->resprints.= '		<button class="button pull-right" type="submit" name="massaction" value="">'.$langs->trans('Cancel').'</button>';
			$this->resprints.= '	</div>';

			$this->resprints.= '</fieldset>';
			$this->resprints.= '</div>';
		}
	}

	/*
	 * Overloading the addMoreMassActions function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		global $user, $langs,$db;

		$contextArray = explode(':', $parameters['context']);



		include_once __DIR__ . '/advKanbanTools.class.php';
		include_once __DIR__ . '/advkanbancard.class.php';

		$staticKanbanCard = new AdvKanbanCard($db);


		if($staticKanbanCard->isCompatibleElement($object) && $user->hasRight('advancedkanban', 'advkanban', 'read')){
			$langs->load("advancedkanban@advancedkanban");


			$sql = /** @lang MySQL */ 'SELECT k.rowid fk_kanban, l.rowid fk_kanbanlist FROM '.$db->prefix().'advancedkanban_advkanban k ';
			$sql.= ' JOIN '.$db->prefix().'advancedkanban_advkanbanlist l ON(l.fk_advkanban = k.rowid) ';
			$sql.= ' JOIN '.$db->prefix().'advancedkanban_advkanbancard c ON(c.fk_advkanbanlist = l.rowid) ';
			$sql.= ' WHERE  c.fk_element = '.intval($object->id);
			$sql.= ' AND c.element_type = \''.$db->escape($object->element).'\' ';
			$sql.= ' AND k.status IN(0,1,2) ';
			$sql.= ' GROUP BY k.rowid, l.rowid ';
			$sql.= ' ORDER BY k.rowid, l.rowid ';
			$sql.= ' LIMIT 50';// au cas ou...


			$out = '';
			$res = $db->query($sql);
			if ($res) {
				if ($db->num_rows($res) > 0) {
					while ($obj = $db->fetch_object($res)) {
						$kanban = AdvKanbanTools::getObjectByElement('advancedkanban_advkanban', $obj->fk_kanban);
						$kanbanList = AdvKanbanTools::getObjectByElement('advancedkanban_advkanbanlist', $obj->fk_kanbanlist);

						$out.= (!empty($out)?', ':'');

						$out.= '<span class="nowrap">';
						$out.= $kanban->getNomUrl(1).' - ';
						$out.= '<span class="fa fa-arrow-right" ></span> <a href="'.dol_buildpath('advancedkanban/advkanban_view.php', 1).'?id='.$kanban->id.'#board-'.$kanbanList->id.'-dropdown-menu" >'.$kanbanList->label.'</a>';
						$out.= '</span>';
					}
				}
			}

			if(!empty($out)){
//				$this->resprints = '<tr><td>'.$langs->trans('AdvKanban').'</td><td class="valuefield">'.$out.'</td></tr>'; // marche pas
				print '<tr><td>'.$langs->trans('AdvKanban').'</td><td class="valuefield">'.$out.'</td></tr>';
			}

			return 0;
		}

	}
}
