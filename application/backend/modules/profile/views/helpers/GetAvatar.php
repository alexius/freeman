<?php 
/**
 * Get avatar helper
 *
 * @author     Kagarlykskiy Aleksey  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Profile_Helper_GetAvatar extends Zend_View_Helper_Abstract 
{
	
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}
	
	public function getAvatar(){
		return $this->view->service->getAvatar();
	}
}
?>
