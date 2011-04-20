<?php
/**
 * Mapper Super Type 
 * Base functionality
 *
 * @author     Fedor Petryk
 * @package    Core_Mapper
 * @subpackage Services
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Core_Mapper_Super 
{
	/**
	 * Database table object 
	 * @var Core_Model_DbTable_Base
	 */
 	protected $_dbTable;
 	
 	/**
 	 * The name of the table class
 	 * @var string
 	 */
	protected $_tableName = "Core_Model_DbTable_Base";

	/**
	 * Model name class
	 * @var String
	 */
	protected $_rowClass;
	
	/**
	 * Model name
	 * @var Core_Model_Super
	 */
	protected $_domainObject;

	/**
	 * Collection object
	 * @var Core_Model_Collection_Super
	 */
	protected $_collectionClass;
	
	/**
	 * Collection object
	 * @var Core_Model_Collection_Super
	 */
	protected $_domainObjectCollection;

	/**
 	 * The constructor initiates table access class
 	 * 
 	 */
	public function __construct()
	{
		if (null != $this->_tableName) {
            $this->setDbTable($this->_tableName);
        }
	}
	
	/**
	 * Return row class name
	 * @return String
	 */
	public function getRowClass()
	{
		return $this->_rowClass;	
	}
	
	/**
 	 * Creates table object
 	 * @var string - table class name  
 	 */
    public function setDbTable($dbTable)
    {

        if (is_string($dbTable)) {
        	$db = $dbTable;
            $dbTable = new $db();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception(Core_Model_Errors::getError(101, 
            	array(0 => __CLASS__)));
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
 	 * Returns table object
 	 * @return Core_Model_DbTable_Base 
 	 */
    public function getDbTable()
    {
        return $this->_dbTable;
    }
    
    /**
 	 * Returns table adapter
 	 * @return Zend_Db_Table_Abstract
 	 */
    public function getAdapter()
    {
        return $this->_dbTable->getAdapter();
    }
    
     /**
 	 * Returns domain object
 	 * @return Core_Model_Super  
 	 */   
    public function getDomainObject()
    {
    	return $this->_domainObject;	
    }

    /**
 	 * Run query
 	 * @param The query |$queries
 	 * @return the result of operations|boolean  
 	 */  
	public function runQuery($query)
	{
		$db = $this->getDbTable()->getAdapter();	
		$db->beginTransaction();
		
		try{
			$db->query($query);
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
    		return $e->getMessage();
		}	
		return true;
	}
	
    /**
 	 * Run queries in array
 	 * @param The array of queries|$queries
 	 * @return the result of operations|boolean  
 	 */  
	public function runQueries($queries)
	{
		$db = $this->getDbTable()->getAdapter();	
		$db->beginTransaction();
		try
		{
			foreach ($queries as $q){
				$db->query($q);
			}
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
    		return $e->getMessage();
		}	
		return true;
	}

     /**
 	 * Returns domain object
 	 * @param Core_Model_Super  
 	 */   
    public function setDomainObject($class)
    {
    	$this->_domainObject = $class;
    }

    /**
     * 
     * Save domain object as is
     * @param Core_Model_Super
     * @return Core_Model_Super
     */
    public function objectSave($object)
    {
    	$table = $this->getDbTable();
    	$data = $object->toArray();
        $data = $table->cleanArray($data);  
        $pk = $table->getPrimary();
		
        $pk = $pk[1]; 

		$row = $table->fetchRow( $pk . ' = "' . $object->$pk . '"');

        if (!empty($row))
        {
            foreach ($data as $key => $value) 
            {
            	if ($object->$key !== false) {
                	$row->$key = $object->$key;
            	}
            	else if ($object->$key === false){
            		$row->$key = null;
            	}

            }
		
            try	{
            	$row->save();
            } 
            catch(Zend_Exception $e){  			
    			$object->setError("Ошибка: " . $e->getMessage() . "\n"); 
    		}
    		return $object;
        } 
        else 
        {
        	try
    		{
    			if (array_key_exists($pk, $data) && 
    				$data[$pk] == null) 
    				unset($data[$pk]);
    				
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
		return false;
    }

    /**
     * 
     * Counts all rows in a table
     * @param table name | $table_name
     * @return String
     */
    public function countAll($table_name = null)
    {
    	if ($table_name === null){
    		$table_name = $this->_dbTable->getName();
    	}
		return $this->getDbTable()->countAll($table_name);
    }

    /**
     * 
     * Fetches row by id of primary key
     * @param $id
     * @return Core_Model_Super
     */
    public function fetchById($id)
    {
    	$pk = $this->getDbTable()->getPrimary(); 
        if (is_array($pk)){
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
    
    /**
     * 
     * Return a collection of domain objects
     * @return Core_Model_Collection_Super
     */
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);
        return $collection;
    }
    
    /**
     * 
     * Fetches all by where statement
     * @param Where string | $where
     * @return Core_Model_Collection_Super
     */
    public function fetchAllWhere($where)
    {
        $resultSet = $this->getDbTable()->fetchAll($where);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);
        return $collection;
    } 

    /**
     * 
     * Fetches all ordered
     * @param Order by table field | $name
     * @param ASC or DESC | $dis
     * @return Core_Model_Collection_Super
     */
    public function fetchAllOrdered($name, $dir)
    {
        $resultSet = $this->getDbTable()->fetchAll(null, $name . ' ' . $dir);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);
        return $collection;
    }

    /**
     * 
     * Fetches all by db key and it's val
     * @param db key | $key
     * @param db key value | $val
     * @return Core_Model_Collection_Super
     */
    public function fetchAllByKey($key, $val)
    {
        $resultSet = $this->getDbTable()->fetchAll($key . ' = "' . $val . '"');
       
	
        if (empty($resultSet))
        {
        	return false;	
        }
        
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new $this->_rowClass;
            $entry->populate($row);
            $entries[] = $entry;
        }
        $collection = new $this->_collectionClass($entries);
        return $collection;
    }

    /**
     * 
     * Fetches row by db key and it's val
     * @param db key | $key
     * @param db key value | $val
     * @return Core_Model_Super
     */
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

    /**
     *	Gets max id as for the versioning
     *	@return int 
     */
    public function getLastKeyVaue($key)
    {
    	$name = $this->getDbTable()->getName();
    	
   		$db = $this->getDbTable()->getAdapter();
    	$select = $db->select()
    			->from($name, 'MAX(' . $key . ') AS max');
		return $db->fetchOne($select);  		
    }
    
    /**
     * 
     * The validation method for validator element Unique
     * Checks whethere value exixts in table field or not
     * 
     * @param String $table | table witch to checl
     * @param String $field | field for checking
     * @param String $value | value of the field
     * @return boolean
     */
    public function checkUnique($table, $field, $value, $id, $primary)
    {
    	$db = $this->getDbTable()->getAdapter();
    	if ($table && $field && $value && $primary && $id)
    	{
    		$select = $db->select()
    			->from($table)
    			->where($field . ' = "' . $value . '" 
    				AND ' . $primary . ' != "' . $id . '"');
    	}
    	else if ($table && $field && $value) 
    	{
    		$select = $db->select()
    			->from($table)
    			->where($field . ' = "' . $value . '"');
    	}
    	else
    	{
    		return true;
    	}
    	
    	$row = $db->fetchRow($select);
    
    	if (empty($row))
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}
    }
    
	/**
	 * 
	 * Returns tender status by its id.
	 * @param int $tender_id | Tender id
	 * @param int $stid | status type id
	 * @return TenderStatus
	 */
	public function getTenderStatusByType($tender_id, $code)
	{
		$db = $this->getAdapter();
		
		$sql = $db->select()
		->from(array('ts' => 'tender_statuses'))
		->joinLeft(array('tst' => 'tender_statuses_types'),
			 'ts.tender_status_id = tst.tender_status_id')
		->where('ts.tender_id = ?', $tender_id)
		->where('tst.status_code = ?', $code); 
		$result = $db->fetchRow($sql);
		if (empty($result))
		{
			return false;
		}
		else
		{
			$status = new Core_Model_Status();
			$status->populate($result); 
			return $status;
		}
	}
	
    /**
     * 
     * Crearing new user (worker) for tender
     * @param $user
     * @param $id
     */
    public function createNewWorker(Core_Model_Worker $user, $id)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), 
				array('user_id', 'name', 'surname', 'patronymic', 'email'))
			->joinLeft( array('ur' => 'users_roles'), 'u.user_id = ur.user_id',
				array())
			->joinLeft( array('r' => 'roles'), 'r.role_id = ur.role_id',
				array('role_id', 'role_name'))
			->where('u.user_id = "' . $id . '"'); 
		$row = $db->fetchRow($select);
		
		if (!empty($row))
		{
			$user->populate($row);
		}
	}
	
	/**
	 * 
	 * Adds tender worker (ties user)
	 * @param Worker $worker
	 */
	public function saveTenderWorker(Core_Model_Worker $worker)
	{
		$this->setDbTable('Core_Model_DbTable_TenderUsers');
		if (empty($worker->id))
		{
			$this->getDbTable()->insert(	
				$this->getDbTable()->cleanArray(
					$worker->toArray())
			);
		}
		else
		{
			$this->getDbTable()->update(	
				$this->getDbTable()->cleanArray(
					$worker->toArray()),
				'id = "' . $worker->id . '"'	
			);		
		}
		$this->setDbTable('DbTable_Tenders');
		
	}
	
	/**
	 * 
	 * Adds tender worker (ties user)
	 * @param int $tender_id | tender id
	 * @param int $user_id | user id
	 * @return Core_Model_Worker
	 */
	public function getTenderWorker($tender_id, $user_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), 
				array('user_id', 'name', 'surname', 'patronymic', 'email'))
			->joinLeft( array('ur' => 'users_roles'), 'u.user_id = ur.user_id',
				array())
			->joinLeft( array('tw' => 'tender_users'), 'tw.user_id = u.user_id')
			->joinLeft(array('t' => 'tenders'), 't.tender_id = tw.tender_id',
				array())
			->joinLeft( array('r' => 'roles'), 'r.role_id = ur.role_id',
				array('role_id', 'role_name'))
			->where('u.user_id = "' . $user_id . '"')
			->where('t.id = "' . $tender_id . '"'); 
		$row = $db->fetchRow($select);
				
		if (!empty($row))
		{
			$worker = new Core_Model_Worker();
			$worker->populate($row);
			return $worker;
		}
		
		return false;
		
	}
	

	
	/**
	 * 
	 * Inserts new document into db
	 * returns document version id
	 * @param Core_Model_Document $doc
	 * @return String
	 */
	public function insertDocument(Core_Model_Document $doc)
	{
		$db = $this->getAdapter();

		$select = $db->select()->from('documents', array('MAX(document_id) as did'));
		$document_id = (int) $db->fetchOne($select);
		$document_id++;
		$doc->document_id = $document_id;
		$db->insert('documents', $doc->toArray());
		
		return $db->lastInsertId('documents', 'id');
	}
	
	
	/**
	 * 
	 * Returns all departments filtered by type
	 * @param array $buyers | department type 
	 * @return array
	 */
	public function getDepartmentsPairs($buyers = false)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('d' => 'departments'), array('department_id', 'department_name'));
		if ($buyers){
			$select->where('purchasing = 1');
		}
		return $db->fetchPairs($select);
	}
	
	
	/**
	 * 
	 * Add new relations for plan and tender version
	 * @param Tender $oldTender | old tender object
	 * @param Tender $sdata | new tender object
	 * @param int $pid | plan id
	 */
	public function addTenderVersionToPlan(Tender $oldTender = null, Tender $sdata, $pid)
	{
		$this->setDbTable('DbTable_PlanTender');
		$data = array ('tender_id' => $sdata->id, 'plan_id' => $pid);
		$this->getDbTable()->insert($data);
		$this->setDbTable('DbTable_Tenders');
	}
	
	/**
	 * 
	 * Get users pairs, by department if needed
	 * @param $dep_id | department id
	 * @return array | pairs user_id - user_name
	 */
	public function getUsersPairs($dep_id = null)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('u' => 'users'), array('user_id', 'name', 'surname', 'patronymic'));
			
		if ($dep_id != null) 
		{
			$sql->joinLeft(array('ud' => 'users_departments'),
				'u.user_id = ud.user_id', array())
				->where('ud.department_id = "' . $dep_id . '"');
		}
		return $db->fetchPairs($sql);
	}
	
	

	/**
	 * 
	 * Get tender plan id attached
	 * @param int $tender_id | tender id
	 * @return String
	 */
	public function getTenderPlanId($tender_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('pt' => 'plans_tenders'), array('MAX(plan_id)'))
			->where('tender_id = "' . $tender_id . '"');
		return $db->fetchOne($sql);
	}
	
	/**
	 * 
	 * Get tender by version id
	 * @param int $tender_id | tender id
	 */
	public function getTender($tender_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from (array('t' => 'tenders'))
			->where('t.id = "' . $tender_id . '"');
	

		$result = $db->fetchRow($sql);
		
		if (!empty($result)) 
		{
			$tender = new Tender(); 	
			$tender->populate($result);
			return $tender;	
		}
		return false;
	}
	
	/**
	 * 
	 * Delete tender version from plan version
	 * @param Tender $tender | tender object 
	 * @param int $pid | plan id
	 * 
	 */
	public function deleteTenderVersionFromPlan(Tender $tender, $pid)
	{
		$this->setDbTable('DbTable_PlanTender');
		$this->getDbTable()->delete(
			'tender_id = "' . $tender->id . '"
				AND plan_id = "' . $pid . '"'
		);
		$this->setDbTable($this->_dbTable);
	}
	
	
	/**
	 * 
	 * Get tender auhorizer (user)
	 * @param int $tender_id | tender id
	 * @return User | boolean
	 */
	public function getTenderAuthorizer($tender_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('t' => 'tenders'), array())
			->joinLeft(array( 'u' => 'users'), 
				't.authorizer_id = u.user_id',
				array('user_id', 'name', 'surname', 'patronymic', 'seat'))
			->where('t.id = "' . $tender_id . '"');
		$user = $db->fetchRow($sql);

		if (!empty($user))
		{
			$user = new User($user);
			return $user;
		}
		return false;
	}
	
	/**
	 * 
	 * Get tender respoinsible user
	 * @param int $tender_id | tender id
	 * @return User | boolean
	 */
	public function getTenderResponsible($tender_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('t' => 'tenders'), array())
			->joinLeft(array( 'u' => 'users'), 
				't.creator_id = u.user_id',
				array('user_id', 'name', 'surname', 'patronymic', 'seat'))
			->where('t.id = "' . $tender_id . '"');
		$user = $db->fetchRow($sql);

		if (!empty($user))
		{
			$user = new User($user);
			return $user;
		}
		return false;
	}
	
	/**
	 * 
	 * Checks if user is department chief
	 * @param int $department_id | department id to check
	 * @param int $user_id | user to check
	 * @return User | boolean
	 */
	public function isDepartmentChief($department_id, $user_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('d' => 'departments'),
				array('department_id'))
			->joinLeft(array( 'ud' => 'users_departments'), 
				'ud.department_id = d.department_id AND ud.chief = 1', 
				array())
			->joinLeft(array( 'u' => 'users'), 
				'ud.user_id = u.user_id',
				array('user_id', 'name', 'surname', 'patronymic'))
			->where('d.department_id = "' . $department_id . '"')
			->where('ud.chief = 1 AND u.user_id = "' . $user_id . '"');
		$user = $db->fetchRow($sql);
	 
		if (!empty($user))
		{
			$user = new User($user);
			return $user;
		}
		return false;
	}

	

		/**
	 *
	 * Get attached tender workers
	 * @param int $tender_id | tender version id
	 * @return Collection_TenderWorkers
	 */
	public function getTenderWorkers($tender_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('t' => 'tenders'), array())
			->joinInner(array('tw' => 'tender_users'),
				'tw.tender_id = t.tender_id')
			->joinInner(array('u' => 'users'),
				'tw.user_id = u.user_id')
			->joinLeft(array('ui' => 'users'),
				'ui.user_id = tw.inviter_id',
				array('name AS inviter'))
			->joinLeft(array('ur' => 'users_roles'),
				'ur.user_id = tw.user_id',
				array())
			->joinLeft(array('r' => 'roles'),
				'r.role_id = ur.role_id',
				array('role_name'))
			->where('t.id = "' . $tender_id . '"');
		$result = $db->fetchAll($sql);
		$collection = new Collection_TenderWorkers();
		if (!empty($result))
		{
			$collection->populate($result);
		}
		return $collection;
	}
	
	/**
	 * 
	 * Delete user from tender workgroup
	 * @param int $tender_id | tender id
	 * @param int $user_id | user id
	 * @return boolean
	 */
	public function deleteTenderWorker($tender_id, $user_id)
	{
		$db = $this->getDbTable()->getAdapter();
		$db->delete('tender_users', 
			'user_id = "' . $user_id . '" AND tender_id = "' . $tender_id . '"');
		return true;
	}
}