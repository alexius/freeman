<?php
/**
 * Get user's interface translation
 * @author Aleksey Kagatlykskiy
 * @copyright  Copyright (c) 2006-2011 S2B (http://www.s2b.com.ua)
 */

class Zend_View_Helper_Translation {

	public function translation($code)
	{
		return Zend_Registry::get('translation')->get($code);
	}
}