<?php
/**
 * Super model - common methods and values
 *  
 */

class SuperModel
{

/**
 * 	Unwanted fields array
 * 	$@var array
 */ 
	protected $_clearFields = array();
	
/**
 * 	Trigger for perfoming field clearing
 * 	@var bool  
 */ 
	protected $_perfomClear = false;
	
/**
 * 	Object data
 * 	@var array  
 */ 	

	protected $_data = array();
	protected $_error = false;
	
	public function __construct($data = null)
    {
    	if (is_array($data) || is_object($data)){
        	$this->populate($data);
    	}
    }

    public function __set($name, $value)
    {
    	if (array_key_exists($name, $this->_data)){
        	$this->_data[$name] = $value;
    	}
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        if (isset($this->$name)) {
            $this->_data[$name] = null;
        }
    }
    
    public function getError()
    {
    	return $this->_error;	
    }
    
	public function setError($error)
    {
    	$this->_error = $error; 	
    }
    
    public function toArray()
    {
    	if (!empty($this->_data)){
    		return $this->_data;
    	}
    }
    
	public function getDataKeys()
	{
		return array_keys($this->_data);
	}
    
    public function populate($data)
    {
      	if ($data instanceof Zend_Db_Table_Row){
            $data = $data->toArray();
        }
        else if (is_object($data)){
            $data = (array) $data;
        }
        if (!is_array($data)){
           	throw new Exception('Initial data must be an array or object');
        }
		
      	foreach ($data as $key => $value){
           	$this->$key = $value;
       	} 

       	if (!empty($this->_clearFields) && $this->_perfomClear == true)
       	{
       		foreach ($this->_clearFields as $key) 
      		{
           		unset($this->$key);
           		unset($this->_data[$key]);
       		}
       	}
       	
        return true;
    }
}