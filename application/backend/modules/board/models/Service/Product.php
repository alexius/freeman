<?php
class Service_Product extends Service_Super 
{
	protected $_mapperName = 'Products';
	protected $_formName = 'Product';
	protected $_gridFields = array ('id', 'name', 'model',
									'url', 'price', 'position', 'show_mainpage', 'status');


	public function save(array $data)
	{
		if (!empty($data['id']))
		{
			$model = $this->_mapper->fetchId($data['id']);	
		} 
        else 
        {
			$model = new Product();	
		}

        $settings = new Service_Settings();
        $sgroup = $settings->getMapper()->fetchSettingsGroup('products');
        $model->setSettings($sgroup);
        
		if ($this->_validator->isValid($data))
		{
			$filtered_data = $this->_validator->getValues();
			
			$model->populate($filtered_data);
			$model = $this->_mapper->objectSave($model);

			if (!empty($data['categories_ids']))
            {
				$model->setCategories($data['categories_ids']);
				$model = $this->_mapper->saveCategories($model);	
			}
			if (isset($data['images_srcs']))
			{
				$images = Zend_Json::decode($data['images_srcs']);
				if (!empty($images))
                {
					$model->setImages($images);
					$this->_mapper->saveImages($model);
					$model->moveImagesFromTemp();
				}	
			}

			
			$this->saveSlug($model, 'products');
			
			if ($o = $model->getError())
            {
				return $o;
			}           
			else 
            {
				$this->_validator->populate($model->toArray());
				return true;
			}
		}
		else 
		{
			return false;
		}
	}
	
	public function getImages($id)
	{
		if ((int) $id <= 0)
        {
			return false;
		}
		
		$images = $this->_mapper->getImages($id);
		if (empty($images))
        {
			return false;
		}
		
		$images_list = '';
		foreach($images as $i)
		{
			$images_list .= '<li>
					<input id = "i' . $i['id'] .  '"  name = "img_name" type = "hidden" value = "' . $i['image'] . '">
						<div class = "block"> <img src = "/images/products/' . $i['image'] . '_q.jpg"></div>
						<div class = "block">
						Название: <input name = "title" type = "text" value = "' . $i['title'] . '">
						<br>Удалить <input name = "delete" type = "checkbox" value = "1">
						</div>
						<div style = "clear:both;"></div>
					</li>';
		}
		return $images_list;
	}
	
	public function uploadImages()
	{
		$img_handler = new ImagesHandler();
		return $img_handler->uploadProductImage();
	}
	
	
	
	public function getForm($cid = null)
	{         	
		if ($cid === true)
        {
			return  $this->_validator;
		}
			
        if (isset($this->_validator)) 
        {
        	if ($cid <= 0)
            {
            	$this->_validator->removeElement('delete');
            	return 	$this->_validator;
        	}
            else 
            {
        		if ($o = $this->_mapper->fetchId($cid))
                {
        			return $this->_validator->populate($o->toArray());
        		} 
                else 
                {
        			return  $this->_validator;
        		}        		
        	}
        }       	
	}	
	
	public function delete($id)
	{
		$this->getMapper()->delete($id);
		$images = $this->_mapper->getImages($id);
		$this->deleteImages($images);
	}	
	
	private function deleteImages($images)
	{		
		if (empty($images))	
        {
			return false;
		}
			
		$settings = new Service_Settings();			
		$sgroup = $settings->getMapper()->fetchSettingsGroup('products');			
		foreach($images as $i)			
		{
			$thumb = $sgroup['products_images_dir'] . $i['image'] . '_s.jpg';
			$origin = $sgroup['products_images_dir'] . $i['image'] . '.jpg';
			if (file_exists($thumb))
            {
				unlink($thumb);
			}
			if (file_exists($origin))
            {
				unlink($origin);
			}
		}	
	}
}