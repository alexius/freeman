<?php

class DbTable_Products extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'products';
    protected $_primary = 'id';  

	public function deleteProductCategories($key, $value)
	{
		$this->getAdapter()->delete('products_categories',
		$this->getAdapter()->quoteInto($key . ' = ?', $value));
	}
	
	public function deleteProductImages($key, $value)
	{
		$this->getAdapter()->delete('products_images',
		$this->getAdapter()->quoteInto($key . ' = ?', $value));
	}
	
	public function deleteProductAttributes($id)
	{
		$this->getAdapter()->delete('products_attributes',
		$this->getAdapter()->quoteInto('val_product_id = ?', $id));
	}
    
    public function deleteProductGroups($key, $value)
    {
        $this->getAdapter()->delete('products_prices_groups',
        $this->getAdapter()->quoteInto($key . ' = ?', $value));
    }
    
    public function deleteAllByKey($key, $value)                                
    {
        if ((int) $value != 0)
        { 
            parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));                                                                                                                                                                        
            $this->getAdapter()->delete('slugs', 
                $this->getAdapter()->quoteInto('item_id = ?', $value));   
        }
    }
    
}
