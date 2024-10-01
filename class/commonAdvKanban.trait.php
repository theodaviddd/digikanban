<?php

trait CommonAdvKanban {

	public function getStdClassFields(){
		$object = new stdClass();
		foreach ($this->fields as $field => $value) {
			$object->{$field} = $this->{$field};
		}
		return $object;
	}

	/**
	 * @param User $user
	 * @param      $cssClass	CSS name to use on img for photo
	 * @param      $imageSize 	'mini', 'small' or '' (original)
	 * @param      $fullSize 	Add link to fullsize image
	 * @return string html image
	 */
	static public function getUserImg(User $user, $cssClass = '', $imageSize = 'small', $fullSize = false){
		global $langs;
		$userImage = $userDropDownImage = '';
		$modulepart = 'userphoto';
//		if (!empty($user->photo)) {
			if(!class_exists('Form')){ include_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php'; }
			return Form::showphoto($modulepart, $user, 0, 0, 0, $cssClass, $imageSize, $fullSize, 1);
//		} else {
//			$nophoto = '/public/theme/common/user_anonymous.png';
//			if ($user->gender == 'man') {
//				$nophoto = '/public/theme/common/user_man.png';
//			}
//			if ($user->gender == 'woman') {
//				$nophoto = '/public/theme/common/user_woman.png';
//			}
//
//			return '<img title="'.dol_escape_htmltag($user->getFullName($langs)).'" class="photo'.$modulepart.($cssClass ? ' '.$cssClass : '').'" alt="No photo" src="'.DOL_URL_ROOT.$nophoto.'">';
//		}
	}

}
