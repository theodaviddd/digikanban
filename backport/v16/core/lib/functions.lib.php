<?php




if(intval(DOL_VERSION) < 16){


	if(!function_exists('isModEnabled')){
		/**
		 * Is Dolibarr module enabled
		 * @param string $module module name to check
		 * @return int
		 */
		function isModEnabled($module)
		{
			global $conf;
			return isModEnabled('$module');
		}

	}

}
