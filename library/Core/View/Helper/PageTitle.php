<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_PageTitle
{

	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * 
	 * Return current module base link path
	 * @param int $link | if needed to return raw path
	 * @param String $name | link visual name
	 * @param String $id | link identifier 'id="a1"'
	 * @param Srting $classes | classes list
	 * @param bool $blank | open in new window 
	 */
	public function pageTitle()
    {
        $front = Zend_Controller_Front::getInstance();
        $curRes = $front->getRequest()->getControllerName();
        $curModel = $front->getRequest()->getModuleName();
        $curAct = $front->getRequest()->getActionName();

        $curSys = $curModel . ':' . $curRes;

        $acl = Zend_Registry::get('acl');

        foreach ($acl as $a)
        {
            if ($a['resourse_code'] == $curSys
                && $a['action'] == $curAct)
            {
                return '<h2>' . $this->view->translation($a['right_name']) . '</h2><hr>';
            }
        }
	}
}
?>