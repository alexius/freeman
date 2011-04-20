<?php

/**
 * 
 * Role row class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Role extends Core_Model_Super
{
	protected $_data = array(
		'role_id' => null,
		'role_name' => null,
		'role_code' => null,
		'editable' => 1,
		'default_module' => null,
		'active' => null
	);
	
	/**
	 * 
	 * Array of role rules
	 * @var array
	 */
	protected $_rules = array();
	
	public function getRules ()
	{
		return $this->_rules;	
	}
	
	/**
	 * 
	 * Setting role rules
	 * @param array $rules
	 */
	public function setRules(array $rules)
	{
		$this->_rules = $rules;
	}
}