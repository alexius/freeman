<?php

/**
 * 
 * Roles db table
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Translation extends Core_Model_Super
{
	protected $_data = array(
		'id' => null,
		'module' => null,
		'resourse' => null,
		'action' => 1,
		'code' => null,
		'caption' => null,
		'lang' => null
	);
}
