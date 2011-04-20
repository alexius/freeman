<?php
class SettingsController extends Moo_Controller_Action 
{
	protected $_defaultServiceName = 'Service_Settings';
    
    public function currencyAction()
    {
               
    }
    
    public function frontendAction()
    {
        if ($this->_service != null)
        {     
            if ($this->getRequest()->isXmlHttpRequest())
            { 
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();                    
                $filters = $this->_request->getPost();
                $filters['vision'] = 'frontend2';
                $filters['filters'] = true;
                $grid_data = $this->_service->getGridData($filters);
                $this->ajaxResponse($grid_data, 'application/json');
            }    
        }           
    }
    
    public function backendAction()
    {              
        if ($this->_service != null)
        {
            if ($this->getRequest()->isXmlHttpRequest())
            {
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();                    
                $filters = $this->_request->getPost();
                $filters['vision'] = 'backend';
                $filters['filters'] = true;  
                $grid_data = $this->_service->getGridData($filters);
                $this->ajaxResponse($grid_data, 'application/json');
            }    
        }           
    }
}