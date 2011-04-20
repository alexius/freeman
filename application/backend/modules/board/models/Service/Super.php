<?php
/**
 *  Super Service Class 
 */
class Service_Super
{
	 /**
 	 * Validator - initialized form object
 	 * @var object
 	 */
	protected $_validator = null;
	
	/**
 	 * Validator - initialized Mapper object
 	 * @var object
 	 */
	protected $_mapper = null;
	
	/**
 	 * Mapper class name
 	 * @var string
 	 */
	protected $_mapperName;
	
	/**
 	 * Form class name
 	 * @var string
 	 */
	protected $_formName;
	
	/**
 	 * Fields, used to pass to grid table
 	 * @var array
 	 */
	protected $_gridFields;
	protected $_slugtype;
    protected $_initForm = true;
    
	public function __construct()
	{
		if (isset($this->_formName) && $this->_initForm == true)
		{
			if (!isset($this->_validator)) 
            {
	            $class = 'Form_' . $this->_formName;
	            $form = new $class;
	            $this->_validator = $form;
	        }	
		}
		if (null != $this->_mapperName) 
        {
			$class = 'Mapper_' . $this->_mapperName;
            $this->setMapper(new $class);
        }
	}
	
	public function saveSlug($model, $type)
	{
		$slug = $this->getMapper()->fetchSlug($model->id, $type);
		if (!empty($slug))
        {
			if ($model->url != $slug['item_url'])
            {
				$model = $this->getMapper()->updateSlug($model, $type);
			}
		} 
        else 
        {
			$model = $this->getMapper()->insertSlug($model, $type);
		}
        return $model;
	}
	
	public function partialSave(array $data)
	{
       
        if (isset($data['id']))
        {
		    $model = $this->_mapper->fetchId($data['id']);
		    if (!$model)
		    {
			    return false;
		    } 
        }
        else
        {
            $model = $this->_mapper->getModel();
        }
        $obj_array = $model->toArray();		

		foreach($data as $key => $oj)
		{
			$obj_array[$key] = $oj;
		}
        $this->_validator->removeElement('image_loader');
		if ($this->_validator->isValid($obj_array))
		{                
			$model->populate($this->_validator->getValues());
			$model = $this->_mapper->objectSave($model);     
			if ($o = $model->getError())
            {    
				return $o;
			}
			else
            {
                if (!empty($this->_slugtype))
                {
                    $this->saveSlug($model, $this->_slugtype);
                } 
				return 'ok';
			}
		}
		else 
		{
            $errors = $this->_validator->getErrors();
            return Zend_Json::encode($errors);
		}
	}
	
	public function getForm($cid = null)
	{
        if (isset($this->_validator)) 
        {
        	if (null === $cid)
            {
            	return $this->_validator;	
        	} 
            else 
            {
        		if ($o = $this->_mapper->fetchId($cid))
                {
        			return $this->_validator->populate($o->toArray());
        		} 
                else 
                {
        			return  $this->_validator;
        		}        		
        	}
        }       	
	}
	
	public function setForm($form)
	{
        if (!empty($form)) 
        {
        	$form_class = 'Form_' . $form;
        	$form_obj = new $form_class;
        	$this->_validator = $form_obj;
        	return $this;
        }       	
	}
	
    public function getMapper()
    {
        return $this->_mapper;
    }
    
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    
	public function getGridData(array $filters)
	{                                   
		$total_rows = $this->getMapper()->countAll();
        if (empty($filters))
        {
            return false;
        }
		$rows = $this->_mapper->fetchForGrid($this->_gridFields, $filters);
        
        if ($rows)
        {			
			return Zend_Json::encode(array('grid_data' => $rows,
				'total_rows' => $total_rows));
		} 
        else 
        {
			return Zend_Json::encode (array ('error' => Errors::getError(111)));
		}
	}
	
    protected function deleteFileImage($path, $image)
    {
		if (!empty($image) && !empty($path))
		{
			$file =  $path . $image;
			if (file_exists($file))	
            {
				unlink($file);
			}		
		}
    }
    
	public function save(array $data)
	{
		if ($this->_validator->isValid($data))
		{
			$filtered_data = $this->_validator->getValues();
			
			$mname = $this->_mapper->getRowClass();
			$model = new $mname;
			$model->populate($filtered_data);
			$model = $this->_mapper->objectSave($model);

            $o = $model->getError();
         
			if ($o)
            {     
				return $o;
			}
            else 
            {
                if (!empty($this->_slugtype))
                {
                    $this->saveSlug($model, $this->_slugtype);
                }  
				$this->_validator->populate($model->toArray());
				return true;
			}
		}
		else 
		{
			return false;
		}
	}
	
}