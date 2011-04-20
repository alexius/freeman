<?php  

class DbTable_Base extends Zend_Db_Table_Abstract
{
	protected $_name;
	protected $_primary;
	
    public function getPrimary()
    {
        return $this->_primary;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function fetchOneField($field, $id)
    {
    	$select = $this->select()
    		->from($this->_name, array($field))
    		->where($this->_primary[1] . ' = ' . $id);
    	return parent::getAdapter()->fetchOne($select);
    }
    
	public function cleanArray($arr)
	{
    	return array_intersect_key($arr, array_combine(parent::info('cols'), parent::info('cols')));
	}

	public function update(array $data, $where)
	{
		return parent::update($this->cleanArray($data), $where);
	}

	public function insert(array $data)
	{
		return parent::insert($this->cleanArray($data));
	}
	
	public function deleteOneByKey($key, $value)
	{
		/*parent::delete($this->getAdapter()
						->quoteInto($key . ' = ?', $value))
						->limit(1);*/
	}
	
	public function deleteAllByKey($key, $value)
	{
		parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));
	}

	public function countAll($table_name = null)
	{
		if (null === $table_name){
			$table_name = $this->_name;
		}
		
		$select = $this->select()->from($table_name, array('COUNT(*) as total_rows'));
		return $this->getAdapter()->fetchOne($select);		
	}
    
    public function deleteById($id)
    {
        parent::delete($this->getAdapter()->quoteInto('id = ?', $id));
    }

}
