<?php
class CategoriesController extends Moo_Controller_Action 
{
    protected $_defaultServiceName = 'Service_Category';

    public function ajaxsubmitAction()
    {
        if ($this->getRequest()->isPost())
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();		
            $post = $this->_request->getPost(); 
            $response = array('error' => 'false', 'model_error' => 'false');
            $o = $this->_service->save($post);
            $form = $this->_service->getForm((int) $post['id']);
            if ($form->isErrors())
            {
                $response['error'] = 'true';
            }
            if ($o->getError())
            {
                $response['model_error'] = $o->getError(); 
            }
            $response['form'] = $form->render();      
            $this->ajaxResponse(Zend_Json::encode($response), 'text/html');          	
        }
    }

    public function changeparentAction()
    {
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $data = $this->_request->getPost(); 
            if (($o = $this->_service->partialSave($data)) == 'ok')
            {
                $categories_tree = $this->_service
                ->getCategoriesTree((int) $data['parent_id']);
                $this->ajaxResponse($categories_tree, 'text/html');
            }
            else
            {
                return $this->ajaxResponse($o, 'text/html'); 
            }
        }		
    }

    public function getcategorytreeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    
            $root = $this->_request->getParam('dir', 0);
            $sel = $this->_request->getParam('sel', 0);
            $categories_tree = $this->_service->getCategoriesTree((int) $root, $sel);		
            $this->ajaxResponse($categories_tree, 'text/html');
        }
    }

    public function getproductcategoriesAction()
    {
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $root = $this->_request->getParam('dir', 0);
            $prod = $this->_request->getParam('product_id', 0);
            $categories_tree = $this->_service->getProductCategories((int) $root, $prod);		
            $this->ajaxResponse($categories_tree, 'text/html');
        }
    }

    public function getformAction()
    {
        if ($this->getRequest()->isXmlHttpRequest())
        {			
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $id = (int) $this->_request->getParam('id');

            $form = $this->_service->getForm($id);
            $parent_id = $this->_request->getParam('parent_id');
            if ((int) $parent_id > 0 ){
                $form->parent_id->setValue($parent_id);
            }
            $this->ajaxResponse($form, 'text/html');
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
        $id = $this->_request->getParam('id');
        $form = $this->_service->getForm($id);
        $this->view->form = $form;
    }
}