<?php

/**
 * 
 * The collection of Domain objects
 * @author user
 *
 */
class Core_Model_Collection_Super 
	implements Iterator,Countable
{
	
	/**
	 * 
	 * Total object count
	 * @var int
	 */
    protected $_count;
    
    /**
     * 
     * Domain object
     * @var Core_Model_Super
     */
    protected $_domainObjectClass = null;
    protected $_resultSet = array();

    public function __construct($results = null)
    {
        $this->_resultSet = $results;
    }
    
    public function populate($results)
    {
    	if (is_object($results) && $results instanceof $this->_domainObjectClass)
    	{
    		$results = $results->toArray();
    	}
    	
    	foreach ($results as $r)
    	{
    		$model = new $this->_domainObjectClass;
    		$model->populate($r);
    		$this->add($model);
    	}
    }
    
    public function add($model)
    {
    	if (empty($this->_resultSet))
    	{
    		$this->_resultSet[] = $model;
    	}
    	else
    	{
    		array_push($this->_resultSet, $model);	
    	}
    }

    public function count()
    {
        if (null === $this->_count) {
            $this->_count = count($this->_resultSet);
        }
        return $this->_count;
    }

    public function key()
    {
        return key($this->_resultSet);
    }

    public function next()
    {
        return next($this->_resultSet);
    }

    public function rewind()
    {
        return reset($this->_resultSet);
    }

    public function valid()
    {
        return (bool) $this->current();
    }

    public function current()
    {
        if ($this->_resultSet instanceof Iterator) {
            $key = $this->_resultSet->key();
        } else {
            $key = key($this->_resultSet);
        }
        
        if (array_key_exists($key, $this->_resultSet))
        	$result  = $this->_resultSet[$key];
        else
       		$result = false;
       		
        return $result;
    }
    
    public function getFirst()
    {
		if (array_key_exists('0', $this->_resultSet))
        	$result  = $this->_resultSet[0];
        else
       		$result = false;
       	return $result;
    }

}