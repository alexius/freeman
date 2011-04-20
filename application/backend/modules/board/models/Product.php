<?php
class Product extends SuperModel 
{
    protected $_data = array(
    	'id' => null,
    	'name' => null,
   		'model' => null,
    	'manufacturer' => null,
       	'status' => null,
    	'url' => null,
   		'date_add' => null,
    	'short_desc' => null,
        'catalog_preview' => null,         
    	'desc' => null,
    	'meta_title' => null,
    	'meta_keywords' => null,
    	'meta_desc' => null,
    	'price' => null, 
    	'position' => null,
   	 	'quantity' => null,
    	'gallery' => array(),
    	'pid' => null,
    	'categories_ids' => array() ,
        'show_mainpage' => 0
    );
    protected $_images = array();
    protected $_settings = array();
    
    public function getProductName()
    {
    	return $this->manufacturer . ' ' . $this->name . ' ' . $this->model;	
    }
    
    public function setSettings($settings)
    {
    	$this->_settings = $settings;
    }
    
    public function getCategories()
    {
    	return $this->categories_ids;
    }
    
    public function setCategories($ids)
    {
    	if (!empty($ids)){
    		$this->categories_ids = explode (',', $ids);
    	} else {
    		$this->categories_ids = array();
    	}
    }
    
    public function getImages()
    {
    	return $this->_images;
    }
    
    public function setImages($images)
    {
    	if (!empty($images)){
    		$this->_images = $images;
    	}
    }
    
	public function moveImagesFromTemp()
	{
		if (empty($this->_images)){
			return false;
		}

		foreach ($this->_images as $i)
		{
			if (empty($i['id']))
			{
				$thumb = $this->_settings['products_temp_dir'] . $i['src'] . '_t.jpg';
                $prev = $this->_settings['products_temp_dir'] . $i['src'] . '_q.jpg';
				$origin = $this->_settings['products_temp_dir'] . $i['src'] . '.jpg';  
				if (file_exists($thumb)){
					copy($thumb, $this->_settings['products_catalog_images_dir']. $i['src'] . '_t.jpg');
					unlink($thumb);
				}
				if (file_exists($origin)){
					copy($origin, $this->_settings['products_catalog_images_dir'] . $i['src'] . '.jpg');
					unlink($origin);
				}   
                if (file_exists($prev)){
                    copy($prev, $this->_settings['products_catalog_images_dir'] . $i['src'] . '_q.jpg');
                    unlink($prev);
                }   
			}
			
			if (($i['delete']) == 'true')
			{
				$thumb = $this->_settings['products_catalog_images_dir'] . $i['src'] . '_t.jpg';
				$origin = $this->_settings['products_catalog_images_dir'] . $i['src'] . '.jpg';
                $prev = $this->_settings['products_catalog_images_dir'] . $i['src'] . '_q.jpg';
				unlink($origin);
				unlink($thumb);
                unlink($prev);
				
			}
		}	
	}
}