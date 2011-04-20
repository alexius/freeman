<?php
class AjaxController extends Moo_Controller_Action 
{
	protected $_defaultServiceName = 'Service_Ajax';
	
	public function getcitiesAction()
	{
		$id = $this->_request->getParam('country_id');	
		$html = $this->_service->getCities($id);
		$this->ajaxResponse($html, 'text/html');
	}
    
    public function imagesAction()
    {
        $this->_helper->layout->disableLayout();
    }
    
    public function previewAction()
    {    
        $this->_helper->layout->disableLayout();  
        $this->_helper->viewRenderer->initView();
        $data = $this->_request->getParam('data');
        $this->view->data = $data;    
    }

}