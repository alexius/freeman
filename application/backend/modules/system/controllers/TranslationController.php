<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class System_TranslationController extends Core_Controller_Start
{
	
	/**
     * Default service class name for current controller
     *
     * @var String
     */
	protected $_defaultServiceName = 'TranslationService';

    /**
     * @var TranslationService
     */
    protected $_service;

	public function indexAction()
	{
		if ($this->_request->isPost())
		{
			$this->_service->translations(
				$this->_request->getParams()
			);
		}
	}

	public function saveAction()
	{
		if ($this->_request->isPost())
		{
			$this->_service->save($this->_request->getParams());
		}
	}

	public function addAction()
	{
		if ($this->_request->isPost())
		{
            $external = $this->_request->getParam('external');
			$this->_service->add($this->_request->getPost());
		}
	}

}
