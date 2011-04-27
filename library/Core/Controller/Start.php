<?php

/**
 * Application start controller
 *
 * @author      Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Controller_Start extends Zend_Controller_Action
{

	/**
     * Default service class name
     *
     * @var String
     */
    protected $_defaultServiceName;

   	/**
     * The service layer object, generaly used in child controllers
     *
     * @var Core_Service_Super
     */
	protected $_service;

	/**
     * Initializing Default Service
     *	@throws Exception
     */


	/**
	 * Flag indicates that actions are permitted
	 * @var boolean
	 */
	protected $_permited = true;

	/**
	 * if true
	 * Enables returning html response to ajax request
	 * and disables service response
	 * @var bool
	 */
	protected $_ajaxViewEnabled = false;

    public function preDispatch()
    {
        $cookieVal = $this->getRequest()->getCookie('hidden');
        $this->view->hidden = $cookieVal;
    }

	public function postDispatch ()
	{
	    if ($this->getRequest()->isXmlHttpRequest())
		{
    		$this->_helper->layout->disableLayout();

			if ($this->_ajaxViewEnabled == false){
				$this->_helper->viewRenderer->setNoRender();
        		$this->serviceResponse();
			}
    	}
	}

	public function init()
	{
		if ($this->_defaultServiceName != null){
			$this->_service = new $this->_defaultServiceName;
			$this->view->service = $this->_service;
		} else {
			throw new Exception(Core_Model_Errors::getError(100));
		}
	}

	/**
     * Default ajax response method
     *
     *	@param Json or Html String|$data
     *	@param Type of the content String|$type
     */
	protected function ajaxResponse($data, $type)
	{
		$this->getResponse()
				->setHeader("Cache-Control", "no-cache, must-revalidate")
				->setHeader("Pragma", "no-cache")
				->setHeader("Content-type", "" . $type . ";charset=utf-8")
				->setBody($data);
	}

	/**
	 * Function for returning json response via ajax with specific structure
	 */
	protected function serviceResponse($type = 'application/json')
	{
		if ($this->_service == null)
		{
			throw new Exception(Core_Model_Errors::getError(100));
			return;
		}

	    if ($this->_service->getError()){
    		$response = array(
    			'error_message' => $this->_service->getError(),
    			'error' => 'true'
    		);
    	} else {
    		$response = array(
    			'error' => 'false',
    			'message' => $this->_service->getMessage()
    		);
    	}

    	if ($this->_service->getFormMessages()){
    		$response['formMessages'] = $this->_service->getFormMessages();
    	}

		if ($this->_service->getJsonData() == true){
			$response['data'] = $this->_service->getJsonData();
		}

    	$data = Zend_Json::encode($response);
    	$this->ajaxResponse($data, $type);
	}
}