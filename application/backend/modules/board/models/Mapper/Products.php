<?php
class Mapper_Products extends Mapper_Super
{
	protected $_tableName = 'Products';
	protected $_rowClass = 'Product';
	protected $_gridFilters = array(1 => 'primary', 2 => 'name',
									3 => 'model');
	protected $_customFiltersEnabled = false;
    				
	public function saveImages($model)
	{
		$images = $model->getImages();
		$queries = array();
		if (!empty($images))
		{
			$insert = false;
			$delete = false;
			$for_deletion = array();
			$query = 'INSERT INTO products_images (product_id, image, title, position) VALUES ';
			$query_del = 'DELETE FROM products_images WHERE ';
			foreach ($images as $key => $a)
			{
				if ($a['delete'] == 'false' && $a['id'] == '' && !empty($a['src']))
				{
					$insert = true;
					$query .= ' (' . $model->id . ', "' . $a['src'] . '", 
						"' . $a['title'] . '", '.$a['pos'].')';
					if (count($images) > ($key + 1)){
						$query .= ', ';
					}
				} 
				else if ($a['delete'] == 'false' && $a['id'] > 0)
				{ 
					$queries[] = 'UPDATE products_images 
						SET title = "' . $a['title'] . '",
                            position =  "' . $a['pos'] . '"
						WHERE product_id = "' . $model->id . '" AND 
							id = "' . $a['id'] . '"';
				} 
				else if ($a['delete'] == 'true' && $a['id'] > 0)
				{
					$delete = true;
					$for_deletion[] = $a['id'];
				}
			}
			 
			foreach ($for_deletion as $key => $fd)
			{
				$query_del .= 'id = ' . $fd;
				if (count($for_deletion) > $key+1){
					$query_del .= ' OR ';
				}
			}

			if ($delete == true){
				$queries[] = $query_del;
			}
			if ($insert == true){
				$queries[] = $query;
			}
			$this->runQueries($queries);
		}
		return $model;		
	}
	
	public function getImages($id)
	{
		$select = $this->getDbTable()->getAdapter()->select()
			->from('products_images')
			->where('product_id = ' . $id)
            ->order('position ASC');
		return $this->getDbTable()->getAdapter()->fetchAll($select);
	}
	
   
	public function saveCategories(Product $model)
	{
		$categories_ids = $model->getCategories();
		$this->getDbTable()->deleteProductCategories('product_id', $model->id);
		
		if (!empty($categories_ids))
		{
			$query = 'INSERT INTO products_categories (product_id, category_id) VALUES ';
			foreach ($categories_ids as $key => $a)
			{
				$query .= ' (' . $model->id . ', ' . $a . ')';
				if (count($categories_ids) > ($key + 1)){
					$query .= ', ';
				}
			}
			$this->getDbTable()->getAdapter()->query($query);
		}
		return $model;
	}
}