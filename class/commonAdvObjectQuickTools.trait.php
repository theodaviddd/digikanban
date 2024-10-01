<?php

trait CommonAdvObjectQuickTools
{

	/**
	 * Return HTML string to show a field into a page
	 *
	 * @param string $key Key of attribute
	 * @param string $moreparam To add more parameters on html input tag
	 * @param string $keysuffix Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param string $keyprefix Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param mixed $morecss Value for css to define size. May also be a numeric.
	 * @return string
	 */
	public function showOutputFieldQuick($key, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = '')
	{
		return $this->showOutputField($this->fields[$key], $key, $this->{$key}, $moreparam, $keysuffix, $keyprefix, $morecss);
	}

}
