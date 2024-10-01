<?php
/* Copyright (C) 2020 Maxime Kohlhaas <maxime@m-development.com>
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

require_once __DIR__ . '/../backport/v16/core/lib/functions.lib.php';

/**
 * \file    advancedkanban/lib/advancedkanban.lib.php
 * \ingroup advancedkanban
 * \brief   Library files with common functions for AdvancedKanban
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function advancedkanbanAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("advancedkanban@advancedkanban");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/advancedkanban/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
	$head[$h][0] = dol_buildpath("/advancedkanban/admin/advkanban_extrafields.php", 1);
	$head[$h][1] = $langs->trans("AdvKanbanExtraFields");
	$head[$h][2] = 'advkanban_extrafields';
	$h++;

	$head[$h][0] = dol_buildpath("/advancedkanban/admin/advkanbancard_extrafields.php", 1);
	$head[$h][1] = $langs->trans("AdvKanbanCardExtraFields");
	$head[$h][2] = 'advkanbancard_extrafields';
	$h++;

    $head[$h][0] = dol_buildpath("/advancedkanban/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@advancedkanban:/advancedkanban/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@advancedkanban:/advancedkanban/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'advancedkanban');

	return $head;
}

