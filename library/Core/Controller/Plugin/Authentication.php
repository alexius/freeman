<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Auth */
require_once 'Zend/Auth.php';

/**
 * Front Controller Plugin
 *
 * @author     Petryk Fedor
 * @uses       Zend_Controller_Plugin_Abstract
 * @package    Core_Controller_Plugin
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Controller_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{
    /**
     * Predispatch
     * Checks if the user authenticated
     *
     * @return void
     **/
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	// if no auth - redirect to login page
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_response->setRedirect('/');
        }
    }

}