<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 John Botella <john.botella@atm-consulting.fr>
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
 *   	\file       advkanban_card.php
 *		\ingroup    advancedkanban
 *		\brief      Page to create/edit/view advkanban
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token).
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
//if (! defined('NOSESSION'))     		     define('NOSESSION', '1');				    // Disable session

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once __DIR__ . '/class/advkanban.class.php';
require_once __DIR__ . '/lib/advancedkanban_advkanban.lib.php';

// Load translation files required by the page
$langs->loadLangs(array("advancedkanban@advancedkanban","advancedkanban@advancedkanban", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$lineid   = GETPOST('lineid', 'int');

$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'advkanbancard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$dol_openinpopup = GETPOST('dol_openinpopup', 'aZ09');

$permissionToAdd = $user->hasRight('advancedkanban', 'advkanban','write');
$permissionToView = $user->hasRight('advancedkanban', 'advkanban','read');

// Initialize technical objects
$object = new AdvKanban($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->advancedkanban->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('advkanbanview')); // Note that conf->hooks_modules contains array

// Load object
$accessForbiden = true;
if ($id>0) {
	$ret = $object->fetch($id);
	if ($ret > 0) {
		$object->fetch_thirdparty();
		$id = $object->id;
		$accessForbiden = false;
	} else {
		if (empty($object->error) && !count($object->errors)) {
			if ($ret < 0) {	// if $ret == 0, it means not found.
				setEventMessages('Fetch on object (type '.get_class($object).') return an error without filling $object->error nor $object->errors', null, 'errors');
			}
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
		$action = '';
	}
}


if($accessForbiden || (!$permissionToAdd && !$permissionToView) ){
	accessForbidden();
}


// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');
// Load translation files required by the page
$langs->load("externalsite");
$head = '<link rel="icon" type="image/png" href="'.dol_buildpath('advancedkanban/img/object_advkanban.png', 1).'" />';
$head.= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
$arrayofjs = array(
	'advancedkanban/vendors/jkanban/dist/jkanban.js', // la librairie de base du kanban
	'advancedkanban/vendors/custom-context-menu/ContextMenu.js',
	'advancedkanban/js/liveedit.js', // la librairie live edit
	'advancedkanban/js/kanbanDragToScroll.js', // la librairie qui permet de scroll au click
	'advancedkanban/js/dialog.js',

	// TODO : bon pour l'instant dragAutoScroll ça marche pas
	//  Doit normalement permettre de scroll les liste en même temps que l'on fait un drag and drop
	//  mais je pense que le dragToScroll doit entrer en conflict
	//	'advancedkanban/js/dragAutoScroll.js',
);
$arrayofcss = array(
	'advancedkanban/css/kanban-view.css',
	'advancedkanban/css/liveedit.css',
	'advancedkanban/vendors/jkanban/dist/jkanban.css',
	'advancedkanban/vendors/custom-context-menu/ContextMenu.css'
);

$confToJs = array(
	'MAIN_MAX_DECIMALS_TOT'		=> getDolGlobalInt('MAIN_MAX_DECIMALS_TOT'),
	'MAIN_MAX_DECIMALS_UNIT'	=> getDolGlobalInt('MAIN_MAX_DECIMALS_UNIT'),
	'interface_kanban_url'		=> dol_buildpath('advancedkanban/interface-kanban.php',1),
	'interface_liveupdate_url'	=> dol_buildpath('advancedkanban/interface-liveupdate.php',1),
	'js_url'					=> dol_buildpath('advancedkanban/js/kanban-view.js',1),
	'srumprojectModuleFolderUrl'=> dol_buildpath('advancedkanban/',1),
	'fk_kanban'					=> $object->id,
	'token'						=> newToken(),
	'kanbanBackgroundUrl'		=> filter_var($object->background_url, FILTER_VALIDATE_URL) ? $object->background_url : '',
	'unsplashClientId'			=> preg_replace("/\s+/", "", getDolGlobalString('ADVKANBAN_UNSPLASH_API_KEY', '')),
	'userRight'					=> array(
			'read' => intval($permissionToView),
			'write' => intval($permissionToAdd),
	)
);

$jsLangs = array(
	'NewList' => $langs->trans('NewList'),
	'NewCard' => $langs->trans('NewCard')
);


// Display error message when conf is missing
$confsToCheck = array(

);

$parameters = array(
	'arrayofjs' => &$arrayofjs,
	'confsToCheck' => &$confsToCheck,
	'arrayofcss' => &$arrayofcss,
	'jsLangs' => &$jsLangs,
	'confToJs' => &$confToJs
);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');


top_htmlhead($head,  $object->ref . ' - ' . $object->label, 0, 0, $arrayofjs, $arrayofcss);

if(!empty($confsToCheck)){
	foreach ($confsToCheck as $confToCheck){
		$val = getDolGlobalString($confToCheck, 0);
		if(empty($val)){
			setEventMessage($langs->trans('MissingSetupStepConfiguration').' : '.$langs->trans($confToCheck), 'errors');
		}
	}
}

?>
<body id="mainbody" class="advkanban-page">
	<section id="kanban" >
		<header class="kanban-header" role="banner">
			<nav class="top-nav-bar" role="navigation">

				<div class="top-nav-group">
					<a class="nav-title nav-button classfortooltip" role="backlink" title="<?php print $langs->trans('BackToDolibarr') ?>"  href="<?php print dol_buildpath('advancedkanban/advkanban_card.php', 1).'?id='.$object->id ?>"><i class="fa fa-arrow-left"></i></a>
					<span class="nav-title"><?php print $object->getNomUrl(1) . ' <span class="kanban-title__label">'.$object->label.'</span>'; ?></span>
				</div>

				<?php
				$hookmanager->executeHooks('kanbanParamNavBar', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
				print $hookmanager->resPrint;
				?>

				<button accesskey="n"  id="addkanbancol" class="nav-button classfortooltip"  title="<?php print dol_escape_htmltag($langs->trans('AccessKeyX','n')); ?>" ><i class="fa fa-plus-circle" aria-hidden="true" ></i> <?php print $langs->trans('NewList'); ?></button>
				<button accesskey="s" id="filter-slide-toggle-btn" class="nav-button classfortooltip" title="<?php print dol_escape_htmltag($langs->trans('OpenFilterPanel').'<br/>'.$langs->trans('AccessKeyX','s')); ?>" ><i class="fa fa-filter"  aria-hidden="true"  ></i></button>
				<button accesskey="l" id="light-bulb-toggle" class="nav-button classfortooltip" title="<?php print dol_escape_htmltag($langs->trans('ToggleDarkMode').'<br/>'.$langs->trans('AccessKeyX','l')); ?>"><i class="fa fa-lightbulb" ></i></button>
				<button accesskey="p" id="kanban-option-btn" class="nav-button classfortooltip" title="<?php print dol_escape_htmltag($langs->trans('OpenOptionsSlidePanel').'<br/>'.$langs->trans('AccessKeyX','p')); ?>"><i class="fa fa-bars"></i></button>

			</nav>
		</header>
		<div id="advance-kanban"></div>
	</section>
	<section id="param-panel-container" class="panel-container --light-color-schema">
		<div class="panel-header">
			<button id="panel-close" class="panel-close-btn" title="<?php print $langs->trans('ClosePanel'); ?>" ><i class="fa fa-times"></i></button>
			<span class="panel-title"><?php print $langs->trans('KanbanSlidePanelTitle'); ?></span>
		</div>
		<div class="panel-body">

			<?php
			$reshook = $hookmanager->executeHooks('kanbanParamPanelBefore', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
			?>

			<details class="option-box">
				<summary class="option-box-title" data-focus-target="#unsplash-search-input"><?php print $langs->trans('KanbanInfos'); ?></summary>
				<div class="option-box-content">

					<div class="panel-infos">
						<?php print $object->showOutputFieldQuick('ref'); ?><br/>
						<?php print $object->showOutputFieldQuick('label'); ?><br/>
						<?php print $langs->trans('CreatedOn') ?> : <span class="fa fa-calendar"></span> <?php print $object->showOutputFieldQuick('date_creation'); ?>
					</div>

					<div class="panel-infos">
						<?php print $object->showOutputFieldQuick('description'); ?>
					</div>

					<div class="panel-infos">
						<?php print $object->note_public; ?>
					</div>

					<div class="panel-infos">
						<?php print $object->note_private; ?>
					</div>
				</div>
			</details>

		<?php
			$unslpashClientId = getDolGlobalString('ADVKANBAN_UNSPLASH_API_KEY', '');
			if(strlen($unslpashClientId) > 0){  ?>
			<!-- Start UnSpash search widget-->
			<details class="option-box">
				<summary class="option-box-title" data-focus-target="#unsplash-search-input"><?php print $langs->trans('KanbanBackgroundSetup'); ?></summary>
				<div class="option-box-content">
					<form class="unsplash-search-form">
						<input id="unsplash-search-input" type="search" name="search" placeholder="<?php print $langs->trans('SearchBackgroundOnUnsplash'); ?>" autocomplete="off">
					</form>

					<div class="unsplash-section-results">

						<?php for($i = 0; $i< 6; $i++) { ?>
						<div class="unsplash-single-result">
							<div class="unsplash-single-result-image">
								<!-- image goes here -->
							</div>
							<span class="sapunsplash-single-result__title" ><span class="loading"></span></span>
							<p><span class="loading"></span></p>
						</div>
						<?php } ?>
					</div>
				</div>
			</details>
			<!-- Start UnSpash search widget-->
		<?php } ?>


		<?php
			$reshook = $hookmanager->executeHooks('kanbanParamPanelAfter', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
		?>
		</div>
		<div class="panel-footer">
			<?php
			$reshook = $hookmanager->executeHooks('kanbanParamPanelFooter', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
			?>
		</div>
	</section>
	<section id="filter-panel-container"  class="panel-container --light-color-schema">
		<div class="panel-header">
			<button id="filter-panel-close" class="panel-close-btn" title="<?php print $langs->trans('ClosePanel'); ?>" ><i class="fa fa-times"></i></button>
			<span class="panel-title"><?php print $langs->trans('KanbanSlideFiltersTitle'); ?></span>
		</div>
		<div class="panel-body">
			<?php
			$reshook = $hookmanager->executeHooks('kanbanFilterPanelBefore', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
			?>

			<form id="card-filters-form" >
				<?php
				$form = new Form($db);

				$reshook = $hookmanager->executeHooks('kanbanFilterPanelFormBefore', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks

				if (empty($reshook)) {
					print $hookmanager->resPrint;

					//Catégories (tags)
					print "\r\n<!-- Line ".__LINE__." category form -->\r\n";
					require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

					print '<div class="panel-form-group" >';
					print '<label id="label-filter-categories" for="filter-categories">';
					print img_picto('', 'category'). ' '.$langs->trans('Categories');
					print '</label>';

					$cat = new Categorie($db);
					$arrayselected = array();
					$cate_arbo = $form->select_all_categories('advkanbancard', '', 'parent', 64, 0, 1);

					$multiSelectElemtype = intval(DOL_VERSION) > 16 ? 'category' : ''; // there is a display glith in Dolibarr < 17
					print Form::multiselectarray('filter-categories', $cate_arbo, $arrayselected, '', 0, '', 0, '95%', '', $multiSelectElemtype);
					print '<label><input type="checkbox" id="filter-categories-operator-and" name="filter-categories-operator-and" value="1" /> '.$langs->trans('NeedAllSelectionOfTags').'</label>';
					print '</div>';

					// Société

					print '<div class="panel-form-group" >';
					print '<label id="label-filter-societe" for="filter-societe" >';
					print img_picto('', 'building'). ' '.$langs->trans('Company');
					print '</label>';

					// TODO : le multi select n'a pas l'aire de fonctionner en mode ajax
					print $form->select_company('', 'filter-socid', '', 1, 0, 0, array(), 0, 'minwidth100', '','',1, array(), true);

					print '</div>';


					// User affected
					print '<div class="panel-form-group" >';
					print '<label id="label-filter-user" for="filter-user" >';
					print img_picto('', 'user'). ' '.$langs->trans('User');
					print '</label>';

					print $form->select_dolusers('', 'filter-user',1,null, 0, '', '', '0', 0, 0, '', 0, '','', 1);
					print '</div>';


				} elseif ($reshook > 0) {
					print $hookmanager->resPrint;
				}

				$reshook = $hookmanager->executeHooks('kanbanFilterPanelFormAfter', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
				print $hookmanager->resPrint;
				?>


			</form>


			<div class="result-resume-item" ><?php print $langs->trans('CardFound'); ?> : <span class="--filter-counter-text" ></span></div>

			<?php
			$reshook = $hookmanager->executeHooks('kanbanFilterPanelAfter', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
			?>
		</div>
		<div class="panel-footer">
			<?php
			$reshook = $hookmanager->executeHooks('kanbanFilterPanelFooter', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
			print $hookmanager->resPrint;
			?>


			<div class="panel-form-group" style="text-align: right;" >
				<span id="disable-filters-btn" ><span class="fa fa-times"></span> <?php print $langs->trans('DisableFilters'); ?></span>
			</div>
			<button id="activate-filters-form" class="footer-form-btn" ><?php print $langs->trans('DoFilters'); ?></button>
		</div>
	</section>
	<script>

		jQuery(function ($) {
			let config = <?php
				foreach($confToJs as &$cv){if(!is_array($cv)){ utf8_encode($cv); }}
				print json_encode($confToJs)
			?>;

			// Chargement de la librairie js
			let advps_script_to_load = document.createElement('script')
			advps_script_to_load.setAttribute('src', config.js_url);
			advps_script_to_load.setAttribute('id', 'advance-product-search-script-load');
			document.body.appendChild(advps_script_to_load);
			// now wait for it to load...
			advps_script_to_load.onload = () => {
				// script has loaded, you can now use it safely
				// Apply conf to AdvancedProductSearch object
				advKanban.init(config, <?php print json_encode($jsLangs) ?>);
			};
		});
	</script>

	<div id="dark-mode-background-overlay" class="background-overlay"></div>
</body>

<?php
llxFooter();
$db->close();
