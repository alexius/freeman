<?php

class Settings extends SuperModel 
{
	protected $_data = array(
    	'id' => null,
    	'param_value' => null,
       	'param_description' => null,
        'active' => 0,
        'param_group' => null
    );
    
    public function __toString()
    {
    	return $this->param_value;
    }
}