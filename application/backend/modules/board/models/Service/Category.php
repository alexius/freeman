<?php
class Service_Category extends Service_Super 
{
    protected $_mapperName = 'Categories';
    protected $_formName = 'Category';
    protected $_gridFields = array ('id', 'name', 'url','active');
    protected $_childs = array();
    protected $_categories = array();
    
    public function getForm($cid = null)
    {
        $settings = new Service_Settings();
        $sgroup = $settings->getMapper()->fetchSettingsGroup('category', 'global');
        
        if (isset($this->_validator)) 
        {
            $show_img = $sgroup['show_images']; 
            if ($show_img->__toString() == 0){
                $this->_validator->removeElement('image');    
                $this->_validator->removeElement('image_loader');    
            }
            
            if (null === $cid){
                $this->_validator->removeElement('delete');
                return     $this->_validator;
            } else {
                if ($o = $this->_mapper->fetchId($cid)){
                    return $this->_validator->populate($o->toArray());
                } else {
                    return  $this->_validator;
                }
                
            }
        }           
    }
    
    private function getChilds($parent_id)
    {
        $childs = array();
        $childs = $this->_mapper->getChilds($parent_id);
        
        if (!empty($childs))
        {
            $this->_childs = array_merge($this->_childs, $childs);
            foreach ($childs as $c)
            {
                if ($c['id'] != null)
                {
                    $this->getChilds($c['id']);     
                }
            }
            return $childs;
        }        
    }

    public function delete($id)
    {
        if (((int)$id) <= 0){
            return;    
        }

        $childs = array();
        $this->getChilds($id);
        $cat = $this->_mapper->fetchId($id);

        array_push($this->_childs, $cat->toArray());

        foreach ($this->_childs as $c)
        {
            if (!empty($c['image']))
            {
                $file = '../upload/images/' . $c['image'];
                if (file_exists($file))
                {
                    unlink($file);
                }    
                    
            }
        }    
        $this->_mapper->deleteCategories($this->_childs);
    }
    
    public function getProductCategories($id = null, $prod_id = null)
    {
        $cats = $this->_mapper->getProductsCategories($id, $prod_id);
        $tree = new Moo_Controller_Plugin_MenuBuilder();
        $cats = $tree->GetMenuForCurrentSite($cats);
        $html = $this->buildTree($cats, $prod_id);
        return $html;
    }
    
    private function buildTree($cats, $prod_id)
    {
        if (!empty($cats))
        {
            if( count($cats) >= 1 ) 
            { 
                $dirs = '';
                $dirs .= "<ul class=\"jqueryFileTree cats-tree\" style=\"display: none;\">";
                foreach( $cats as $cat ) 
                {
                    $open = 'collapsed';
                    $pcat = '';
                    if (array_key_exists('sub', $cat)){
                        $open = 'expanded';
                    }
                    if ($prod_id == $cat['product_id']){
                        $pcat = 'CHECKED';
                    }
                    
                    $dirs .= "<li class = 'directory " . $open . "'>";
                    $dirs .= '<input ' . $pcat . ' class = "open-tag-box" type = "checkbox" id = "s-' . $cat['id'] . '">';
                    $dirs .= "<div class = 'category' id=\"c". $cat['id'] 
                            . "\" style = 'padding: 0 0 0 15px; display:inline;'>
                            <a href=\"#\" rel=\"" . $cat['id'] . "\">" 
                            . ($cat['name']) . "</a></div>";
                            
                    if (array_key_exists('sub', $cat)){
                        $dirs .= $this->buildTree($cat['sub'], $prod_id);
                    }
                    $dirs .= "</li>";                        
                }
                $dirs .= "</ul>";    
            }
            return $dirs;
        }    
    }
    
    public function getCategoriesTree($id = null, $sel = null)
    {
        $cats = $this->_mapper->getCategoriesTree($id);
        if (!empty($cats))
        {
            if( count($cats) >= 1 ) 
            { 
                $dirs = '';
                $dirs .= "<ul class=\"jqueryFileTree cats-tree\" style=\"display: none;\">";
                foreach( $cats as $cat ) 
                {
                    $dirs .= "<li class = 'directory collapsed'>";
                    if ($sel !=  null){
                        $dirs .= '<input class = "open-tag" type = "checkbox" id = "s-' . $cat->id . '">';
                    } else {
                        $dirs .= "<a class = 'open-tag' href=\"#\" rel=\"". $cat->id . "\">
                        <span class='open-icon ui-icon'></span>
                        </a>";
                    }
                    $dirs .= "<div id=\"c". $cat->id . "\" class = 'category-folder'>
                                <a href=\"#\" rel=\"" 
                                    . $cat->id . "\">" 
                                    . ($cat->name) . "
                                </a>
                            </div></li>";
                        
                }
                $dirs .= "</ul>";    
            }
            return $dirs;
        }
    }
    
    public function save(array $data)
    {       
        $settings = new Service_Settings();
        $sgroup = $settings->getMapper()->fetchSettingsGroup('category', 'global');
        
        if (isset($this->_validator)) 
        {
            $show_img = $sgroup['show_images']; 
            if ($show_img->__toString() == 0){
                $this->_validator->removeElement('image');    
                $this->_validator->removeElement('image_loader');    
            }
        }     
        $model = new Category();                        
        if ($this->_validator->isValid($data))
        {
            $filtered_data = $this->_validator->getValues();
            $model->populate($filtered_data);
            
            if (isset($data['del_image']) && isset($model->id))
            {
                $this->deleteImage($model->id);
                $model->image = false;
            }
            else if (!isset($data['del_image']))
            {
                if ($model->image_loader)
                {
                    $model->uploadImage();
                }
            }

            if ($o = $model->getError())
            {
                $this->_validator->image_loader->addError($o);
                return $model;
            }
    
            $model = $this->_mapper->objectSave($model);
            $model = $this->saveSlug($model, 'catalog');  
            if ($o = $model->getError())
            {
                return $model;
            }
            
            if ($o = $model->getError()){
                return $model;
            } else {
                $this->_validator->populate($model->toArray());
                return $model;
            }
        }
        else 
        {
            return $model;
        }
    }
    
    private function deleteImage($id)
    {
        $image = $this->_mapper->fetchImage($id);
        $path = '../upload/images/';
        $this->deleteFileImage($path, $image);
    }
}