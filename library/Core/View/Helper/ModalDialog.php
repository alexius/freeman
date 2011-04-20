<?php

/**
 * Modal dialog cunstructer
 *
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_ModalDialog extends Zend_View_Helper_Abstract 
{
	public function setView(Zend_View_Interface $view)
    {
		$this->view = $view;
	}
	/**
	 * 
	 * Build dialog div
	 * @param String $body | dialog text
	 * @param String $id | dialog id
	 * @param String $title | dialog title
	 * @param String $class | dialog class
	 * @return String
	 */
	public function modalDialog($body, $id = 'dialog', $title = 'Сообщение', $class = '') 
	{
		$dialog = '<div id="' . $id . '" 
			title="' . $title . '" 
			class="modal-dialogue ' . $class. '">';

        $dialog .= $this->view->notification(null, 'error');
        $dialog .= $this->view->notification(null, 'success');

		$dialog .= $body;
		$dialog .= '</div>';
		return $dialog;			
	}	
}