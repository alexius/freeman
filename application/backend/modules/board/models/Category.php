<?php
class Category extends SuperModel 
{
    protected $_data = array(
    	'id' => null,
    	'parent_id' => null,
   		'name' => null,
    	'desc' => null,
       	'image' => null,
    	'meta_title' => null,
   		'meta_desc' => null,
    	'meta_keywords' => null,
    	'url' => null,
    	'active' => null,
    	'root' => null,
    	'image_loader' => null,
    );
    
    public function clearImage()
    {
    	if (isset($this->image))
    	{
    		unset($this->image);
    	}
    }
    
    public function uploadImage()
    {
		$img_upload = new ImagesHandler();
		$img_upload->uploadImage($this);
    }
    

      	
}