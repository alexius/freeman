<?php
/**
 * Base class for Domain object
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Core_Model_Super
{

	/**
	 * 	Object data (Database fields) as field_name => val (null)
	 * 	@var array  
	 */ 	

	protected $_data = array();

	/**
	 * 
	 * Error value if occured
	 * @var String
	 */
	protected $_error = array();
	
	/**
	 * 
	 * Countructs domain object
	 * @param array of fields or object for object creation | $data
	 */
	public function __construct($data = null)
    {
    	if (is_array($data) || is_object($data)){
        	$this->populate($data);
    	}
		$this->_init();
    }

    /**
     * 
     * The magic set function 
     * Checks whethere $name exists in $_data
     * 
     * @param param name String | $name
     * @param param value | $value
     */
    public function __set($name, $value)
    {
    	if (array_key_exists($name, $this->_data)){
        	$this->_data[$name] = $value;
    	}
    }

        /**
     * 
     * The magic get function 
     * Checks whethere $name exists in $_data and returns value
     * 
     * @param param name String | $name
     * @return String
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    /**
     * 
     * The magic isset function 
     * @param param name String | $name
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * 
     * The magic unset function 
     * @param param name String | $name
     */
    public function __unset($name)
    {
        if (isset($this->$name)) {
            $this->_data[$name] = null;
        }
    }

    /**
     * 
     * Returns error
     * @return String
     */
    public function getError()
    {
    	return $this->_error;	
    }
    
    /**
     * 
     * Sets error
     * @param String | $error
     */
	public function setError($error)
    {
    	$this->_error = $error; 	
    }

    /**
     * 
     * Returns array represantation of object dada
     * @return array
     */
    public function toArray()
    {
    	if (!empty($this->_data)){
    		return $this->_data;
    	}
    }

    /**
     * 
     * returns keys of objectr
     * @return array
     */
	public function getDataKeys()
	{
		return array_keys($this->_data);
	}
    
	/**
	 * 
	 * Populates the data to object fields
	 * @param array |  $data
	 * @throws Exception
	 */
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
       	
        return true;
    }

	/**
	 * Runs after object population
	 * @return void
	 */
	protected function _init()
	{

	}

}