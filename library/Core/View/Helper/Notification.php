<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_Notification
{
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * 
	 * Return current module base link path
	 * @param int $base | if needed to return raw path
	 */
	public function notification($message = null, $type)
	{
        $imgLink = $this->view->domainLink(1)
            . 'images/icons/cross_grey_small.png';

		if ($type == 'error') {
            $class = 'notification error png_bg';
		} 
		else if ($type == 'success') {
            $class = 'notification success png_bg';
        }
        else if ($type == 'information') {
            $class = 'notification information png_bg';
        }
        else if ($type == 'attention') {
            $class = 'notification attention png_bg';
        }
		
		$template = '<div class="' . $class . ' no-display">
                    <a href="#" class="close">
                        <img src="' . $imgLink . '"
                            title="'
                        . $this->view->translation('Close notification') .
                        '" alt="' . $this->view->translation('close') . '">
                    </a>
                    <div class="notification-message">' . $message . '</div>
			    </div>';
		return $template;
	}
}
?>