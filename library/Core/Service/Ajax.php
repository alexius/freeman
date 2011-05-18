<?php

/**
 *  Super Ajax Service Class
 *  @author Fedor Petryk
 *  @package Core_Service
 *  @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Service_Ajax extends Core_Service_Super
{
    /**
     * Module identifier for slugs saving
     * @var string
     */
     protected $_slugtype = null;

	/**
	 * array of data to beencoded to JSON and returned
	 * @var array
	 */
	protected $_jsonData = null;

    /**
     * @var  Core_Mapper_Ajax
     */
    protected $_mapper;

    public function getRow($id)
    {
        $model = $this->_mapper->fetchById($id);
        if (empty($model))
        {
            $this->setError(Core_Model_Errors::getError('no_data'));
        }
        $this->setJsonData(
            array('formData' => $model->toArray())
        );
        $this->setMessage(Core_Model_Messages::getMessage('data_get'));
    }

        /**
     *
     * Saves domain object to db
     * @uses Core_Mapper_Super
     * @param array $data
     */
	public function save($data)
	{
        if ($this->_validator == null) {
			$this->setError(Core_Model_Errors::getError('no_validator'));
            return false;
		}

        if (!$this->_validator->isValid($data))
        {
            $this->setError(Core_Model_Errors::getError(300));
            $this->setFormMessages(
                $this->_validator->getMessages()
            );
            return false;
        }
        
		if (is_array($data))
		{
			$mname = $this->_mapper->getRowClass();
			$model = new $mname;
			$model->populate($data);
		}

		if (is_object($data))
		{
			$model = $data;
		}

		if ($this->_validator != null) {
			$filtered_data = $this->_validator->getValues();
			$model->populate($filtered_data);
		}

        if (!empty($this->_slugtype))
        {
            $slugUrlExists = $this->checkSlugUrl($model);
            if ($slugUrlExists == true){
                return false;
            }
        }

		$model = $this->_mapper->objectSave($model);

        if (!empty($this->_slugtype))
        {
            $this->saveSlug($model, $this->_slugtype);
        }

        $primaryKey = $this->_mapper->getDbTable()->getPrimary();

        if (empty($data[$primaryKey[1]]))
        {
            $this->setJsonData(
                array ('formData' =>
                    array(
                        'primaryKey' => $primaryKey[1],
                        'value' => $model->$primaryKey[1]
                    )
                )
            );
            $this->setMessage(Core_Model_Messages::getMessage(2));
        }
        else
        {
            $this->setMessage(Core_Model_Messages::getMessage(1));
        }
        
		if ($this->_validator != null)
        {
			$this->_validator->populate($model->toArray());
		}

		return $model;
	}
    
	public function getJsonData()
	{
		if ($this->_jsonData != null)
		{
			return $this->_jsonData;
		}

		return false;
	}

    public function setJsonData($json)
	{
	    $this->_jsonData = $json;
	}

    public function getGridData(array $filters)
	{
		$total_rows = $this->getMapper()->countAll();
        if (empty($filters))
        {
            return false;
        }
		$rows = $this->_mapper->fetchForGrid($this->_gridFields, $filters);

        if (!empty($rows))
        {
            $this->setMessage(Core_Model_Messages::getMessage('data_get'));
			$this->setJsonData(array(
                'grid_data' => $rows,
				'total_rows' => $total_rows
            ));
		}
        else
        {
            $this->setError(Core_Model_Errors::getError('302'));
			//$this->setJsonData (array ('error' => Core_Model_Errors::getError(111)));
		}
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

		if ($this->_validator->isValid($obj_array))
		{
			$model->populate($this->_validator->getValues());
            if (!empty($this->_slugtype))
            {
                $slugUrlExists = $this->checkSlugUrl($model);
                if ($slugUrlExists == true){
                    return false;
                }
            }

			$model = $this->_mapper->objectSave($model);
			if ($o = $model->getError())
            {
                $this->setError(Core_Model_Errors::getError($o));
                return false;
			}
			else
            {
                if (!empty($this->_slugtype))
                {
                    $this->saveSlug($model, $this->_slugtype);
                } 
			}
            $this->setMessage(Core_Model_Messages::getMessage(1));
            return true;
		}
		else
		{
            $this->setError(Core_Model_Errors::getError(300));
            $this->setFormMessages(
                $this->_validator->getMessages()
            );
            return false;
		}
	}

    public function saveSlug($model, $module)
	{
		$slug = $this->getMapper()->fetchSlug($model->id, $module);
		if (!empty($slug))
        {
			if ($model->url != $slug['item_url'])
            {
                $slugUrlExists = $this->checkSlugUrl($model);
                if ($slugUrlExists == true){
                    return false;
                }
				$model = $this->getMapper()->updateSlug($model, $module);
			}
		}
        else
        {
            $slugUrlExists = $this->checkSlugUrl($model);
            if ($slugUrlExists == true){
                return false;
            }
			$model = $this->getMapper()->insertSlug($model, $module);
		}
        return $model;
	}

    public function delete($id)
    {
        $this->_mapper->delete($id, $this->_slugtype);
        $this->setMessage(Core_Model_Messages::getMessage('deleted'));
    }

    protected function checkSlugUrl($model)
    {
        $urlExist = $this->_mapper->fetchSlugByUrl($model->url, $model->id);
        if (!empty($urlExist))
        {
            $this->setError(Core_Model_Errors::getError(300));
            $this->_validator->url->addError(
                Core_Model_Errors::getError('url_exists')
            );
            $this->setFormMessages(
                $this->_validator->getMessages()
            );
            return true;
        }
        return false;
    }

}