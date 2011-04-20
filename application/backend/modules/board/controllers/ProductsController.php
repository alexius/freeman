<?php
class ProductsController extends Moo_Controller_Action 
{
	protected $_defaultServiceName = 'Service_Product';
	
    public function indexAction()
    {
        if ($this->_service != null)
        {
            if ($this->getRequest()->isXmlHttpRequest())
            {
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();                    
                $filters = $this->_request->getPost();
                $grid_data = $this->_service->getGridData($filters);
                $this->ajaxResponse($grid_data, 'application/json');
            }   
        }
    }    
    
	public function editAction()
	{
		$id = $this->_request->getParam('id');
		$form = $this->_service->getForm($id);	
		$this->view->form = $form;
		$this->view->images = $this->_service->getImages($id);
		$this->_helper->layout->setLayout('twocolumns');
	}

	public function uploadimagesAction() 
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		echo ( $this->_service->uploadImages() );
	}	
}