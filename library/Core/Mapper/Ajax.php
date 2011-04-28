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
}