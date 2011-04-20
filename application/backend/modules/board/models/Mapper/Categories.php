<?php
class Mapper_Categories extends Mapper_Super
{
	protected $_tableName = 'Categories';
	protected $_rowClass = 'Category';
	protected $_gridFilters = array(
									1 => 'primary', 
									2 => 'name',
									3 => 'url'
								);

	public function getProductsCategories($id = 0, $prod_id = 0)
	{
		$table = $this->getDbTable()->getAdapter();
		$select = $table->select()
				->from(array('c' => 'categories'), 
					array('id','name','parent_id'))
				->joinLeft(array('pc' => 'products_categories'), 
					'pc.product_id = ' . $prod_id . ' AND pc.category_id = c.id',
					array('product_id'))
				->order('c.parent_id ASC');
		if ($id > 0){
			$select->where('c.parent_id = ' . $id);
		}
        $resultSet = $table->fetchAssoc($select);
        return $resultSet;
	}
	
	public function fetchImage($id)
	{
		return $this->_dbTable->fetchOneField('image', $id);		
	}
	
	public function getCategoriesTree($id)
	{
		$table = $this->getDbTable()->getAdapter();
		$select = $table->select()
				->from(array('c' => 'categories'), 
					array('id','name','parent_id'))
				->where('c.parent_id = ' . $id);
        $resultSet = $table->fetchAll($select);
        $entries = array();
        if (!empty($resultSet))
        {
        	foreach ($resultSet as $row) 
        	{
	            $entry = new $this->_rowClass;
	            $entry->populate($row);
	            $entries[] = $entry;
       		}
        	return $entries;        	
        }	
	}
	
	public function getChilds($id)
	{
		$table = $this->getDbTable()->getAdapter();
		$select = $table->select()
				->from('categories', array('id','image','parent_id'))
				->where('parent_id = ' . $id);
        $resultSet = $table->fetchAll($select);
        if (!empty($resultSet))
        {
        	return $resultSet;
        }
	}
	
	public function deleteCategories($cats)
	{
		if (!empty($cats))
		{
			$where = '';
			$total = count($cats);
			foreach ($cats as $key => $c)
			{
				$where .= ' id = ' . $c['id'];
				if ($total != ($key + 1)){
					$where .= ' OR ';
				}
			}
			$this->getDbTable()->deleteAllWhere($where);
			return true;
		}
	}
}