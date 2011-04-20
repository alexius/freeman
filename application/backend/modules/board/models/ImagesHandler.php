<?php

class ImagesHandler extends Moo_Upload 
{
	protected $_uploadPath = '../upload/images/';
	protected $_thumbResize = false;
	protected $_resize = false;
	protected $_resizeHeight;
	protected $_resizeWidth;
	
	private function setUploadSettings($group)
	{
		$settings = new Service_Settings();
		$settings_group = $settings->getMapper()->fetchSettingsGroup($group);

		$this->_uploadPath = $settings_group[$group . '_temp_dir'];
		//$this->_resizeHeight = $settings_group[$group . '_image_height'];
		//$this->_resizeWidth = $settings_group[$group. '_image_width'];
		//$this->_resize = $settings_group[$group. '_resize'];
	}
	
	private function generatefileName()
	{
		return substr(md5(rand()), 0, 10);
	}
	
	private function _findExtension($filename)
	{
		return substr(strrchr($filename, '.'), 1);
	}
	
	public function uploadProductImage()
	{
		$this->setUploadSettings('products');
		$upload_name = "Filedata";
		$file_name = $_FILES[$upload_name]["name"];
		
		if (function_exists('pathinfo'))
		{
			$inf = pathinfo ($file_name);
			$file_name = $inf['filename'];
			$ext = $inf['extension'];
		}
		else 
		{
			return Errors::getError(98);

		}
		
		if (!$ext || !$file_name) {
			return Errors::getError(105);
		 }
			
		$file_name = $this->generatefileName();
		$this->upload($_FILES[$upload_name]);
		

		if ($this->uploaded) {

            $this->jpeg_quality         = 100;
            $this->image_src_type       = $ext;
            $this->image_watermark      =  '../admin/styles/images/watermark.png';
            $this->image_watermark_position = 'BR';
            $this->image_watermark_x = -20;
            $this->image_watermark_y = -20;
            $this->file_overwrite          = true;
            $this->mime_check              = true;
            $this->file_auto_rename     = false;
            $this->image_convert        = $ext;
            
            $this->file_new_name_body   = $file_name;
            $this->image_resize         = true;
            $this->image_ratio          = true;  
            $this->image_x              = 375;
            $this->image_y              = 355;
            $this->process($this->_uploadPath->__toString());
            
            $this->file_new_name_body   = $file_name . '_q';
            $this->image_ratio          = true;
            $this->image_resize         = true;
            $this->image_x              = 202;
            $this->image_y              = 173;
            $this->process($this->_uploadPath->__toString());

           
            $this->file_new_name_body   = $file_name . '_t';
            $this->image_ratio          = true;
            $this->image_ratio_crop     = true;
            $this->image_resize         = true;
            $this->image_x              = 62;
            $this->image_y              = 62;
            $this->process($this->_uploadPath->__toString());
             

			if ($this->processed)
			{
				return $file_name;
				 
			}
			else {
				return $this->error;
			}

		}		
	}
	
	public function uploadImage($model)
	{
		$upload_name = "image_loader";
		$file_name = $_FILES[$upload_name]["name"];
		
		if (function_exists('pathinfo'))
		{
			$inf = pathinfo ($file_name);
			$file_name = $inf['filename'];
			$ext = $inf['extension'];
		}
		else 
		{
			$model->setError(Errors::getError(98));  
			return $model;
		}
		
		if (!$ext || !$file_name) {
			$model->setError(Errors::getError(105)); 
			return $model;
		 }
			
		$file_name = $this->generatefileName();
		$this->upload($_FILES[$upload_name]);
		
	
		if ($this->uploaded) {

			$this->jpeg_quality         = 100;
		 	$this->image_src_type       = $ext;
			$this->file_new_name_body   = $file_name;
			//$this->image_ratio          = true;
			$this->file_overwrite		  = true;
			$this->mime_check		      = true;
			$this->file_auto_rename     = false;
			$this->image_convert        = $ext;
			
			//$this->image_resize         = $resize;
			//$this->image_x              = $logo_resize_x->param_value;
			//$this->image_y              = $logo_resize_y->param_value;
			$this->process($this->_uploadPath);

			if ($this->processed)
			{
				$model->image = $file_name . '.jpg';
				return $model;
			}
			else {
				
				$model->setError($this->error);
				return $model;
			}

		}		
	}
}