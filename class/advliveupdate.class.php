<?php

/**
 * Tool class for live update
 * use class with statics methods to easiest copy to another module
 */
class AdvLiveUpdate {


	/**
	 * @param string $element             the commonobject element
	 * @param int    $fk_element          the object id
	 * @param string $field               field code to update
	 * @param string $ajaxSuccessCallback a javascript function name used for call back on update fail
	 * @param string $ajaxIdleCallback    a javascript function name used for call back on update do nothing
	 * @param string $ajaxFailCallback    a javascript function name used for call back on update fail
	 * @return string
	 */
	static function genLiveUpdateAttributes($element, $fk_element, $field, $ajaxSuccessCallback = '', $ajaxIdleCallback = '', $ajaxFailCallback = ''){
		$liveEditInterfaceUrl = dol_buildpath('advancedkanban/interface-liveupdate.php',2);
		$liveEditInterfaceUrl.= '?element='.$element;
		$liveEditInterfaceUrl.= '&fk_element='.$fk_element;
		$liveEditInterfaceUrl.= '&field='.$field;

		$attributes = array(
			'data-ajax-target' => $liveEditInterfaceUrl,
			'data-live-edit' => 1
		);

		if(!empty($ajaxSuccessCallback)){
			$attributes['data-ajax-success-callback'] = $ajaxSuccessCallback;
		}

		if(!empty($ajaxIdleCallback)){
			$attributes['data-ajax-idle-callback'] = $ajaxIdleCallback;
		}

		if(!empty($ajaxFailCallback)){
			$attributes['data-ajax-fail-callback'] = $ajaxFailCallback;
		}

		$Aattr = array();
		if (is_array($attributes)) {
			foreach ($attributes as $attribute => $value) {
				if (is_array($value) || is_object($value)) {
					continue;
				}
				$Aattr[] = $attribute.'="'.dol_escape_htmltag($value).'"';
			}
		}

		return !empty($Aattr)?implode(' ', $Aattr):'';
	}
}
