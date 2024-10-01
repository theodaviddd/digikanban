<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
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
 * \file        class/advkanbancard.class.php
 * \ingroup     advancedkanban
 * \brief       This file is a CRUD class file for AdvKanbanCard (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once __DIR__ . '/commonAdvKanban.trait.php';

/**
 * Class for AdvKanbanCard
 */
class AdvKanbanCard extends CommonObject
{
	use CommonAdvKanban;

//	/**
//	 * @var string ID of module.
//	 */
//	public $module = 'advancedkanban'; // already included in $this->element

	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'advancedkanban_advkanbancard';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'advancedkanban_advkanbancard';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 1;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for advkanbancard. Must be the part after the 'object_' into object_advkanbancard.png
	 */
	public $picto = 'advkanbancard@advancedkanban';


	const STATUS_DRAFT = 0;
	const STATUS_READY = 1;
	const STATUS_DONE = 2;

	public $compatibleElementList = array();

	/**
	 *  'type' if the field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'text:none', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'picto' is code of a picto to show before value in forms
	 *  'enabled' is a condition when the field must be managed (Example: 1 or 'getDolGlobalString('MY_SETUP_PARAM'))
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' and 'cssview' and 'csslist' is the CSS style to use on field. 'css' is used in creation and update. 'cssview' is used in view mode. 'csslist' is used for columns in lists. For example: 'maxwidth200', 'wordbreak', 'tdoverflowmax200'
	 *  'help' is a 'TranslationString' to use to show a tooltip on field. You can also use 'TranslationString:keyfortooltiponlick' for a tooltip on click.
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arraykeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
	 *  'autofocusoncreate' to have field having the focus on a create form. Only 1 field should have this property set to 1.
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'css'=>'left', 'comment'=>"Id"),
		'entity' => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>0, 'default'=>'1', 'index'=>1,),
		'label' => array('type'=>'varchar(255)', 'label'=>'Label', 'enabled'=>'1', 'position'=>30, 'notnull'=>0, 'visible'=>1, 'searchall'=>1, 'css'=>'minwidth300', 'showoncombobox'=>'1',),
		'date_limit' => array('type'=>'datetime', 'label'=>'DateLimit', 'enabled'=>'1', 'position'=>40, 'notnull'=>0, 'visible'=>-1,),
		'fk_rank' => array('type'=>'integer', 'label'=>'Rank', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'css'=>'left', 'comment'=>"Id"),
		'fk_advkanbanlist' => array('type'=>'integer:AdvKanbanList:advancedkanban/class/advkanbanlist.class.php', 'label'=>'AdvKanbanList', 'enabled'=>'1', 'position'=>55, 'notnull'=>1, 'visible'=>0, 'index'=>1, 'foreignkey'=>'advancedkanban_advkanbanlist.rowid',),
		'fk_element' => array('type' => 'integer','label' => 'AdvKanbanCardLinkedTo','help' => 'AdvKanbanCardLinkedToHelp','enabled' => 1,'visible' => 1,'notnull' => 0,'default' => 0,'index' => 1,'position' => 0),
		'element_type' => array('type' => 'varchar(40)','label' => 'element_type','enabled' => 1,'visible' => 0,'position' => 10,'required' => 0),
		'description' => array('type'=>'html', 'label'=>'Description', 'enabled'=>'1', 'position'=>60, 'notnull'=>0, 'visible'=>3,),
		'note_public' => array('type'=>'html', 'label'=>'NotePublic', 'enabled'=>'1', 'position'=>61, 'notnull'=>0, 'visible'=>0,),
		'note_private' => array('type'=>'html', 'label'=>'NotePrivate', 'enabled'=>'1', 'position'=>62, 'notnull'=>0, 'visible'=>0,),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>500, 'notnull'=>1, 'visible'=>-2,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>501, 'notnull'=>0, 'visible'=>-2,),
		'fk_user_creat' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthor', 'enabled'=>'1', 'position'=>510, 'notnull'=>1, 'visible'=>-2, 'foreignkey'=>'user.rowid',),
		'fk_user_modif' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModif', 'enabled'=>'1', 'position'=>511, 'notnull'=>-1, 'visible'=>-2, 'foreignkey'=>'user.rowid',),
		'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'enabled'=>'1', 'position'=>1000, 'notnull'=>-1, 'visible'=>-2,),
		'status' => array(
			'type'=>'smallint',
			'label'=>'Status',
			'enabled'=>'1',
			'position'=>1000,
			'notnull'=>1,
			'visible'=>2,
			'default' => 0,
			'index'=>1,
			'arrayofkeyval'=>array(
				'0'=>'Brouillon',
				'1'=>'ToDo',
				'2'=>'Termin&eacute;e'
			),),
	);
	public $rowid;
	public $entity;
	public $date_limit;
	public $label;
	public $fk_rank;
	public $fk_advkanbanlist;

	/** @var int $fk_element targeted element rowid */
	public $fk_element;

	/** @var string $element_type targeted element  */
	public $element_type;


	public $description;
	public $note_public;
	public $note_private;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $import_key = '';
	public $status = 0;
	// END MODULEBUILDER PROPERTIES

	/** @var object $elementObject targeted element object  */
	public $elementObject;

	// If this object has a subtable with lines

	// /**
	//  * @var string    Name of subtable line
	//  */
	// public $table_element_line = 'advancedkanban_advkanbancardline';

	// /**
	//  * @var string    Field with ID of parent key if this object has a parent
	//  */
	// public $fk_element = 'fk_advkanbancard';

	// /**
	//  * @var string    Name of subtable class that manage subtable lines
	//  */
	// public $class_element_line = 'AdvKanbanCardline';

	// /**
	//  * @var array	List of child tables. To test if we can delete object.
	//  */
	// protected $childtables = array();

	// /**
	//  * @var array    List of child tables. To know object to delete on cascade.
	//  *               If name matches '@ClassNAme:FilePathClass;ParentFkFieldName' it will
	//  *               call method deleteByParentField(parentId, ParentFkFieldName) to fetch and delete child object
	//  */
	// protected $childtablesoncascade = array('advancedkanban_advkanbancarddet');

	// /**
	//  * @var AdvKanbanCardLine[]     Array of subtable lines
	//  */
	// public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (!getDolGlobalString('MAIN_SHOW_TECHNICAL_ID') && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		//if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

		// Example to show how to set values of fields definition dynamically
		/*if ($user->hasRight('advancedkanban', 'advkanban', 'read')) {
			$this->fields['myfield']['visible'] = 1;
			$this->fields['myfield']['noteditable'] = 0;
		}*/

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs))
		{
			foreach ($this->fields as $key => $val)
			{
				if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval']))
				{
					foreach ($val['arrayofkeyval'] as $key2 => $val2)
					{
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}

		$this->status = self::STATUS_DRAFT;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $langs, $extrafields;
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) $object->fetchLines();

		// get lines so they will be clone
		//foreach($this->lines as $line)
		//	$line->fetch_optionals();

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);

		// Clear fields
		if (property_exists($object, 'ref')) $object->ref = empty($this->fields['ref']['default']) ? "Copy_Of_".$object->ref : $this->fields['ref']['default'];
		if (property_exists($object, 'label')) $object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		if (property_exists($object, 'status')) { $object->status = self::STATUS_DRAFT; }
		if (property_exists($object, 'date_creation')) { $object->date_creation = dol_now(); }
		if (property_exists($object, 'date_modification')) { $object->date_modification = null; }
		// ...
		// Clear extrafields that are unique
		if (is_array($object->array_options) && count($object->array_options) > 0)
		{
			$extrafields->fetch_name_optionals_label($this->table_element);
			foreach ($object->array_options as $key => $option)
			{
				$shortkey = preg_replace('/options_/', '', $key);
				if (!empty($extrafields->attributes[$this->table_element]['unique'][$shortkey]))
				{
					//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
					unset($object->array_options[$key]);
				}
			}
		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);
		if ($result < 0) {
			$error++;
			$this->error = $object->error;
			$this->errors = $object->errors;
		}

		if (!$error)
		{
			// copy internal contacts
			if ($this->copy_linked_contact($object, 'internal') < 0)
			{
				$error++;
			}
		}

		if (!$error)
		{
			// copy external contacts if same company
			if (property_exists($this, 'socid') && $this->socid == $object->socid)
			{
				if ($this->copy_linked_contact($object, 'external') < 0)
					$error++;
			}
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $object;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && !empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchLines()
	{
		$this->lines = array();

		$result = $this->fetchLinesCommon();
		return $result;
	}


	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return self[]|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
		else $sql .= ' WHERE 1 = 1';
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 't.rowid') {
					$sqlwhere[] = $key.'='.$value;
				}
				else if($key == 'customsql') {
					$sqlwhere[] = $value;
				}
				else if(in_array($this->fields[$key]['type'], array('date', 'datetime', 'timestamp'))) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				}
				else if(strpos($value, '%') === false) {
					$sqlwhere[] = $key.' IN ('.$this->db->sanitize($this->db->escape($value)).')';
				}
				else {
					$sqlwhere[] = $key.' LIKE \'%'.$this->db->escape($value).'%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ('.implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= ' '.$this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		global $langs;

		if ($this->element_type > 0 && $this->fk_element <= 0){
			$this->error = $langs->trans("EmptyElementLink");
			return -1;
		}
		return $this->updateCommon($user, $notrigger);
	}


	/**
	 * Update object into database
	 *
	 * @param User $user User that modifies
	 * @param AdvKanbanList $kanbanList
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 * @param bool $noUpdate false=launch update after, true=disable update
	 * @param Object $dataToResponse Usable to get some data
	 * @return int             <if KO, >0 if OK
	 */
	public function dropInKanbanList(User $user, AdvKanbanList $kanbanList, $notrigger = false, $noUpdate = false, &$dataToResponse = null)
	{

        $dataToResponse = new stdClass();

		$this->fk_advkanbanlist = $kanbanList->id;

		$this->status = AdvKanbanCard::STATUS_READY;
		if($kanbanList->ref_code == 'backlog'){
			$this->status = AdvKanbanCard::STATUS_DRAFT;
		}
		elseif($kanbanList->ref_code == 'done'){
			$this->status = AdvKanbanCard::STATUS_DONE;
		}

		// Impacter les objects liés
		/**
		 * Traitement de l'element attaché
		 */
		$res = $this->fetchElementObject();

		if($res){
			$elementObject = $this->elementObject;
			if(is_callable(array($elementObject, 'dropInKanbanList'))){
				$resDropForEl = $elementObject->dropInKanbanList($user, $this, $kanbanList, $notrigger, $noUpdate, $dataToResponse);
				if($resDropForEl < 0){
					if(!empty($elementObject->error)){
						$this->error = $elementObject->error;
					}

					if(!empty($elementObject->errors) && is_array($elementObject->errors)){
						$this->errors+= $elementObject->errors;
					}

					return $resDropForEl;
				}
			}
		}

		if($noUpdate){
			return 0;
		}

		return $this->update($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		global  $db,$langs;

		if($this->fetchElementObject()){
			if(is_callable(array($this->elementObject, 'onAdvKanbanCardDelete'))){
				if($this->elementObject->onAdvKanbanCardDelete( $this, $user, $notrigger)<0){
					$this->errors[] = $this->elementObject->error;
					return -1;
				}
			}
		}

		unset($this->fk_element); // avoid conflict with standard Dolibarr comportment

		return $this->deleteCommon($user, $notrigger);
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function deleteAllFromElement(User $user, $element_type, $fk_element, $notrigger = false)
	{
		global $langs;

		$kanbanCards = $this->fetchAll('','', 0, 0, array('fk_element'=>$fk_element, 'customsql' => 'element_type="'. $element_type.'"'));
		if(!is_array($kanbanCards)){
			$this->error = $langs->trans('ErrorOnFetchingAllAdvKanbanCardsAffectedToElement', $element_type);
			$this->errors[] = $this->error;
			return -1;
		}
		elseif (!empty($kanbanCards)){
			$this->db->begin();
			foreach ($kanbanCards as $kanbanCard){
				if($kanbanCard->delete($user, $notrigger)<0){
					$this->error = $kanbanCard->error;
					$this->errors[] = array_merge($this->errors,  $kanbanCard->errors);
					$this->db->rollback();
					return -1;
				}
			}
			$this->db->commit();
		}


		return 1;
	}

	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	bool 	$notrigger  false=launch triggers after, true=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, $idline, $notrigger = false)
	{
		if ($this->status < 0)
		{
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}


	/**
	 *	Validate object
	 *
	 *	@param		User	$user     		User making status change
	 *  @param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@return  	int						<=0 if OK, 0=Nothing done, >0 if KO
	 */
	public function validate($user, $notrigger = 0)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$error = 0;

		// Protection
		if ($this->status == self::STATUS_READY)
		{
			dol_syslog(get_class($this)."::validate action abandonned: already validated", LOG_WARNING);
			return 0;
		}

		$now = dol_now();

		$this->db->begin();



		if (!empty($num)) {
			// Validate
			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " SET status = ".self::STATUS_READY;
			if (!empty($this->fields['date_validation'])) $sql .= ", date_validation = '".$this->db->idate($now)."'";
			if (!empty($this->fields['fk_user_valid'])) $sql .= ", fk_user_valid = ".$user->id;
			$sql .= " WHERE rowid = ".$this->id;

			dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql)
			{
				dol_print_error($this->db);
				$this->error = $this->db->lasterror();
				$error++;
			}

			if (!$error && !$notrigger)
			{
				// Call trigger
				$result = $this->call_trigger('SCRUMCARD_VALIDATE', $user);
				if ($result < 0) $error++;
				// End call triggers
			}
		}

		if (!$error)
		{
			$this->oldref = '(PROV'.$this->id.')';
			$num = $this->id;
			// Rename directory if dir was a temporary ref
			if (preg_match('/^[\(]?PROV/i', $this->id))
			{
				// Now we rename also files into index
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'advkanbancard/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'advkanbancard/".$this->db->escape($this->id)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) { $error++; $this->error = $this->db->lasterror(); }

				// We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
				$oldref = dol_sanitizeFileName($this->id);
				$newref = dol_sanitizeFileName($num);
				$dirsource = $conf->advancedkanban->dir_output.'/advkanbancard/'.$oldref;
				$dirdest = $conf->advancedkanban->dir_output.'/advkanbancard/'.$newref;
				if (!$error && file_exists($dirsource))
				{
					dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

					if (@rename($dirsource, $dirdest))
					{
						dol_syslog("Rename ok");
						// Rename docs starting with $oldref with $newref
						$listoffiles = dol_dir_list($conf->advancedkanban->dir_output.'/advkanbancard/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
						foreach ($listoffiles as $fileentry)
						{
							$dirsource = $fileentry['name'];
							$dirdest = preg_replace('/^'.preg_quote($oldref, '/').'/', $newref, $dirsource);
							$dirsource = $fileentry['path'].'/'.$dirsource;
							$dirdest = $fileentry['path'].'/'.$dirdest;
							@rename($dirsource, $dirdest);
						}
					}
				}
			}
		}

		// Set new ref and current status
		if (!$error)
		{
			$this->ref = $num;
			$this->status = self::STATUS_READY;
		}

		if (!$error)
		{
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Set draft status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setDraft($user, $notrigger = 0)
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT)
		{
			return 0;
		}

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'SCRUMCARD_UNVALIDATE');
	}

	/**
	 *	Set done status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function done($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_READY)
		{
			return 0;
		}

		return $this->setStatusCommon($user, self::STATUS_DONE, $notrigger, 'SCRUMCARD_CLOSE');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_DONE)
		{
			return 0;
		}

		return $this->setStatusCommon($user, self::STATUS_READY, $notrigger, 'SCRUMCARD_REOPEN');
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *  @param  string  $option                     On what the link point to ('nolink', ...)
	 *  @param  int     $notooltip                  1=Disable tooltip
	 *  @param  string  $morecss                    Add more css on link
	 *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *  @return	string                              String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) $notooltip = 1; // Force disable tooltips

		$result = '';

		$label = img_picto('', 'object_'.$this->picto).' <u>'.$langs->trans("AdvKanbanCard").'</u>';
		if (isset($this->status)) {
			$label .= ' '.$this->getLibStatut(5);
		}
		$label .= '<br>';
		$label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;

		$url = dol_buildpath('/advancedkanban/advkanbancard_card.php', 1).'?id='.$this->id;

		if ($option != 'nolink')
		{
			// Add param to save lastsearch_values or not
			$add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
			if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) $add_save_lastsearch_values = 1;
			if ($add_save_lastsearch_values) $url .= '&save_lastsearch_values=1';
		}

		$linkclose = '';
		if (empty($notooltip))
		{
			if (getDolGlobalString('MAIN_OPTIMIZEFORTEXTBROWSER'))
			{
				$label = $langs->trans("ShowAdvKanbanCard");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
		} else $linkclose = ($morecss ? ' class="'.$morecss.'"' : '');

		$linkstart = '<a href="'.$url.'"';
		$linkstart .= $linkclose.'>';
		$linkend = '</a>';

		$result .= $linkstart;

		if (empty($this->showphoto_on_popup)) {
			if ($withpicto) $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
		} else {
			if ($withpicto) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

				list($class, $module) = explode('@', $this->picto);
				$upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
				$filearray = dol_dir_list($upload_dir, "files");
				$filename = $filearray[0]['name'];
				if (!empty($filename)) {
					$pospoint = strpos($filearray[0]['name'], '.');

					$pathtophoto = $class.'/'.$this->ref.'/thumbs/'.substr($filename, 0, $pospoint).'_mini'.substr($filename, $pospoint);
					if (empty(getDolGlobalString(strtoupper($module.'_'.$class).'_FORMATLISTPHOTOSASUSERS'))) {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div></div>';
					} else {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
					}

					$result .= '</div>';
				} else {
					$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
				}
			}
		}

		if ($withpicto != 2) $result .= $this->ref;

		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action, $hookmanager;
		$hookmanager->initHooks(array('advkanbancarddao'));
		$parameters = array('id'=>$this->id, 'getnomurl'=>$result);
		$reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) $result = $hookmanager->resPrint;
		else $result .= $hookmanager->resPrint;

		return $result;
	}

	/**
	 *  Return the label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode = 0)
	{
		// phpcs:enable
		if (empty($this->labelStatus) || empty($this->labelStatusShort))
		{
			global $langs;
			//$langs->load("advancedkanban@advancedkanban");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('StatusAdvKanbanCardDraft');
			$this->labelStatus[self::STATUS_READY] = $langs->trans('StatusAdvKanbanCardReady');
			$this->labelStatus[self::STATUS_DONE] = $langs->trans('StatusAdvKanbanCardDone');
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('StatusAdvKanbanCardDraft');
			$this->labelStatusShort[self::STATUS_READY] = $langs->trans('StatusAdvKanbanCardReady');
			$this->labelStatusShort[self::STATUS_DONE] = $langs->trans('StatusAdvKanbanCardDone');
		}

		$statusType = 'status'.$status;
		if ($status == self::STATUS_READY) $statusType = 'status4';
		if ($status == self::STATUS_DONE) $statusType = 'status6';

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql .= ' fk_user_creat, fk_user_modif';
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql .= ' WHERE t.rowid = '.$id;
		$result = $this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if (!empty($obj->fk_user_author))
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					if(isset($this->user_creation)){ $this->user_creation = $cuser; }
					if(isset($this->user_creation_id)){ $this->user_creation_id = $cuser; }
				}

				if (!empty($obj->fk_user_valid))
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					if(isset($this->user_validation)){ $this->user_validation = $vuser; }
					if(isset($this->user_validation_id)){ $this->user_validation_id = $vuser; }
				}

				if (!empty($obj->fk_user_cloture))
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					if(isset($this->user_cloture)){ $this->user_cloture = $cluser; }
					if(isset($this->user_cloture_id)){ $this->user_cloture_id = $cluser; }
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
			}

			$this->db->free($result);
		} else {
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}

	/**
	 * 	Create an array of lines
	 *
	 * 	@return array|int		array of lines if OK, <0 if KO
	 */
	public function getLinesArray()
	{
		$this->lines = array();

		$objectline = new AdvKanbanCardLine($this->db);
		$result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_advkanbancard = '.$this->id));

		if (is_numeric($result))
		{
			$this->error = $this->error;
			$this->errors = $this->errors;
			return $result;
		} else {
			$this->lines = $result;
			return $this->lines;
		}
	}

	/**
	 *  Returns the reference to the following non used object depending on the active numbering module.
	 *
	 *  @return string      		Object free reference
	 */
	public function getNextNumRef()
	{
		global $langs, $conf;
		$langs->load("advancedkanban@advancedkanban");

		if (!getDolGlobalString('ADVANCEDKANBAN_SCRUMCARD_ADDON')) {
			$conf->global->ADVANCEDKANBAN_SCRUMCARD_ADDON = 'mod_advkanbancard_standard';
		}

		if (getDolGlobalString('ADVANCEDKANBAN_SCRUMCARD_ADDON'))
		{
			$mybool = false;

			$file = getDolGlobalString('ADVANCEDKANBAN_SCRUMCARD_ADDON') . ".php";
			$classname = getDolGlobalString('ADVANCEDKANBAN_SCRUMCARD_ADDON');

			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir)
			{
				$dir = dol_buildpath($reldir."core/modules/advancedkanban/");

				// Load file with numbering class (if found)
				$mybool |= @include_once $dir.$file;
			}

			if ($mybool === false)
			{
				dol_print_error('', "Failed to include file ".$file);
				return '';
			}

			if (class_exists($classname)) {
				$obj = new $classname();
				$numref = $obj->getNextValue($this);

				if ($numref != '' && $numref != '-1')
				{
					return $numref;
				} else {
					$this->error = $obj->error;
					//dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
					return "";
				}
			} else {
				print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
				return "";
			}
		} else {
			print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
			return "";
		}
	}

	/**
	 * @return void
	 */
	public function showTags(){
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

		$cat = new Categorie($this->db);
		$categories = $cat->containing($this->id, 'advkanbancard');

		$toprint = array();
		foreach ($categories as $c) {
			$ways = $c->print_all_ways(' &gt;&gt; ', 'none', 0, 1);
			foreach ($ways as $way) {
				$title = "";
				if(strlen($c->description)>0){
					$title = ' title="'.dol_escape_htmltag($c->description,0,1).'" ';
				}
				$toprint[] = '<li '.$title.' data-category="'.$c->id.'" class="select2-search-choice-dolibarr noborderoncategories classfortooltip "'.($c->color ? ' style="background: #'.$c->color.';"' : ' style="background: #bbb"').'>'.$way.'</li>';
			}
		}

		if(!empty($toprint)){
			return '<div class="select2-container-multi-dolibarr"><ul class="select2-choices-dolibarr">'.implode(' ', $toprint).'</ul></div>';
		}
		else{
			return '';
		}
	}

	/**
	 * get this object formatted for jKanan
	 * @return stdClass
	 */
	public function getAdvKanBanItemObjectFormatted(){
		global $user, $conf, $langs;

		/**
		 * $cardDataObj : attention il doit être compatible avec l'objet js des items de kanban
		 * */
		$cardDataObj = new stdClass();
		$cardDataObj->id = 'advkanbancard-' . $this->id; // kanban dom id

		$cardDataObj->label = $this->label;
		$cardDataObj->type = 'adv-kanban-card';
		$cardDataObj->class = array();     // array of additional classes
		$cardDataObj->element = $this->element;
		$cardDataObj->socid = 0;
		$cardDataObj->cardUrl = dol_buildpath('/advancedkanban/advkanbancard_card.php',1).'?id='.$this->id;
		$cardDataObj->elementLinkedCardUrl = dol_buildpath('/advancedkanban/advkanbancard_card.php',1).'?id='.$this->id;
		$cardDataObj->objectId = $this->id;
		$cardDataObj->title = '';
		$cardDataObj->errorMsgs = array();

		$tpl = new stdClass();
		$tpl->useTime = false;
		$tpl->timeSpend = $tpl->timePlanned ='--';
		$tpl->timeDone = false;
		$tpl->dateLimit = $this->date_limit;
		$tpl->useAutoStatusBadge = true;
		$tpl->status = '';
		$tpl->linkedElementTitle = '';
		$tpl->tags = $this->showTags();


		$TContactUsersAffected = $this->liste_contact(-1,'internal');

		/**
		 * Traitement de l'element attaché
		 */
		$res = $this->fetchElementObject();

		if($res){
			$elementObject = $this->elementObject;

			$cardDataObj->type = 'adv-kanban-card-linked';

			/**
			 * OVERRIDE FOR CORE ELEMENT AND LOAD LANGS
			 */
			if($elementObject->element == 'propal'){
				/** @var Facture $elementObject */
				$langs->load('propal');
				$cardDataObj->type = 'adv-kanban-card-quotation';
				$cardDataObj->cardUrl = dol_buildpath('comm/propal/card.php',1).'?id='.$elementObject->id;
			}elseif($elementObject->element == 'commande'){
				$langs->load('orders');
				/** @var Commande $elementObject */
				$cardDataObj->type = 'adv-kanban-card-commande';
				$cardDataObj->cardUrl = dol_buildpath('commande/card.php',1).'?id='.$elementObject->id;
			}elseif($elementObject->element == 'facture'){
				$langs->load('bills');
				/** @var Facture $elementObject */
				$cardDataObj->type = 'adv-kanban-card-invoice';
				$cardDataObj->cardUrl = dol_buildpath('compta/facture/card.php',1).'?id='.$elementObject->id;
			}elseif($elementObject->element == 'societe'){
				$langs->load('bills');
				/** @var Societe $elementObject */
				$cardDataObj->type = 'adv-kanban-card-societe';
				$cardDataObj->cardUrl = dol_buildpath('societe/card.php',1).'?socid='.$elementObject->id;
			}elseif($elementObject->element == 'dolresource'){
				$langs->load('resource');
				/** @var Societe $elementObject */
				$cardDataObj->type = 'adv-kanban-card-resource';
				$cardDataObj->cardUrl = DOL_URL_ROOT.'/resource/card.php?id='.$elementObject->id;
			}elseif($elementObject->element == 'mo'){
				$langs->load('mo');
				$cardDataObj->type = 'adv-kanban-card-mo';
				$cardDataObj->cardUrl = DOL_URL_ROOT.'/mrp/mo_card.php?id='.$elementObject->id;
			}elseif($elementObject->element == 'ticket'){
				$langs->load('ticket');
				$cardDataObj->type = 'adv-kanban-card-ticket';
				$cardDataObj->cardUrl = DOL_URL_ROOT.'/ticket/card.php?id='.$elementObject->id;
			}elseif($elementObject->element == 'projet'){
				$langs->load('projects');
				$cardDataObj->type = 'adv-kanban-card-project';
				$cardDataObj->cardUrl = DOL_URL_ROOT.'/projet/card.php?id='.$elementObject->id;
			}




			if(!empty($this->elementObject->socid)){
				$cardDataObj->socid = $this->elementObject->socid;
			}

			if(method_exists($this->elementObject, 'getNomUrl')){
				$tpl->linkedElementTitle = $this->elementObject->getNomUrl(1);
			}


			if(defined( get_class($this->elementObject).'::OVERRIDE_KANBAN_CARD_CONTACTS' )) {
				$TContactUsersAffected = $elementObject->liste_contact(-1, 'internal');
			}
			$cardDataObj->element = $elementObject->element;
			$cardDataObj->targetelementid = $elementObject->id;

			if(getDolGlobalString('ADVKANBAN_KANBAN_DISPLAY_STATUS_MODE') && is_callable(array($elementObject, 'getLibStatut'))){
				$mode = 3; // dot
				if(getDolGlobalString('ADVKANBAN_KANBAN_DISPLAY_STATUS_MODE') == 'badge'){
					$mode = 5;
				}

				if($tpl->useAutoStatusBadge){
					try {
						$tpl->status.= $elementObject->getLibStatut($mode);
					}
					catch (Exception $e){
						// no catch
					}
				}
			}




			/**
			 * OVERRIDE FROM EXTERNAL MODULE ELEMENT
			 */
			if(method_exists($elementObject, 'getItemObjectFormattedForAdvKanBan') && is_callable(array($elementObject, 'getItemObjectFormattedForAdvKanBan'))){
				$objectFromElement = $elementObject->getItemObjectFormattedForAdvKanBan($this, $cardDataObj, $tpl);
				if($objectFromElement){
					return $objectFromElement;
				}
			}
		} elseif (!empty($this->element_type) && $this->fk_element > 0) {
			// l'object n'a pas été trouvé
			$cardDataObj->errorMsgs[] = $langs->trans('ObjectLinkBreak', $this->element_type, $this->fk_element);
		}


		if(!empty($cardDataObj->errorMsgs)){
			$cardDataObj->title.= '<span class="kanban-item__error_icon classfortooltip" title="'.dol_escape_htmltag(implode('<br/>', $cardDataObj->errorMsgs)).'"></span>';
		}

		$cardDataObj->title.= '<div class="kanban-item__header">';

		if(!empty($tpl->tags)){
			$cardDataObj->title.= '<span class="kanban-item__tags">'.$tpl->tags.'</span>';
		}

		if(!empty($tpl->linkedElementTitle)){
			$cardDataObj->title.= '<span class="kanban-item__linked-element-label">'.$tpl->linkedElementTitle.'</span>';
		}

		if(!empty($cardDataObj->socid)){
			$company = new Societe($this->db);
			if($company->fetch($cardDataObj->socid)>0){
				$cardDataObj->title.= '<span class="kanban-item__company">'.$company->name.'</span>';
			}
		}

		$cardDataObj->title.= '</div>';

		if(!empty($tpl->beforeItemBody)){
			$cardDataObj->title.= $tpl->beforeItemBody;
		}

		$cardDataObj->title.= '<div class="kanban-item__body">';

		if(isset($tpl->dateLimitTplOverride)){
			$cardDataObj->title.= $tpl->dateLimitTplOverride;
		} elseif(!empty($tpl->dateLimit)){
			$moreDateLimitClass = '';
			if($tpl->dateLimit < dol_now() && $this->status != self::STATUS_DONE){
				$moreDateLimitClass.= ' --times-up';
			}

			if(!empty($tpl->moreDateLimitClassOverride)){
				$moreDateLimitClass = $tpl->moreDateLimitClassOverride;
			}

			$cardDataObj->title.= '<span class="kanban-item__date-limit '.$moreDateLimitClass.'">';
			$cardDataObj->title.= '<span class="fa fa-clock"></span> ';
			$cardDataObj->title.= dol_print_date($tpl->dateLimit, '%d/%m/%Y %H:%M');
			$cardDataObj->title.= '</span>';
		}

		if(!empty($tpl->beforeItemLabel)){ $cardDataObj->title.= $tpl->beforeItemLabel; }
		$cardDataObj->title.= '<span class="kanban-item__label">'.$cardDataObj->label.'</span>';
		if(!empty($tpl->afterItemLabel)){ $cardDataObj->title.= $tpl->afterItemLabel; }

		$cardDataObj->title.= '</div>';


		$cardDataObj->title.= '<div class="kanban-item__footer">';
		if(!empty($tpl->prependItemFooter)){ $cardDataObj->title.= $tpl->prependItemFooter; }

		if($tpl->useTime){
			$cardDataObj->title.= '<span class="kanban-item__time-spend">';
			//$cardDataObj->title.= '<i class="fa fa-hourglass-o"></i> ';
			$cardDataObj->title.= '<span class="kanban-item__time-consumed" title="'.dol_escape_htmltag($langs->trans('QtyConsumed')).'"><span class="fa fa-hourglass"></i></span> '.$tpl->timeSpend.'</span>';
			if($tpl->timeDone !== false && !empty($tpl->timeDone)){
				$cardDataObj->title.= ' <span class="kanban-item__time-done" title="'.dol_escape_htmltag($langs->trans('QtyDone')).'"><span class="fa fa-check"></span> '.$tpl->timeDone.'</span>';
			}

			if(!isset($tpl->QtyPlannedBefore)){ $tpl->QtyPlannedBefore = '<span class="fa fa-calendar-check"></span>'; }
			if(!isset($tpl->QtyPlannedTitle)){ $tpl->QtyPlannedTitle = $langs->trans('QtyPlanned'); }
			if(!isset($tpl->QtyPlannedMoreClass)){ $tpl->QtyPlannedMoreClass = ''; }
			$cardDataObj->title.= ' <span class="kanban-item__time-planned '.$tpl->QtyPlannedMoreClass.'" title="'.dol_escape_htmltag($tpl->QtyPlannedTitle).'">'.$tpl->QtyPlannedBefore.' '.$tpl->timePlanned.'</span>';

			$cardDataObj->title.= '</span>';
		}


		if(!empty($tpl->status)) {
			$cardDataObj->title .= '<span class="kanban-item__status">' . $tpl->status . '</span>';
		}

		if(!empty($tpl->appendItemFooter)){ $cardDataObj->title.= $tpl->appendItemFooter; }

		$cardDataObj->title.= '</div>';



		// Afficher les contacts de la carte et/ou object attaché (user story, taches etcc)
		if(isset($tpl->userAffectedTplOverride)){
			$cardDataObj->title.= $tpl->userAffectedTplOverride;
		} elseif (!empty($TContactUsersAffected) && is_array($TContactUsersAffected)){
			include_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';

			$userImgList = '';
			$curentUserIsAffected = 0;
			foreach ($TContactUsersAffected as $contactUserAffected){

				if($contactUserAffected['id'] == $user->id){
					$curentUserIsAffected = 1;
				}

				$userAffected = new User($this->db);
				if($userAffected->fetch($contactUserAffected['id']) > 0){
					$userImgList.= '<span class="kanban-item__users_img" data-user-id="'.$contactUserAffected['id'].'" >'.self::getUserImg($userAffected, 'kanban-item__user').'</span>';
				}
			}
			$cardDataObj->title.= '<span class="kanban-item__users" data-iam-affected="'.$curentUserIsAffected.'">'.$userImgList.'</span>';
		}

		$cardDataObj->item = array();

		return $cardDataObj;
	}


	/**
	 * get this object formatted for ajax and json
	 * @return stdClass
	 */
	public function getScrumKanBanItemObjectStd(){


		$object = new stdClass();
		$object->objectId = $this->id;
		$object->type = 'adv-kanban-card';// le type dans le kanban tel que getAdvKanBanItemObjectFormatted le fait
		$object->id = 'advkanbancard-' . $this->id; // kanban dom id
		$object->label = $this->label;
		$object->element = $this->element;
		$object->cardUrl = dol_buildpath('/advancedkanban/advkanbancard_card.php',1).'?id='.$this->id;
		$object->title = '';
		$object->status = intval($this->status);
		$object->statusLabel = $this->LibStatut(intval($this->status), 1);
		$object->contactUsersAffected = $this->liste_contact(-1,'internal',1);

		/**
		 * Traitement de l'élément attaché
		 */

		$object->targetelementid = $this->fk_element;
		$object->targetelement = $this->element_type;

		$res = $this->fetchElementObject();
		if($res){
			$object->elementObject = false;

			/**
			 * OVERRIDE FROM ELEMENT
			 */
			if(is_callable(array($this->elementObject, 'getScrumKanBanItemObjectStd'))){
				$object->elementObject = $this->elementObject->getScrumKanBanItemObjectStd($this, $object);
			}

			// Si gestion de l'object sans getScrumKanBanItemObjectStd : **typiquement les objects Dolibarr**
			if(!$object->elementObject){
				$object->elementObject = new stdClass();
				$object->elementObject->contactUsersAffected = $this->elementObject->liste_contact(-1,'internal', 1);

				if($this->elementObject->element == 'project_task'){
					$object->type = 'project-task';
				}else{
					$object->type = 'adv-kanban-card-linked';
				}
			}
		}

		return $object;
	}



	/**
	 * Return HTML string to put an input field into a page
	 * Code very similar with showInputField of extra fields
	 *
	 * @param  array   		$val	       Array of properties for field to show
	 * @param  string  		$key           Key of attribute
	 * @param  string  		$value         Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
	 * @param  string  		$moreparam     To add more parameters on html input tag
	 * @param  string  		$keysuffix     Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  		$keyprefix     Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string|int	$morecss       Value for css to define style/length of field. May also be a numeric.
	 * @return string
	 */
	public function showInputField($val, $key, $value, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = 0, $nonewbutton = 0)
	{
		global $db, $hookmanager;

		// for cache
		if(empty($this->form)){
			$this->form = new Form($db);
		}

		// Add  hook
		if (!is_object($hookmanager)) {
			include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
			$hookmanager = new HookManager($db);
		}
		$hookmanager->initHooks(array('advkanbancarddao'));

		// Hook params
		$parameters = array(
			'val' =>& $val,
			'key' =>& $key,
			'value' =>& $value,
			'moreparam' =>& $moreparam,
			'keysuffix' =>& $keysuffix,
			'keyprefix' =>& $keyprefix,
			'morecss' =>& $morecss,
			'nonewbutton' =>& $nonewbutton
		);

		$reshook = $hookmanager->executeHooks('AdvKanbanCard_showInputField', $parameters, $this);
		if ($reshook > 0) {
			return $hookmanager->resPrint;
		}

		if ($key == 'fk_element')
		{
			$compatibleElementList = $this->getCompatibleElementListLabels();
			$elementTypeMoreParam = ' data-reset-target="#'.$keyprefix.$key.$keysuffix.'" data-reset-value="0" ';
			$out= $this->form->selectarray($keyprefix.'element_type'.$keysuffix, $compatibleElementList, $this->element_type, 1, 0, 0, $elementTypeMoreParam, 0, 0, 0, '', 'adv-kanban-form-toggle-trigger adv-kanban-form-reset-trigger', 1);

			$out.= '<input type="hidden" id="'.$keyprefix.$key.$keysuffix.'" name="'.$keyprefix.$key.$keysuffix.'" value="'.$this->fk_element.'" />';

			// TODO : a remplacer par une recherche ajax plus propre
			$compatibleElementList = $this->getCompatibleElementList();
			foreach ($compatibleElementList as $item => $itemvalue){

				if(empty($itemvalue['selectable'])){
					continue;
				}

				// utilisation d'un override pour plus de flexibilite : peut etre issue d'un hook de getCompatibleElementList()
				if(!empty($compatibleElementList[$item]['overrideFkElementType']))
				{
					$this->fields[$key]['type'] = $val['type'] = $compatibleElementList[$item]['overrideFkElementType']; //'integer:webpassword:webpassword/class/webpassword.class.php:1:statut=1'
				}
				else{
					$this->fields[$key]['type'] = $val['type'] = 'integer:'.$compatibleElementList[$item]['class'].':'.$compatibleElementList[$item]['classfile'].':1';
				}

				// Affichage par defaut du conteneur de formaulaire fonction de $this->element_type
				$containerStatus = 0;
				if($item==$this->element_type){
					$containerStatus = 1;
				}

				$out.= '<div id="container_'.$item.'_'.$keyprefix.$key.$keysuffix.'" class="adv-kanban-form-toggle-target" data-display="'.$containerStatus.'" data-toggle-trigger="'.$keyprefix.'element_type'.$keysuffix.'" data-toggle-trigger-value="'.$item.'" >';
				$moreparam = ' data-cloneval-target="#'.$keyprefix.$key.$keysuffix.'" ';

				if(self::isFieldPropertieIsTypeLink($this->fields[$key]['type']) && intval(DOL_VERSION) >= 19){
					// method created to avoid error see on issue https://github.com/Dolibarr/dolibarr/issues/29369
					$out.= static::customShowInputField($val, $key, $value, $moreparam, $keysuffix, $item.'_'.$keyprefix, 'adv-kanban-form-cloneval-trigger', $nonewbutton);
				}else{
					$out.= parent::showInputField($val, $key, $value, $moreparam, $keysuffix, $item.'_'.$keyprefix, 'adv-kanban-form-cloneval-trigger', $nonewbutton);
				}

				$out.= '</div>';
			}


		}
		else{
			$out = parent::showInputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $morecss);
		}

		return $out;
	}


	/**
	 * method created to avoid error see on issue https://github.com/Dolibarr/dolibarr/issues/29369
	 * check type of properties for field to show
	 * return
	 * @param $type
	 * @return bool
	 */
	public static function isFieldPropertieIsTypeLink($type){
		if (preg_match('/^(integer|link):(.*):(.*):(.*):(.*)/i', $type, $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3].':'.$reg[4].':'.$reg[5] => 'N');
			return true;
		} elseif (preg_match('/^(integer|link):(.*):(.*):(.*)/i', $type, $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3].':'.$reg[4] => 'N');
			return true;
		} elseif (preg_match('/^(integer|link):(.*):(.*)/i', $type, $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3] => 'N');
			return true;
		}
		return false;
	}


	/**
	 * method created to avoid error see on issue https://github.com/Dolibarr/dolibarr/issues/29369
	 *
	 * Return HTML string to put an input field into a page
	 * Code very similar with showInputField of extra fields
	 *
	 * @param  array|null	$val	       Array of properties for field to show (used only if ->fields not defined)
	 * @param  string  		$key           Key of attribute
	 * @param  string|array	$value         Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value, for array type must be array)
	 * @param  string  		$moreparam     To add more parameters on html input tag
	 * @param  string  		$keysuffix     Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  		$keyprefix     Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string|int	$morecss       Value for css to define style/length of field. May also be a numeric.
	 * @param  int			$nonewbutton   Force to not show the new button on field that are links to object
	 * @return string
	 */
	public function customShowInputField($val, $key, $value, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = 0, $nonewbutton = 0)
	{
		global $langs, $form;

		if (!is_object($form)) {
			require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
			$form = new Form($this->db);
		}

		if (!empty($this->fields)) {
			$val = $this->fields[$key];
		}

		// Validation tests and output
		$fieldValidationErrorMsg = $this->getFieldError($key);
		if (!empty($fieldValidationErrorMsg)) {
			$validationClass = ' --error'; // the -- is use as class state in css :  .--error can't be be defined alone it must be define with another class like .my-class.--error or input.--error
		} else {
			$validationClass = ' --success'; // the -- is use as class state in css :  .--success can't be be defined alone it must be define with another class like .my-class.--success or input.--success
		}

		$out = '';
		$isDependList = 0;
		$param = array();
		$param['options'] = array();
		$reg = array();
		$size = !empty($this->fields[$key]['size']) ? $this->fields[$key]['size'] : 0;
		// Because we work on extrafields
		if (preg_match('/^(integer|link):(.*):(.*):(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3].':'.$reg[4].':'.$reg[5] => 'N');
			$type = 'link';
		} elseif (preg_match('/^(integer|link):(.*):(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3].':'.$reg[4] => 'N');
			$type = 'link';
		} elseif (preg_match('/^(integer|link):(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[2].':'.$reg[3] => 'N');
			$type = 'link';
		} else {
			$param['options'] = array();
			$type = $this->fields[$key]['type'];
		}
		//var_dump($type); var_dump($param['options']);

		// Special case that force options and type ($type can be integer, varchar, ...)
		if (!empty($this->fields[$key]['arrayofkeyval']) && is_array($this->fields[$key]['arrayofkeyval'])) {
			$param['options'] = $this->fields[$key]['arrayofkeyval'];
			$type = (($this->fields[$key]['type']=='checkbox') ? $this->fields[$key]['type'] : 'select');
		}

		$label = $this->fields[$key]['label'];
		//$elementtype=$this->fields[$key]['elementtype'];	// Seems not used
		$default = (!empty($this->fields[$key]['default']) ? $this->fields[$key]['default'] : '');
		$computed = (!empty($this->fields[$key]['computed']) ? $this->fields[$key]['computed'] : '');
		$unique = (!empty($this->fields[$key]['unique']) ? $this->fields[$key]['unique'] : 0);
		$required = (!empty($this->fields[$key]['required']) ? $this->fields[$key]['required'] : 0);
		$autofocusoncreate = (!empty($this->fields[$key]['autofocusoncreate']) ? $this->fields[$key]['autofocusoncreate'] : 0);

		$langfile = (!empty($this->fields[$key]['langfile']) ? $this->fields[$key]['langfile'] : '');
		$list = (!empty($this->fields[$key]['list']) ? $this->fields[$key]['list'] : 0);
		$hidden = (in_array(abs($this->fields[$key]['visible']), array(0, 2)) ? 1 : 0);

		$objectid = $this->id;

		if ($computed) {
			if (!preg_match('/^search_/', $keyprefix)) {
				return '<span class="opacitymedium">'.$langs->trans("AutomaticallyCalculated").'</span>';
			} else {
				return '';
			}
		}

		// Set value of $morecss. For this, we use in priority showsize from parameters, then $val['css'] then autodefine
		if (empty($morecss) && !empty($val['css'])) {
			$morecss = $val['css'];
		} elseif (empty($morecss)) {
			if ($type == 'date') {
				$morecss = 'minwidth100imp';
			} elseif ($type == 'datetime' || $type == 'link') {	// link means an foreign key to another primary id
				$morecss = 'minwidth200imp';
			} elseif (in_array($type, array('int', 'integer', 'price')) || preg_match('/^double(\([0-9],[0-9]\)){0,1}/', $type)) {
				$morecss = 'maxwidth75';
			} elseif ($type == 'url') {
				$morecss = 'minwidth400';
			} elseif ($type == 'boolean') {
				$morecss = '';
			} else {
				if (round($size) < 12) {
					$morecss = 'minwidth100';
				} elseif (round($size) <= 48) {
					$morecss = 'minwidth200';
				} else {
					$morecss = 'minwidth400';
				}
			}
		}

		// Add validation state class
		if (!empty($validationClass)) {
			$morecss.= $validationClass;
		}

		if ($type == 'link') {
			// $param_list='ObjectName:classPath[:AddCreateButtonOrNot[:Filter[:Sortfield]]]'
			// Filter can contains some ':' inside.
			$param_list = array_keys($param['options']);
			$param_list_array = explode(':', $param_list[0], 4);

			$showempty = (($required && $default != '') ? 0 : 1);

			if (!preg_match('/search_/', $keyprefix)) {
				if (!empty($param_list_array[2])) {		// If the entry into $fields is set to add a create button
					if (!empty($this->fields[$key]['picto'])) {
						$morecss .= ' widthcentpercentminusxx';
					} else {
						$morecss .= ' widthcentpercentminusx';
					}
				} else {
					if (!empty($this->fields[$key]['picto'])) {
						$morecss .= ' widthcentpercentminusx';
					}
				}
			}

			// FIX see : https://github.com/Dolibarr/dolibarr/issues/29369
			$objectfield = null; // $this->element.($this->module ? '@'.$this->module : '').':'.$key.$keysuffix;
			$out = $form->selectForForms($param_list[0], $keyprefix.$key.$keysuffix, $value, $showempty, '', '', $morecss, $moreparam, 0, (empty($val['disabled']) ? 0 : 1), '', $objectfield);
			// FIN FIX


			if (!empty($param_list_array[2])) {		// If the entry into $fields is set, we must add a create button
				if ((!GETPOSTISSET('backtopage') || strpos(GETPOST('backtopage'), $_SERVER['PHP_SELF']) === 0)	// // To avoid to open several times the 'Plus' button (we accept only one level)
					&& empty($val['disabled']) && empty($nonewbutton)) {	// and to avoid to show the button if the field is protected by a "disabled".
					[$class, $classfile] = explode(':', $param_list[0]);
					if (file_exists(dol_buildpath(dirname(dirname($classfile)).'/card.php'))) {
						$url_path = dol_buildpath(dirname(dirname($classfile)).'/card.php', 1);
					} else {
						$url_path = dol_buildpath(dirname(dirname($classfile)).'/'.strtolower($class).'_card.php', 1);
					}
					$paramforthenewlink = '';
					$paramforthenewlink .= (GETPOSTISSET('action') ? '&action='.GETPOST('action', 'aZ09') : '');
					$paramforthenewlink .= (GETPOSTISSET('id') ? '&id='.GETPOST('id', 'int') : '');
					$paramforthenewlink .= (GETPOSTISSET('origin') ? '&origin='.GETPOST('origin', 'aZ09') : '');
					$paramforthenewlink .= (GETPOSTISSET('originid') ? '&originid='.GETPOST('originid', 'int') : '');
					$paramforthenewlink .= '&fk_'.strtolower($class).'=--IDFORBACKTOPAGE--';
					// TODO Add Javascript code to add input fields already filled into $paramforthenewlink so we won't loose them when going back to main page
					$out .= '<a class="butActionNew" title="'.$langs->trans("New").'" href="'.$url_path.'?action=create&backtopage='.urlencode($_SERVER['PHP_SELF'].($paramforthenewlink ? '?'.$paramforthenewlink : '')).'"><span class="fa fa-plus-circle valignmiddle"></span></a>';
				}
			}
		}

		if (!empty($hidden)) {
			$out = '<input type="hidden" value="'.$value.'" name="'.$keyprefix.$key.$keysuffix.'" id="'.$keyprefix.$key.$keysuffix.'"/>';
		}

		if ($isDependList == 1) {
			$out .= $this->getJSListDependancies('_common');
		}

		// Display error message for field
		if (!empty($fieldValidationErrorMsg) && function_exists('getFieldErrorIcon')) {
			$out .= ' '.getFieldErrorIcon($fieldValidationErrorMsg);
		}

		return $out;
	}

	/**
	 * Return HTML string to show a field into a page
	 * Code very similar with showOutputField of extra fields
	 *
	 * @param  array   $val		       Array of properties of field to show
	 * @param  string  $key            Key of attribute
	 * @param  string  $value          Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
	 * @param  string  $moreparam      To add more parametes on html input tag
	 * @param  string  $keysuffix      Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  $keyprefix      Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  mixed   $morecss        Value for css to define size. May also be a numeric.
	 * @return string
	 */
	public function showOutputField($val, $key, $value, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = '')
	{

		if ($key == 'fk_element')
		{
			$compatibleElementList = $this->getCompatibleElementListLabels();

			if(!empty($compatibleElementList[$this->element_type])){
				$out = $compatibleElementList[$this->element_type];

				$this->fetchElementObject();
				if(!empty($this->elementObject->id))
				{
					if(is_callable(array($this->elementObject, 'getNomUrl')))
					{
						$out =  $this->elementObject->getNomUrl(1);
					}
				}
			}
			else{
				$out = '';
			}
		}
		else{
			$out = parent::showOutputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $morecss);
		}

		return $out;
	}


	/**
	 * Return array of compatible elements
	 * Code very similar with showOutputField of extra fields
	 *
	 * @return array
	 */
	public function getCompatibleElementListLabels()
	{
		global $langs;

		$compatibleElements = $this->getCompatibleElementList();

		$list = array();

		foreach ($compatibleElements as $key => $value){
			$list[$key] = $value['label'];
		}


		// TODO : add hook here

		return $list;
	}

	/**
	 * Return array of compatible elements
	 * Code very similar with showOutputField of extra fields
	 *
	 * @return array
	 */
	public function getCompatibleElementList()
	{
		global $langs, $user, $conf;
		$error = 0;


		// Key of  compatibleElementList is object element

		$this->compatibleElementList = array();

		if (isModEnabled('societe')) {
			$this->compatibleElementList['societe'] = array(
				'selectable' => true,
				'label' => $langs->trans('Societe'),
				'class' => 'Societe',
				'classfile' => 'societe/class/societe.class.php',
			);
		}
		if (isModEnabled('propal')) {

			$langs->load("propal");
			$this->compatibleElementList['propal'] = array(
				'selectable' => true,
				'label' => $langs->trans('Proposal'),
				'class' => 'Propal',
				'classfile' => 'comm/propal/class/propal.class.php',
			);
		}

		if (isModEnabled('commande')) {
			$langs->load("orders");
			$this->compatibleElementList['commande'] = array(
				'selectable' => true,
				'label' => $langs->trans('Order'),
				'class' => 'Commande',
				'classfile' => 'commande/class/commande.class.php',
			);
		}

		if (isModEnabled('invoice')) {
			$langs->load("bills");
			$this->compatibleElementList['facture'] = array(
				'selectable' => true,
				'label' => $langs->trans('Bill'),
				'class' => 'Facture',
				'classfile' => 'compta/facture/class/facture.class.php',
			);
		}


		if (isModEnabled('resource')) {

			$this->compatibleElementList['dolresource'] = array(
				'selectable' => true,
				'label' => $langs->trans('Ressource'),
				'class' => 'Dolresource',
				'classfile' => 'resource/class/dolresource.class.php',
				'overrideFkElementType' => 'integer:Dolresource:resource/class/dolresource.class.php:1',
			);
		}


		if (isModEnabled('projet')) {
			$this->compatibleElementList['task'] = array(
				'selectable' => true,
				'label' => $langs->trans('Task'),
				'class' => 'Task',
				'classfile' => 'projet/class/task.class.php',
				'overrideFkElementType' => 'integer:Task:projet/class/task.class.php:1',
			);
		}

		if (isModEnabled('mrp')) {
			$this->compatibleElementList['mo'] = array(
				'selectable' => true,
				'label' => $langs->trans('Mo'),
				'class' => 'Mo',
				'classfile' => 'mrp/class/mo.class.php',
				'overrideFkElementType' => 'integer:Mo:mrp/class/mo.class.php:1',
			);
		}

		if (isModEnabled('ticket')) {
			$this->compatibleElementList['ticket'] = array(
				'selectable' => true,
				'label' => $langs->trans('Ticket'),
				'class' => 'Ticket',
				'classfile' => 'ticket/class/ticket.class.php',
				'overrideFkElementType' => 'integer:Ticket:ticket/class/ticket.class.php:1',
			);
		}

		if (isModEnabled('projet')) {
			$this->compatibleElementList['project'] = array(
				'selectable' => true,
				'label' => $langs->trans('Project'),
				'class' => 'Project',
				'classfile' => 'projet/class/project.class.php',
				'overrideFkElementType' => 'integer:Project:projet/class/project.class.php:1',
			);
		}

		// Call triggers for the "security events" log
		include_once DOL_DOCUMENT_ROOT.'/core/class/interfaces.class.php';
		$interface = new Interfaces($this->db);
		$result = $interface->run_triggers('ADVANCEDKANBAN_GET_COMPATIBLE_ELEMENT_LIST', $this, $user, $langs, $conf);
		if ($result < 0) {
			$error++;
		}
		// End call triggers

		return $this->compatibleElementList;
	}

	/**
	 * test if curent object is compatible with webpassword
	 *
	 * @param CommonObject $object
	 * @return string|false
	 */
	public function isCompatibleElement($object)
	{
		if(!is_object($object)){
			return false;
		}

		if(empty($this->compatibleElementList)){
			$this->getCompatibleElementList();
		}

		foreach ($this->compatibleElementList as $key => $values){
			if($object->element === $key){
				return $key;
			}
		}

		return false;
	}


	/**
	 * Return the number of password stored for the element
	 *
	 * @param string $element
	 * @param  int  $fk_element
	 * @return string|false
	 */
	public function countElementItems($element, $fk_element)
	{
		if(empty($element) || empty($fk_element)){
			return false;
		}

		$sql = 'SELECT COUNT(*) nb FROM '.MAIN_DB_PREFIX.$this->table_element.' t ';
		$sql.= ' WHERE t.fk_element = '.intval($fk_element);
		$sql.= ' AND t.element_type = \''.$this->db->escape($element).'\'';


		$res = $this->db->query($sql);
		if ($res)
		{
			$obj = $this->db->fetch_object($res);
			return $obj->nb;
		}

		return false;
	}


	/**
	 *	Get element object and children from database
	 *	@param      bool	$force       force fetching new
	 *	@return     int         				>0 if OK, <0 if KO, 0 if not found
	 */
	public function fetchElementObject($force = false)
	{
		if(empty($force) && is_object($this->elementObject) && $this->elementObject->id > 0){
			// use cache
			return 1;
		}

		if(!class_exists('AdvKanbanTools')){
			require_once __DIR__ . '/advKanbanTools.class.php';
		}

		$this->elementObject = AdvKanbanTools::getObjectByElement($this->element_type, $this->fk_element);

		if($this->elementObject !== false){
			return 1;
		}

		$this->elementObject = false;
		return 0;
	}

	/**
	 * @param string $code
	 * @param CommonObject $object
	 * @return void
	 */
	public static function getInternalContactIdFromCode($code, $object, &$error = ''){
		global $db;
		$sql = "SELECT rowid id FROM ".MAIN_DB_PREFIX."c_type_contact WHERE active=1 AND element='".$db->escape($object->element)."' AND source='internal' AND code = '".$db->escape($code)."' ";
		$obj = $db->getRow($sql);
		if(!empty($obj)){
			return $obj->id;
		}elseif($obj!==false){
			$error = $sql;
			return 0;
		}
		else{
			$error = $db->error();
			return false;
		}
	}

	/**
	 * Permet de spliter la carte en 2
	 * @param double $qty la quantité de la nouvelle carte
	 * @param string $newCardLabel le libelle de la nouvelle carte
	 * @return bool
	 */
	public function splitCard($qty, $newCardLabel, User $user){
		$this->fetchElementObject();
		if(is_callable(array($this->elementObject, 'splitCard'))){
			if($this->elementObject->splitCard( $qty, $newCardLabel, $this, $user)){
				return true;
			}else{
				$this->error = $this->elementObject->error;
				$this->errors = $this->elementObject->errors;
				return false;
			}
		}else{
			$this->error = 'splitCard not supported';
			return false;
		}
	}

	/**
	 * @param int 		$fk_list
	 * @param string 	$element_type
	 * @param int 		$fk_element
	 * @return int <if KO >0 if OK
	 */
	public function getCardRankByElement($fk_list, $element_type, $fk_element) {
		$sql = 'SELECT fk_rank FROM '.MAIN_DB_PREFIX.'advancedkanban_advkanbancard WHERE fk_advkanbanlist="'.intval($fk_list).'" AND element_type="'.$this->db->escape($element_type).'" AND fk_element="'.intval($fk_element).'"';
		$rank = $this->db->getRow($sql);
		if(!empty($rank)) return $rank->fk_rank;
		else return -1;
	}

	/**
	 * @param int $fk_rank
	 * @return bool
	 */
	public function shiftAllCardRankAfterRank($fk_rank = false) {

		if($fk_rank===false) $fk_rank = $this->fk_rank;

		$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' SET tms=NOW(), fk_rank = (fk_rank +1)
		 		WHERE fk_advkanbanlist ='.intval($this->fk_advkanbanlist).' AND fk_rank >= '.intval($fk_rank).' AND rowid != '.$this->id.';';
		$resUp = $this->db->query($sql);
		if(! $resUp) {
			$this->error = $this->db->lasterror();
			return false;
		}

		return true;
	}

	/**
	 * Ajoute les categories/tags
	 *
	 * @param int[] $categories id des categories
	 * @return float|int
	 */
	public function setCategories($categories)
	{
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
		return parent::setCategoriesCommon($categories, 'advkanbancard');
	}


	/**
	 * @param int|bool     $userId
	 * @param bool         $toggle if contact already prevent it remove it
	 * @return bool|void
	 */
	public function assignUserToCard($user, $userId = false, $toggle = false){
		global  $conf, $langs;

		if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
			$this->error = $langs->trans('NotEnoughRights');
			return false;
		}

		if($userId === false){
			$userId = $user->id;
			$user->fetch_optionals();
			if(!empty($user->array_options['options_advancedkanban_role'])) $typeContact = $user->array_options['options_advancedkanban_role'];
		}elseif(empty($userId)){
			$this->error = 'Need user Id';
			return false;
		}else{
			$contactUser = new User($this->db);
			if($contactUser->fetch($userId) <= 0){
				$this->error = 'Need valid user';
				return false;
			}

			$contactUser->fetch_optionals();
			$typeContact = $contactUser->array_options['options_advancedkanban_role'];
		}

		if(empty($typeContact) && getDolGlobalString('ADVKANBAN_DEFAULT_KANBAN_CONTACT_CODE')){
			$typeContact = getDolGlobalString('ADVKANBAN_DEFAULT_KANBAN_CONTACT_CODE');
		}


		// Get card id
		if(empty($this->id)){
			$this->error = 'Need card Id';
			return false;
		}

		if($this->fk_element > 0 && !$this->fetchElementObject()) {
			$this->error = 'Error fectching element object';
			return false;
		}

		if(!empty($this->element->kanbanDefaultCardTypeContactCode)){
			$typeContact = $this->element->kanbanDefaultCardTypeContactCode;
		}

		if(empty($typeContact)){
			$this->error = 'Type contact not defined for '.$this->elementObject->element.'. Please contact your administrator';
			return false;
		}

		if($this->fk_element > 0 && defined( get_class($this->elementObject).'::OVERRIDE_KANBAN_CARD_CONTACTS' )){

			$typeContactId = $this::getInternalContactIdFromCode($typeContact, $this->elementObject);
			if(!$typeContactId){
				$this->error = 'Error contact type '.$typeContact.' not found for '.$this->elementObject->element;
				return false;
			}

			$result = $this->elementObject->add_contact($userId, $typeContactId,'internal');
			if($result<0){
				$this->error = 'Error adding contact : '.$this->elementObject->errorsToString();
				return false;
			}

			if($toggle && $result == 0){
				return $this->removeUserToCard($user, $userId);
			}
		}
		else{
			$typeContactId = $this::getInternalContactIdFromCode($typeContact, $this);
			if(!$typeContactId){
				$this->error = 'Error contact type '.$typeContact.' not found for scrum card';
				return false;
			}

			$result = $this->add_contact($userId, $typeContactId,'internal');
			if($result<0){
				$this->error = 'Error adding contact : '.$this->errorsToString();
				return false;
			}

			if($toggle && $result == 0){
				return $this->removeUserToCard($user, $userId);
			}
		}

		return true;
	}



	/**
	 * @param int|bool         $userId
	 * @return bool|void
	 */
	function removeUserToCard($user, $userId = false){
		global $langs;

		if (!$user->hasRight('advancedkanban', 'advkanbancard', 'write')) {
			$this->error = $langs->trans('NotEnoughRights');
			return false;
		}

		if($userId === false){
			$userId = $user->id;
		}elseif(empty($userId)){
			$this->error = 'Need user Id';
			return false;
		}else{
			$contactUser = new User($this->db);
			if($contactUser->fetch($userId) <= 0){
				$this->error = 'Need valid user';
				return false;
			}
		}

		// Get card id
		if(empty($this->id)){
			$this->error = 'Need card Id';
			return false;
		}

		if($this->fk_element > 0 && !$this->fetchElementObject()) {
			$this->error = 'Error fectching element object';
			return false;
		}

		if($this->fk_element > 0 && defined( get_class($this->elementObject).'::OVERRIDE_KANBAN_CARD_CONTACTS' )){

			$TContactUsersAffected = $this->elementObject->liste_contact(-1,'internal');
			if($TContactUsersAffected == -1 || !is_array($TContactUsersAffected)){
				$this->error = 'Error removing contact : '.$this->elementObject->errorsToString();
				return false;
			}

			foreach ($TContactUsersAffected as $contactArray){
				if($contactArray['id'] != $userId){
					continue;
				}

				$result = $this->elementObject->delete_contact($contactArray['rowid']);
				if($result<0){
					$this->error = 'Error delecting contact : '.$this->errorsToString();
					return false;
				}
			}
		}
		else{
			$TContactUsersAffected = $this->liste_contact(-1,'internal');
			if($TContactUsersAffected == -1 || !is_array($TContactUsersAffected)){
				$this->error = 'Error removing contact : '.$this->errorsToString();
				return false;
			}

			foreach ($TContactUsersAffected as $contactArray){

				if($contactArray['id'] != $userId){
					continue;
				}

				$result = $this->delete_contact($contactArray['rowid']);
				if($result<0){
					$this->error = 'Error deleting contact : '.$this->errorsToString();
					return false;
				}
			}
		}

		return true;
	}
}
