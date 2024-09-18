<?php
/* Copyright (C) 2024 EVARISK <technique@evarisk.com>
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
 * \defgroup digikanban Module DigiKanban
 * \brief    DigiKanban module descriptor
 *
 * \file     core/modules/modDigiKanban.class.php
 * \ingroup  digikanban
 * \brief    Description and activation file for module DigiKanban
 */

// Load Dolibarr libraries
require_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

/**
 * Description and activation class for module DigiKanban
 */
class modDigiKanban extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    function __construct($db)
    {
        global $conf, $langs;
        $this->db = $db;

        if (file_exists(__DIR__ . '/../../../saturne/lib/saturne_functions.lib.php')) {
            require_once __DIR__ . '/../../../saturne/lib/saturne_functions.lib.php';
            saturne_load_langs(['digikanban@digikanban']);
        } else {
            $this->error++;
            $this->errors[] = $langs->trans('activateModuleDependNotSatisfied', 'DigiKanban', 'Saturne');
        }

        // ID for module (must be unique)
        $this->numero = 436313;

        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'digikanban';

        // Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
        // It is used to group modules by family in module setup page
        $this->family = '';

        // Module position in the family on 2 digits ('01', '10', '20', ...)
        $this->module_position = '';

        // Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
        $this->familyinfo = ['Evarisk' => ['position' => '01', 'label' => 'Evarisk']];
        // Module label (no space allowed), used if translation string 'ModuleDigiKanbanName' not found (DigiKanban is name of module)
        $this->name = preg_replace('/^mod/i', '', get_class($this));

        // Module description, used if translation string 'ModuleDigiKanbanDesc' not found (DigiKanban is name of module)
        $this->description = $langs->trans('DigiKanbanDescription');
        // Used only if file README.md and README-LL.md not found
        $this->descriptionlong = $langs->trans('DigiKanbanDescriptionLong');

        // Author
        $this->editor_name = 'Evarisk';
        $this->editor_url  = 'https://evarisk.com';

        // Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
        $this->version = '1.0.0';

        // Url to the file with your last numberversion of this module
        //$this->url_last_version = 'http://www.example.com/versionmodule.txt';

        // Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);

        // Name of image file used for this module
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
        // To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
        $this->picto = 'digikanban_color@digikanban';

        // Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
        $this->module_parts = [
            // Set this to 1 if module has its own trigger directory (core/triggers)
            'triggers' => 0,
            // Set this to 1 if module has its own login method file (core/login)
            'login' => 0,
            // Set this to 1 if module has its own substitution function file (core/substitutions)
            'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory (core/menus)
            'menus' => 0,
            // Set this to 1 if module overwrite template dir (core/tpl)
            'tpl' => 0,
            // Set this to 1 if module has its own barcode directory (core/modules/barcode)
            'barcode' => 0,
            // Set this to 1 if module has its own models' directory (core/modules/xxx)
            'models' => 0,
            // Set this to 1 if module has its own printing directory (core/modules/printing)
            'printing' => 0,
            // Set this to 1 if module has its own theme directory (theme)
            'theme' => 0,
            // Set this to relative path of css file if module has its own css file
            'css' => [],
            // Set this to relative path of js file if module must load a js on all pages
            'js' => [],
            // Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
            'hooks' => [
                'projecttaskcard',
                'projecttaskscard'
            ],
            // Set this to 1 if features of module are opened to external users
            'moduleforexternal' => 0
        ];

        // Data directories to create when module is enabled
        // Example: this->dirs = array("/digikanban/temp","/digikanban/subdir");
        $this->dirs = ['/digikanban/temp'];

        // Config pages. Put here list of php page, stored into digikanban/admin directory, to use to set up module
        $this->config_page_url = ['setup.php@digikanban'];

        // Dependencies
        // A condition to hide module
        $this->hidden = false;

        // List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
        $this->depends      = ['modProjet', 'modSaturne'];
        $this->requiredby   = []; // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
        $this->conflictwith = []; // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

        // The language file dedicated to your module
        $this->langfiles = ['digikanban@digikanban'];

        // Prerequisites
        $this->phpmin                = [7, 4];  // Minimum version of PHP required by module
        $this->need_dolibarr_version = [16, 0]; // Minimum version of Dolibarr required by module

        // Messages at activation
        $this->warnings_activation     = []; // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
        $this->warnings_activation_ext = []; // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
        //$this->automatic_activation = array('FR'=>'DigiKanbanWasAutomaticallyActivatedBecauseOfYourCountryChoice');
        //$this->always_enabled = true; // If true, can't be disabled

        // Constants
        // List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example: $this->const=array(1 => array('DIGIKANBAN_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
        //                             2 => array('DIGIKANBAN_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
        // );
        $i = 0;
        $this->const = [
            // CONST MODULE
            $i++ => ['DIGIKANBAN_VERSION', 'chaine', $this->version, '', 0, 'current'],
            $i++ => ['DIGIKANBAN_DB_VERSION', 'chaine', $this->version, '', 0, 'current'],
            $i   => ['DIGIKANBAN_SHOW_PATCH_NOTE', 'integer', 1, '', 0, 'current']
        ];

        // Some keys to add into the overwriting translation tables
        /*$this->overwrite_translation = array(
            'en_US:ParentCompany'=>'Parent company or reseller',
            'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
        )*/

        if (!isset($conf->digikanban) || !isset($conf->digikanban->enabled)) {
            $conf->digikanban = new stdClass();
            $conf->digikanban->enabled = 0;
        }

        // Array to add new pages in new tabs
        $this->tabs = [];

        // Dictionaries
        if (! isset($conf->digikanban->enabled))
        {
            $conf->digikanban=new stdClass();
            $conf->digikanban->enabled=0;
        }

        // Dictionaries
        $this->dictionaries = [
            'langs' => 'digikanban@digikanban',
            // List of tables we want to see into dictionary editor
            'tabname' => [
                MAIN_DB_PREFIX . 'c_tasks_columns',
            ],
            // Label of tables
            'tablib' => [
                'TasksColumns',
            ],
            // Request to select fields
            'tabsql' => [
                'SELECT t.rowid as rowid, t.ref, t.label, t.lowerpercent, t.upperpercent, t.position, t.active FROM ' . MAIN_DB_PREFIX . 'c_tasks_columns as t',
            ],
            // Sort order
            'tabsqlsort' => [
                'position ASC'
            ],
            // List of fields (result of select to show dictionary)
            'tabfield' => [
                'ref,label,lowerpercent,upperpercent,position'
            ],
            // List of fields (list of fields to edit a record)
            'tabfieldvalue' => [
                'ref,label,lowerpercent,upperpercent,position'
            ],
            // List of fields (list of fields for insert)
            'tabfieldinsert' => [
                'ref,label,lowerpercent,upperpercent,position'
            ],
            // Name of columns with primary key (try to always name it 'rowid')
            'tabrowid' => [
                'rowid'
            ],
            // Condition to show each dictionary
            'tabcond' => [
                $conf->digikanban->enabled
            ]
        ];

        // Boxes/Widgets
        // Add here list of php file(s) stored in digikanban/core/boxes that contains a class to show a widget
        $this->boxes = [];

        // Cronjobs (List of cron jobs entries to add when module is enabled)
        // unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
        $this->cronjobs = [];

        // Permissions provided by this module
        $this->rights = [];
        $r = 0;

        /* ADMINPAGE PANEL ACCESS PERMISSIONS */
        $this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1);
        $this->rights[$r][1] = $langs->transnoentities('ReadAdminPage', 'DigiKanban');
        $this->rights[$r][4] = 'adminpage';
        $this->rights[$r][5] = 'read';

		/* KANBAN PERMISSSIONS */
		$this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = $langs->transnoentities('ReadObjects',$langs->transnoentities('KanbanMin')); // Permission label
		$this->rights[$r][4] = 'kanban'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = $langs->transnoentities('CreateObjects', $langs->transnoentities('KanbanMin')); // Permission label
		$this->rights[$r][4] = 'kanban'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = $langs->transnoentities('DeleteObjects', $langs->transnoentities('KanbanMin')); // Permission label
		$this->rights[$r][4] = 'kanban'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->digiquali->level1->level2)
		$r++;

        // Main menu entries to add
        $this->menu = [];
        $r = 0;

        // Add here entries to declare new menus
        // DIGIKANBAN MENU
        $this->menu[$r++] = [
            'fk_menu'  => 'fk_mainmenu=project',
            'type'     => 'left',
            'titre'    => 'digikanban',
            'prefix'   => '<span class="paddingrightonly fa fa-th-list"></span>',
            'leftmenu' => 'digikanban',
            'url'      => '/digikanban/index.php',
            'langs'    => 'digikanban@digikanban',
            'position' => 1000 + $r,
            'enabled'  => 1,
            'perms'    => '$user->rights->projet->lire',
            'target'   => '',
            'user'     => 0
        ];

        $this->menu[$r++] = [
            'fk_menu'  => 'fk_mainmenu=project,fk_leftmenu=digikanban',
            'type'     => 'left',
            'titre'    => $langs->trans('ModuleConfig'),
            'prefix'   => '<i class="fas fa-cog pictofixedwidth"></i>',
            'leftmenu' => 'digikanbanconfig',
            'url'      => '/digikanban/admin/setup.php',
            'langs'    => 'digikanban@digikanban',
            'position' => 1000 + $r,
            'enabled'  => 1,
            'perms'    => '$user->rights->digikanban->adminpage->read',
            'target'   => '',
            'user'     => 0
        ];

		$this->menu[$r++] = [
			'fk_menu'  => 'fk_mainmenu=digikanban',
			'type'     => 'top',
			'titre'    => $langs->trans('DigiKanban'),
			'prefix'   => '<i class="fas fa-cog pictofixedwidth"></i>',
			'leftmenu' => 'digikanban',
			'url'      => '/digikanban/digikanbanindex.php&mainmenu=digikanban',
			'langs'    => 'digikanban@digikanban',
			'position' => 1000 + $r,
			'enabled'  => 1,
			'perms'    => '$user->rights->digikanban->adminpage->read',
			'target'   => '',-
			'user'     => 0
		];

		$this->menu[$r++] = [
			'fk_menu'  => 'fk_mainmenu=digikanban',
			'type'     => 'left',
			'titre'   => $langs->trans('NewDigiKanban'),
			'prefix'   => '<i class="fas fa-plus pictofixedwidth"></i>',
			'leftmenu' => 'digikanban',
			'url'      => '/digikanban/view/kanban_card.php?action=create',
			'langs'    => 'digikanban@digikanban',
			'position' => 1000 + $r,
			'enabled'  => 1,
			'perms'    => '$user->rights->digikanban->kanban->read',
			'target'   => '',
			'user'     => 0
		];
    }

    /**
     * Function called when module is enabled
     * The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database
     * It also creates data directories
     *
     * @param  string     $options Options when enabling module ('', 'noboxes')
     * @return int                 1 if OK, 0 if KO
     * @throws Exception
     */
    public function init($options = ''): int
    {
        global $conf;

        // Permissions
        $this->remove($options);

        $sql = [];

        $result = $this->_load_tables('/digikanban/sql/');
        if ($result < 0) {
            return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
        }

        dolibarr_set_const($this->db, 'DIGIKANBAN_VERSION', $this->version, 'chaine', 0, '', $conf->entity);
        dolibarr_set_const($this->db, 'DIGIKANBAN_DB_VERSION', $this->version, 'chaine', 0, '', $conf->entity);

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled
     * Remove from database constants, boxes and permissions from Dolibarr database
     * Data directories are not deleted
     *
     * @param  string $options Options when enabling module ('', 'noboxes')
     * @return int             1 if OK, 0 if KO
     */
    public function remove($options = ''): int
    {
        $sql = [];
        return $this->_remove($sql, $options);
    }
}
