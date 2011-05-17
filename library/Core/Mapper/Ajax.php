<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 28.04.11
 * Time: 19:21
 * To change this template use File | Settings | File Templates.
 */
 
class Core_Mapper_Ajax extends Core_Mapper_Super
{

   public function insertSlug($model, $module)
   {
    	$db = $this->getDbTable()->getAdapter();
        try
        {
           $db->insert('slugs', array('item_url' => $model->url,
            'item_id' => $model->id, 'item_module' => $module));          
        }
        catch(Zend_Exception $e)
        {
            $model->setError(Core_Model_Errors::getError('url_exists'));
        }
        return $model;
    }

    public function updateSlug($model, $module)
    {
    	$db = $this->getDbTable()->getAdapter();
        try
        {
            $db->update('slugs', array('item_url' => $model->url),
            'item_id = "' . $model->id . '" AND item_module = "' . $module . '"');
        }
        catch(Zend_Exception $e)
        {
            $model->setError(Errors::getError('url_exists'));
        }
        return $model;


    }

    public function fetchSlug($id, $module)
    {
    	$db = $this->getDbTable()->getAdapter();
    	$select = $db->select()->from('slugs')
    		->where('item_id = "' . $id . '" AND item_module  = "' . $module . '"');
    	return $db->fetchRow($select);
    }

    public function fetchSlugByUrl($url, $id = null)
    {
    	$db = $this->getDbTable()->getAdapter();
    	$select = $db->select()->from('slugs')
    		->where('item_url = "' . $url . '"');
        if ($id != null){
            $primary = $this->_dbTable->getPrimary();
            if (is_array($primary)){
		        $primary = $primary[1];
            }
            $select->where($primary . ' = ?', $id , 'INTEGER');
        }
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
                $object->setError("Îøèáêà: " . $e->getMessage() . "\n");
            }
            return $object;
        }
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

    public function delete($id, $module = null)
	{
        $this->getDbTable()->deleteAllByKey('id', $id);

        if ($module != null){
            $this->getAdapter()->delete('slugs',
                'item_id = "' . $id . '" AND item_module = "' . $module . '"'
            );
        }

	}

    protected  function addCustomFilters($data, Zend_Db_Select $select)
    {

    }
}