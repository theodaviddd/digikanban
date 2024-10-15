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
 * or see https://www.gnu.org/
 */


/**
 * \file    core/triggers/interface_99_modDigiKanban_DigiKanbanTriggers.class.php
 * \ingroup digikanban
 * \brief   DigiKanban trigger.
 */

// Load Dolibarr libraries.
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/triggers/dolibarrtriggers.class.php';

// Load DigiKanban libraries
require_once __DIR__ . '/../../lib/digikanban_kanban.lib.php';

/**
 *  Class of triggers for DigiKanban module
 */
class InterfaceDigiKanbanTriggers extends DolibarrTriggers
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		parent::__construct($db);

		$this->name        = preg_replace('/^Interface/i', '', get_class($this));
		$this->family      = 'demo';
		$this->description = 'DigiKanban triggers.';
		$this->version     = '1.13.0';
		$this->picto       = 'digikanban@digikanban';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName(): string
	{
		return parent::getName();
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc(): string
	{
		return parent::getDesc();
	}

	/**
	 * Function called when a Dolibarr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param  string       $action Event action code
	 * @param  CommonObject $object Object
	 * @param  User         $user   Object user
	 * @param  Translate    $langs  Object langs
	 * @param  Conf         $conf   Object conf
	 * @return int                  0 < if KO, 0 if no triggered ran, >0 if OK
	 * @throws Exception
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf): int
	{
		if (!isModEnabled('digikanban')) {
			return 0; // If module is not enabled, we do nothing
		}

		// Data and type of action are stored into $object and $action
		dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . '. id=' . $object->id);

		require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
		$now = dol_now();
		$actioncomm = new ActionComm($this->db);

		$actioncomm->elementtype  = $object->element . '@digikanban';
		$actioncomm->type_code    = 'AC_OTH_AUTO';
		$actioncomm->datep        = $now;
		$actioncomm->fk_element   = $object->id;
		$actioncomm->userownerid  = $user->id;
		$actioncomm->percentage   = -1;

        if ($conf->global->DIGIQUALI_ADVANCED_TRIGGER && !empty($object->fields)) {
			$actioncomm->note_private = method_exists($object, 'getTriggerDescription') ? $object->getTriggerDescription($object) : '';
        }

		switch ($action) {
			case 'KANBAN_CREATE' :

				// Load Digiquali libraries
				$elementArray = [];
				if ($object->context != 'createfromclone') {
					$elementArray = get_kanban_linkable_objects();
					if (!empty($elementArray)) {
						foreach ($elementArray as $linkableElementType => $linkableElement) {
							if (GETPOST('object_type') == $linkableElement['post_name']) {
								$linkedObjectType = $linkableElement['link_name'];
							}
						}
					}
				}
				$category     = new Categorie($this->db);

				$category->label       = $object->ref;
				$category->description = '';
				$category->visible     = 1;
				$category->type        = $linkedObjectType;
				$result                = $category->create($user);

				// create to do / doing / done categories with this parent
				$categories = [
					['label' => 'To Do', 'description' => $langs->trans('ToDo'), 'type' => $linkedObjectType, 'parent' => $result],
					['label' => 'Doing', 'description' => $langs->trans('Doing'), 'type' => $linkedObjectType, 'parent' => $result],
					['label' => 'Done', 'description' => $langs->trans('Done'), 'type' => $linkedObjectType, 'parent' => $result],
				];

				foreach ($categories as $category) {
					$cat = new Categorie($this->db);
					$cat->label       = $category['label'];
					$cat->description = $category['description'];
					$cat->visible     = 1;
					$cat->type        = $category['type'];
					$cat->fk_parent      = $category['parent'];
					$cat->create($user);
				}

				$actioncomm->code = 'AC_' . strtoupper($object->element) . '_CREATE';
				$actioncomm->label = $langs->transnoentities('ObjectCreateTrigger', $langs->transnoentities(ucfirst($object->element)), $object->ref);
				$actioncomm->create($user);
				break;
		}
		return 0;
	}
}
