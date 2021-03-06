<?php

class Core_Controller_DefaultActions extends Core_Controller_Start
{
     /**
     * The service layer object, generaly used in child controllers
     * @var Core_Service_Ajax
     */
	protected $_service;

	public function indexAction()
	{
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $filters = $this->_request->getPost();
            $this->_service->getGridData($filters);
        }
	}
    
    public function editAction()
    {
        if ($this->_request->isPost())
        {    
            $post = $this->_request->getPost(); 
            if (($o = $this->_service->save($post)) === true){
                
            } 
            else {
                $this->view->message = $o;
            }            
        }

        if ($this->_request->isGet())
        {
            $id = (int) $this->_request->getParam('id');
            $this->_service->getRow($id);
        }
    }
	
	public function inlinesaveAction()
	{
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();		
			
			$post = $this->_request->getPost();
			$ans = $this->_service->partialSave($post);
		}
	}
	
	public function ajaxsubmitAction()
	{	
		if ($this->getRequest()->isXmlHttpRequest() ||
            $this->getRequest()->isPost())
		{
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();			
			
			$post = $this->_request->getPost(); 
			$response = array();
			$o = $this->_service->save($post);
			if ($o)
            {
				$form = $this->_service->getForm((int) $post['id']);
			} 
            else 
            {
				$form = $this->_service->getForm($post);
			}
			$response['error'] = 'false';
		
			if ($form->isErrors())
            {
				$response['error'] = 'true';
            }

			$response['form'] = $form->render();
			$this->ajaxResponse(Zend_Json::encode($response), 'text/html');
			
		}
	}
	
	public function submitredirectAction()
	{
		if ($this->getRequest()->isXmlHttpRequest())
		{	
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();		
			$post = $this->_request->getPost();
			
			if (($o = $this->_service->save($post)) === true){
				$form = $this->_service->getForm((int) $post['id']);
				$this->ajaxResponse('ok', 'text/html');
			} 
			else {
				$form = $this->_service->getForm((int) $post['id']);
				$this->ajaxResponse($form, 'text/html');
			}			
		}
	}
	
    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $id = (int) $this->_request->getParam('id');
        $this->_service->delete($id);
    }

    public function imagesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_ajaxViewEnabled = true;
    }
}