<?php
/**
 *  Super Mapper Class
 *
 */
class Mapper_Super
{
	/**
	 * Database table object 
	 * @var object
	 */
 	protected $_dbTable;
 	
 	/**
 	 * 
 	 * @var string
 	 */
	protected $_tableName;
	
	/**
	 * Model name
	 * @var string
	 */
	protected $_rowClass;
	
	/**
	 * Fields used to pass to grid table by default
	 * @var string
	 */
	protected $_gridFields;
	protected $_customFiltersEnabled = false;
    
	public function __construct()
	{
		if (null != $this->_tableName) 
        {
            $this->setDbTable('DbTable_' . $this->_tableName);
        }
	}
	
    public function setDbTable($dbTable)
    {

        if (is_string($dbTable)) 
        {
        	$db = $dbTable;
            $dbTable = new $db();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) 
        {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        return $this->_dbTable;
    }
    
    public function getRowClass()
    {
    	return $this->_rowClass;	
    }

    public function getModel()
    {
        return new $this->_rowClass;    
    }
    
    public function insertSlug($model, $type)
    {
    	$db = $this->getDbTable()->getAdapter();
        try
        {
           $db->insert('slugs', array('item_url' => $model->url,
            'item_id' => $model->id, 'item_module' => $type));          
        } 
        catch(Zend_Exception $e)
        {          
            $model->setError("Ошибка: " . Errors::getError(112)); 
        }
        return $model;         

    }
    
    public function updateSlug($model, $type)
    {
    	$db = $this->getDbTable()->getAdapter();
        try
        {
            $db->update('slugs', array('item_url' => $model->url), 
            'item_id = "' . $model->id . '" AND item_module = "' . $type . '"');      
        } 
        catch(Zend_Exception $e)
        {          
            $model->setError("Ошибка: " . Errors::getError(112)); 
        }
        return $model;
        
	
    }
    
    public function fetchSlug($id, $type)
    {
    	$db = $this->getDbTable()->getAdapter();
    	$select = $db->select()->from('slugs')
    		->where('item_id = "' . $id . '" AND item_module  = "' . $type . '"');
    	return $db->fetchRow($select); 	
    }
    
	public function fetchForGrid(array $fields, array $data)
	{
		$primary = $this->_dbTable->getPrimary();
		$primary = $primary[1];
		$table_name = $this->_dbTable->getName();
		$range_to = 'to-' . $primary;
        $range_from = 'from-' . $primary;

		$select = $this->_dbTable->getAdapter()->select()
			->from(array('gt' => $table_name), $fields);
	    
        if (isset($data['sortcol']) && $data['sortdir'])
        {
        	$select->order($data['sortcol'] . ' ' . $data['sortdir']);
        }
        if (isset($data['page']) && $data['rows'])
        {
        	$select->limitPage($data['page'], $data['rows']);
        }
        	
       	if (!empty($this->_gridFilters))
        {
        	foreach($this->_gridFilters as $filter_name)
        	{	
        		if ($filter_name != 'primary')
       			{
	       			if (!empty($data[$filter_name]))
                    {
	       				$select->where($filter_name . ' LIKE "%' 
	       					. $data[$filter_name] . '%"');
        			}
       			} 
        		else 
        		{
	       			if (!empty($data[$range_from]) && !empty($data[$range_to]))
			       	{
			        	$select->where($primary . ' BETWEEN ' . $data[$range_from] 
			        	. ' AND ' . $data[$range_to]);
			        }
        		}
        	}
        }    

   	    $result = $this->_dbTable->getAdapter()->fetchAll($select); 

        if (empty($result))
        {
        	return array();
        }
        	
        if (!empty($data['filters']) && $data['filters'] == true)
        {
	        $select = $this->_dbTable->select()
	        	->from($table_name, array('COUNT(' . $primary . ')'));   
	        		     		
	        if (!empty($this->_gridFilters))
	        {
	        	foreach($this->_gridFilters as $filter_name)
	        	{	
	        		if ($filter_name != 'primary')
	        		{
		        		if (!empty($data[$filter_name]))
                        {
		        			$select->where($filter_name . ' LIKE "%' 
		        				. $data[$filter_name] . '%"');
		        		}
	        		} 
	        		else 
	        		{
		        		if (!empty($data[$range_from]) && !empty($data[$range_to]))
				       	{
				        	$select->where($primary . ' BETWEEN ' 
				        		. $data[$range_from] 
				        		. ' AND ' . $data[$range_to]);
				        }
	        		}
	        	}
	        }	        	
         
	        $all = $this->_dbTable->getAdapter()->fetchOne($select); 
	        	
	        if (!empty($all))
            {
	        	$result['total'] = ceil($all / $data['rows']);
	        }
        }
              
		return $result;	
	}
    
    protected  function addCustomFilters($data, Zend_Db_Select $select)
    {
        
    }
	
	public function fetchPairs($table, $where = null, array $pairs)
	{
		$class = 'DbTable_' . $table;
		$db = new $class;
		$info = $db->info();
		$s = $db->select()->from($info['name'], $pairs);
		if ($where)
        {
			$s->where($where);
		} 
		$cities = $db->getAdapter()->fetchPairs($s);
		return $cities;
	}
	
	public function runQueries($queries)
	{
		$db = $this->getDbTable()->getAdapter();	
		$db->beginTransaction();
		try
		{
			foreach ($queries as $q)
            {
				$db->query($q);
			}
			$db->commit();
		}
		catch (Exception $e) 
        {
			$db->rollBack();
    		return $e->getMessage();
		}	
		return true;
	}


    public function setRowClass($class)
    {
    	$this->_rowClass = $class;
    }

    public function partialSave(array $data)
    {
    	$table = $this->getDbTable();
    	$data = $table->cleanArray($data);  
        $pk = $table->getPrimary();
        $pk = $pk[1]; 

        $row = $table->fetchRow($pk . ' =  "' . $data[$pk] . '"');
        if ($row)
        {
            foreach ($data as $key => $value) 
            {
                $row->$key = $value;
            }
            $row->save();
            return true; 
        }
        else 
        {
            try
            {
                if ($data[$pk] == null) unset($data[$pk]);
                $table->insert($data);
                $ids = $table->getAdapter()->lastInsertId();
                $object->$pk = $ids; 
                
            } 
            catch(Zend_Exception $e)
            {          
                $object->setError("Ошибка: " . $e->getMessage() . "\n"); 
            }
            return $object;
        }
    }
    
    public function objectSave($object)
    {
    	$table = $this->getDbTable();
    	$data = $object->toArray();
        $data = $table->cleanArray($data);  
        $pk = $table->getPrimary(); 
        $pk = $pk[1];    

        
        if ($row = $table->fetchRow('id = "' . $object->$pk . '"'))
        {
            foreach ($data as $key => $value) 
            {
            	if ($object->$key != null)
                {
                	$row->$key = $object->$key;
            	}
            	else if ($object->$key == false)
                {
            		$row->$key = null;
            	}
            }
            
            try	
            {
            	$row->save();
            } 
            catch(Zend_Exception $e)
            {  			
    			$object->setError("Ошибка: " . $e->getMessage() . "\n"); 
    		}
    		return $object;
        } 
        else 
        {
        	try
    		{
    			if ($data[$pk] == null) unset($data[$pk]);
				$table->insert($data);
				$ids = $table->getAdapter()->lastInsertId();
				$object->$pk = $ids; 
				
    		} 
    		catch(Zend_Exception $e)
    		{  		
    			$object->setError("Ошибка: " . $e->getMessage() . "\n"); 
    		}
    		return $object;
        }       
    }

    public function countAll($table_name = null)
    {
    	if ($table_name === null)
        {
    		$table_name = $this->_dbTable->getName();
    	}
		return $this->getDbTable()->countAll($table_name);
    }

    public function fetchId($id)
    {
    	$pk = $this->getDbTable()->getPrimary(); 
        if (is_array($pk))
        {
        	$pk = $pk[1];	
        } 
        
        $result = $this->getDbTable()
        	->fetchRow($pk . ' = "' . $id . '"');
       	if (!empty($result))
       	{
	        $entry = new $this->_rowClass; 
	        $entry->populate($result);
	        return $entry;
       	}
       	else 
       	{
       		return false;
       	}
    }
    
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) 
        {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function fetchAllOrdered($nam, $dir)
    {
        $resultSet = $this->getDbTable()->fetchAll(null, $nam . ' ' . $dir);
        $entries   = array();
        foreach ($resultSet as $row) 
        {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        return $entries;
    }


    public function fetchAllByKey($key, $val)
    {
        $resultSet = $this->getDbTable()->fetchAll($key . ' = "' . $val . '"');
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        return $entries;
    }


    public function fetchByKey($key, $val)
    {
        $result = $this->getDbTable()
        	->fetchRow($key . ' = "' . $val . '"');
       	if (!empty($result))
       	{
	        $entry = new $this->_rowClass; 
	        $entry->populate($result);
	        return $entry;
       	}
       	else 
       	{
       		return false;
       	}
    }

    public function deleteAll($values, $valKey)
    {
    	$where = '';
    	$num = count($values);
    	foreach ($values as $key => $value)
    	{
    		if (($key + 1) != $num)
    		{
    			$where .= $valKey . ' = ' . $value . ' OR ';
    		}
    		else
    		{
    			$where .= $valKey . ' = ' . $value;
    		}
    	}
    	$this->getDbTable()->delete($where);
    }
    
	public function delete($id)
	{
		$this->getDbTable()->deleteAllByKey('id', $id);
	}
	
	public function getCountriesPairs()
	{
		$db = $this->getDbTable()->getAdapter();
		return $db->fetchPairs(
				$db->select()->from('countries', array('country_id', 'name'))
			);
	}
	
	public function getCitiesPairs($id = null)
	{

		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()->from('cities', array('city_id', 'name'));
		
		if ($id != null)
        {
			$select->where('country_id = ' . $id);
		}
		return $db->fetchPairs($select);		
	}
    
    public function getProductsPairs()
    {

        $db = $this->getDbTable()->getAdapter();
        $select = $db->select()->from('products', array('id', 'CONCAT(name, " \"",model, "\"")'));
        return $db->fetchPairs($select);        
    }
    
    public function getClientsPairs()
    {

        $db = $this->getDbTable()->getAdapter();
        $select = $db->select()->from('clients', array('id', 'CONCAT(firstname, " ", surname)'));
        return $db->fetchPairs($select);        
    }
    
    public function getPricesGroupsPairs()
    {

        $db = $this->getDbTable()->getAdapter();
        $select = $db->select()->from('prices_groups', 
            array('id', 'group_name'))
            ->order('default_group DESC');
        return $db->fetchPairs($select);        
    }
	
	public function getCountry($id)
	{
		$db = $this->getDbTable()->getAdapter();
		return $db->fetchOne(
				$db->select()->from('countries', array('name'))
				->where('country_id = "' . $id . '"')
			);
	}
	
	public function getCity($id)
	{

		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()->from('cities', array('name'));
		
		if ($id != null)
        {
			$select->where('city_id = "' . $id . '"');
		}
		return $db->fetchOne($select);		
	}
}
