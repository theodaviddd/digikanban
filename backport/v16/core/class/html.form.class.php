<?php



if(intval(DOL_VERSION) > 15 ){
	class SPRetroCompatibilityForm extends Form
	{

	}
}
else{

	/**
	 *	Class to manage generation of HTML components
	 *	Only common components must be here.
	 *
	 *  TODO Merge all function load_cache_* and loadCache* (except load_cache_vatrates) into one generic function loadCacheTable
	 */
	class SPRetroCompatibilityForm extends Form
	{



		/**
		 * Output the buttons to submit a creation/edit form
		 *
		 * @param   string  $save_label     Alternative label for save button
		 * @param   string  $cancel_label   Alternative label for cancel button
		 * @param   array   $morebuttons    Add additional buttons between save and cancel
		 * @param   bool    $withoutdiv     Option to remove enclosing centered div
		 * @param	string	$morecss		More CSS
		 * @return 	string					Html code with the buttons
		 */
		public function buttonsSaveCancel($save_label = 'Save', $cancel_label = 'Cancel', $morebuttons = array(), $withoutdiv = 0, $morecss = '')
		{
			global $langs;

			$buttons = array();

			$save = array(
				'name' => 'save',
				'label_key' => $save_label,
			);

			if ($save_label == 'Create' || $save_label == 'Add' ) {
				$save['name'] = 'add';
			} elseif ($save_label == 'Modify') {
				$save['name'] = 'edit';
			}

			$cancel = array(
				'name' => 'cancel',
				'label_key' => 'Cancel',
			);

			!empty($save_label) ? $buttons[] = $save : '';

			if (!empty($morebuttons)) {
				$buttons[] = $morebuttons;
			}

			!empty($cancel_label) ? $buttons[] = $cancel : '';

			$retstring = $withoutdiv ? '': '<div class="center">';

			foreach ($buttons as $button) {
				$addclass = empty($button['addclass']) ? '' : $button['addclass'];
				$retstring .= '<input type="submit" class="button button-'.$button['name'].($morecss ? ' '.$morecss : '').' '.$addclass.'" name="'.$button['name'].'" value="'.dol_escape_htmltag($langs->trans($button['label_key'])).'">';
			}
			$retstring .= $withoutdiv ? '': '</div>';

			return $retstring;
		}

	}
}


