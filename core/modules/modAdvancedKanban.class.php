<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020	Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2020		Maxime Kohlhaas			<maxime@atm-consulting.fr>
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
 * 	\defgroup   advancedkanban     Module AdvancedKanban
 *  \brief      AdvancedKanban module descriptor.
 *
 *  \file       htdocs/advancedkanban/core/modules/modAdvancedKanban.class.php
 *  \ingroup    advancedkanban
 *  \brief      Description and activation file for module AdvancedKanban
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module AdvancedKanban
 */
class modAdvancedKanban extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 104891;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'advancedkanban';
		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "projects";
		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';
		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleAdvancedKanbanName' not found (AdvancedKanban is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description, used if translation string 'ModuleAdvancedKanbanDesc' not found (AdvancedKanban is name of module).
		$this->description = "AdvancedKanbanDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "AdvancedKanban description (Long)";
		$this->editor_name = 'ATM Consulting';
		$this->editor_url = 'www.atm-consulting.fr';
		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'

		$this->version = '1.9.3';

		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where ADVANCEDKANBAN is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto = 'module.svg@advancedkanban';
		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 1,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 1,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 1,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/advancedkanban/js/advancedkanban.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				'data' => array(
					'projecttaskcard',
					'category',
					'emailtemplates',
					'thirdpartylist',
					'propallist',
					'orderlist',
					'invoicelist',
                    'molist',
					'globalcard',
					'ticketlist',
					'projectlist'
				),
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			'contactelement' => array(
				'advancedkanban_advkanbancard' => "advancedkanban_advkanbancard",
				'advancedkanban_advkanban' => "advancedkanban_advkanban"
			)
		);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/advancedkanban/temp","/advancedkanban/subdir");
		$this->dirs = array("/advancedkanban/temp");
		// Config pages. Put here list of php page, stored into advancedkanban/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@advancedkanban");
		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->langfiles = array("advancedkanban@advancedkanban");
		$this->phpmin = array(7, 0); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(16, 0); // Minimum version of Dolibarr required by module
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		//$this->automatic_activation = array('FR'=>'AdvancedKanbanWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled


		// Url to the file with your last numberversion of this module
		require_once __DIR__ . '/../../class/techatm.class.php';
		$this->url_last_version = \advancedkanban\TechATM::getLastModuleVersionUrl($this);

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('ADVANCEDKANBAN_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('ADVANCEDKANBAN_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->advancedkanban) || ! isModEnabled('advancedkanban')) {
			$conf->advancedkanban = new stdClass();
			$conf->advancedkanban->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();
		$this->tabs[] = array();  					// To add a new tab identified by code tabname1



		// Boxes/Widgets
		// Add here list of php file(s) stored in advancedkanban/core/boxes that contains a class to show a widget.
		$this->boxes = array();

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array();

		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->advancedkanban->enabled', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->advancedkanban->enabled', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions





		/**
		 * DROIT POUR KANBAN
		 */

		$this->rights[$r][0] = $this->numero . $r; // Permission id (must not be already used)
		$this->rights[$r][1] = 'ReadAdvKanban'; // Permission label
		$this->rights[$r][4] = 'advkanban'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$r++;
		$this->rights[$r][0] = $this->numero . $r; // Permission id (must not be already used)
		$this->rights[$r][1] = 'WriteAdvKanban'; // Permission label
		$this->rights[$r][4] = 'advkanban'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$r++;
		$this->rights[$r][0] = $this->numero . $r; // Permission id (must not be already used)
		$this->rights[$r][1] = 'DeleteAdvKanban'; // Permission label
		$this->rights[$r][4] = 'advkanban'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$r++;


		/**
		 * DROIT POUR SCRUM CARDS
		 * For card read right see advkanban->read because kanban is used to display cards so if user can see kanban he must see card
		 */
		$this->rights[$r][0] = $this->numero . $r; // Permission id (must not be already used)
		$this->rights[$r][1] = 'WriteAdvKanbanCard'; // Permission label
		$this->rights[$r][4] = 'advkanbancard'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$r++;
		$this->rights[$r][0] = $this->numero . $r; // Permission id (must not be already used)
		$this->rights[$r][1] = 'DeleteAdvKanbanCard'; // Permission label
		$this->rights[$r][4] = 'advkanbancard'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->advancedkanban->level1->level2)
		$r++;


		// Main menu entries to add
		$this->menu = array();
		$r = 0;


		/**
		 * MENU SCRUM KANBAN
		 */
		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'AdvKanban',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanban',
			'url'=>'/advancedkanban/advkanban_list.php?mainmenu=tools&leftmenu=advkanban',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban")',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'read\')',
			'prefix' => '<span class="fa fa-align-right icon-kanban em092 pictofixedwidth scrum-project-left-menu-picto" style="color: #00384e;"></span>',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);
		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanban',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'NewAdvKanban',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbannew',
			'url'=>'/advancedkanban/advkanban_card.php?action=create',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban")',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'write\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0
		);

		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanban',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'List',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbanlist',
			'url'=>'/advancedkanban/advkanban_list.php?mainmenu=tools&leftmenu=advkanban',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban")',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'read\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);
		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanbanlist',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'StatusAdvKanbanDraft',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbanlist0',
			'url'=>'/advancedkanban/advkanban_list.php?mainmenu=tools&leftmenu=advkanban&search_status=0',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban") && $leftmenu==\'advkanban\'',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'read\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);
		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanbanlist',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'StatusAdvKanbanReady',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbanlist1',
			'url'=>'/advancedkanban/advkanban_list.php?mainmenu=tools&leftmenu=advkanban&search_status=1',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban") && $leftmenu==\'advkanban\'',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'read\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);
		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanbanlist',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'StatusAdvKanbanDone',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbanlist2',
			'url'=>'/advancedkanban/advkanban_list.php?mainmenu=tools&leftmenu=advkanban&search_status=2',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->advancedkanban->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban") && $leftmenu==\'advkanban\'',
			// Use 'perms'=>'$user->rights->advancedkanban->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight(\'advancedkanban\', \'advkanban\',\'read\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);

		$this->menu[$r++]=array(
			// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=advkanban',
			// This is a Left menu entry
			'type'=>'left',
			'titre'=>'Tags/catégories',
			'mainmenu'=>'tools',
			'leftmenu'=>'advkanbancat',
			'url'=>'/categories/index.php?type=advkanbancard',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs'=>'advancedkanban@advancedkanban',
			'position'=>1100+$r,
			// Define condition to show or hide menu entry. Use '$conf->scrumproject->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled'=>'isModEnabled("advancedkanban")',
			// Use 'perms'=>'$user->rights->scrumproject->level1->level2' if you want your menu with a permission rules
			'perms'=>'$user->hasRight( \'categorie\',\'lire\')',
			'target'=>'',
			// 0=Menu for internal users, 1=external users, 2=both
			'user'=>0,
		);

	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		if(intval(DOL_VERSION) < 20){ // TODO : change version according to PR validation see https://github.com/Dolibarr/dolibarr/pull/21674
			$this->db->query('ALTER TABLE '.MAIN_DB_PREFIX.'element_element MODIFY COLUMN sourcetype VARCHAR(64) NOT NULL;');
			$this->db->query('ALTER TABLE '.MAIN_DB_PREFIX.'element_element MODIFY COLUMN targettype VARCHAR(64) NOT NULL;');
			$this->db->query('ALTER TABLE '.MAIN_DB_PREFIX.'c_type_contact MODIFY COLUMN element VARCHAR(64) NOT NULL;');
//			$this->db->query('ALTER TABLE llx_c_email_templates MODIFY COLUMN type_template VARCHAR(64) NOT NULL;');
		}

		// Ne pas permettre l'installation du module si le script ne migration de scrum project n'a pas été lancé
		foreach (['scrumproject_scrumcard', 'scrumproject_scrumkanban', 'scrumproject_scrumkanbanlist', 'categorie_scrumcard'] as $tableToCheck){
			$testTable = $this->db->query('SHOW TABLES LIKE \''.$this->db->prefix().$this->db->escape($tableToCheck).'\' ');
			if ($testTable) {
				$objTestTable = $this->db->fetch_object($testTable);
				if ($objTestTable) {
					$this->error = '<strong>SCRUM PROJECT IS INSTALLED :</strong><br/>YOU NEED TO RUN MIGRATION SCRIPT BEFORE ACTIVATE ADVANCE KANBAN';
					return 0;
				}
			}
		}


		$result = $this->_load_tables('/advancedkanban/sql/');
		if ($result < 0) return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')


		// Create extrafields during init
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$extrafields->addExtraField('advancedkanban_role', "AdvancedKanbanUserRole", 'sellist', 1010, '32', 'user',      0, 0, '', array('options' => array("c_type_contact:libelle:code::active=1 AND element='advancedkanban_advkanbancard' AND source='internal'" => null)), 1, '', 1, 'AdvancedKanbanUserRoleHelp', '', '', 'advancedkanban@advancedkanban', 'isModEnabled("advancedkanban")');

		// Permissions
		$this->remove($options);



		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
