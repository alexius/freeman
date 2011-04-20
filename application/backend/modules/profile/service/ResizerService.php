<?php
/**
 * Resizer of avatar
 *
 * @author     Kagarlykskiy Aleksey  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class ResizerService extends Core_Image_Resizer
{
	public function setup(){
		$this->image_resize = true;
        $this->image_x = 90;
        $this->image_y = 120;
        $this->content = null;
        $this->mime_check = true;
	}
	
	public function resizeAvatar($file){
		$this->upload($file);
		if ($this->uploaded)
		{
	        $this->setup();
	        $resizing_content = $this->process();
	        if($this->processed){
	        	 return $resizing_content;
	        }else{
	        	return $this->error;
	        }
		}
	}
}