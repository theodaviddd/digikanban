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
 */

/**
 * \file    lib/digikanban_kanban.lib.php
 * \ingroup digikanban
 * \brief   Library files with common functions for Kanban.
 */

// Load Saturne libraries.
require_once __DIR__ . '/../../saturne/lib/object.lib.php';

/**
 * Prepare array of tabs for kanban.
 *
 * @param  Kanban $object Kanban object.
 * @return array           Array of tabs.
 * @throws Exception
 */
function kanban_prepare_head(Kanban $object): array
{
	// Global variables definitions.

	$moreparam['documentType']       = 'KanbanDocument';
	$moreparam['attendantTableMode'] = 'simple';

	return saturne_object_prepare_head($object, $head, $moreparam, true);
}
