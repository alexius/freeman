<?php

/**
 *
 * The collection of Domain objects
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Collection_Translations extends Core_Model_Collection_Super
{
    protected $_domainObjectClass = 'Translation';

	public function generateInputs()
	{
		if ($this->count() < 1){
			return false;
		}

		$conf = Zend_Registry::get('app_config');
		$langs = $conf['languages'];
		$defLang = $langs[$conf['lang']];
		
		$htmlInputs = '<table class="default-table"><thead><tr>
				<th>Код</th>';

		foreach ($langs as $l => $val)
		{
			$htmlInputs .= '<th>' . $val . '</th>';
		}
		$htmlInputs .= '</tr></thead><tbody>';

		$sortedByLang = array();
		foreach ($this as $trans)
		{
			$sortedByLang[$trans->code][$trans->lang] = $trans;
		}

		foreach ($sortedByLang as $code => $trans)
		{
			$htmlInputs .= '<tr>';
			$htmlInputs .= '<td>' . $code . '</td>';
			foreach ($langs as $l => $val)
			{

				if (array_key_exists($l, $trans))
				{
					$obj = $trans[$l];
					$caption = htmlspecialchars (($obj->caption));

					$htmlInputs .= '<td><input name="trans[' . $code . '][' . $l .']" type="text" value="'
							. $caption . '"></td>';

				}
				else
				{
					$htmlInputs .= '<td><input name="trans[' . $code
							. '][' . $l .'][new]" type="text" value=""></td>';
				}
			}
			$htmlInputs .= '</tr>';
		}
		$htmlInputs .= '</tbody></table>';
		
		return $htmlInputs;
	}
}