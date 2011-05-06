<?php

/**
 *  Super Service Class 
 *  @author Fedor Petryk
 *  @package Core_Service
 *  @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Service_Super
{
	 /**
 	 * Validator - initialized form object
 	 * @var Core_Form
 	 */
	protected $_validator = null;
	
	/**
 	 * initialized Mapper object
 	 * @var Core_Mapper_Super
 	 */
	protected $_mapper = null;
	
	/**
 	 * Mapper class name
 	 * @var string
 	 */
	protected $_mapperName = 'Core_Mapper_Super';
	
	/**
 	 * Validator (Form) class name
 	 * @var string
 	 */
	protected $_validatorName;
	
	/**
	 * Error, generated in action processing by service class
	 * or by mapper class
	 * @var String
	 */
	protected $_error;

	/**
	 * Message, generated in action processing by service class
	 * @var String
	 */
	protected $_message;

	/**
	 * Message, generated in processing form by service class
	 * @var array
	 */
	protected $_formMessages;

	/**
	 * Encoded array of data to be returned
	 * @var String
	 */
	protected $_jsonData = null;

	public function getJsonData()
	{
		if ($this->_jsonData != null)
		{
			return $this->_jsonData;
		}

		return false;
	}


	/**
 	 * Constructor inializes validator and mapper instances
 	 * @var array
 	 */
	public function __construct()
	{
		if (null != $this->_validatorName)
		{
	    	$class = $this->_validatorName;
	       	$form = $this->setValidator(new $class);
		}
		
		if (null != $this->_mapperName) 
        {
			$class = $this->_mapperName;
            $this->setMapper(new $class);
        }
	}
	
	/**
	 * 
	 * Gets validator
	 */
	public function getValidator()
	{
        if (isset($this->_validator)) 
        {
        	return  $this->_validator;
        }       	
	}
	
	/**
	 * 
	 * Sets new validator by form nams
	 * @param String $name
	 */
	public function setValidator($name)
	{
        $this->_validator = new $name;
        return $this;    	
	}
	
	/**
	 * 
	 * returns mapper
	 * @return Core_Mapper_Super
	 */
    public function getMapper()
    {
        return $this->_mapper;
    }
    
    /**
     * 
     * Sets new mapper
     * @param $mapper
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    
    /**
     * 
     * Returns error
     * @return String
     */
    public function getError()
    {
    	return $this->_error;	
    }
    
    /**
     * 
     * Sets error
     * @param String | $error
     */
	public function setError($error)
    {
    	$this->_error = $error; 	
    }
    
    /**
     * 
     * Returns message
     * @return String
     */
    public function getMessage()
    {
    	return $this->_message;	
    }
    
    /**
     * 
     * Sets message
     * @param String | $error
     */
	public function setMessage($message)
    {
    	$this->_message = $message; 	
    }

    /**
     * 
     * Returns form messages
     * @return String
     */
    public function getFormMessages()
    {
    	return $this->_formMessages;	
    }
    
    /**
     * 
     * Sets form messages
     * @param String | $error
     */
	public function setFormMessages($message)
    {
    	$this->_formMessages = $message; 	
    }
    
    /**
     * 
     * Saves domain object to db
     * @uses Core_Mapper_Super
     * @param array $data
     */
	public function save($data)
	{
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
				
		 			 
		$model = $this->_mapper->objectSave($model);
			                                        
		if ($this->_validator != null)
        {
			$this->_validator->populate($model->toArray());
		}
        $this->setMessage(Core_Model_Messages::getMessage(1));
		return $model;
	}
	
	/**
	 * 
	 * Creates new document from validator data
	 */
	public function uploadDocument()
	{
		$document = new Core_Model_Document();
		$document->create($this->_validator);
		
		if ($document->getError())
		{
			$this->setError($document->getError());
			return false;
		}
		$res = $this->_mapper->insertDocument($document);
		if (!$res)
		{
			$this->setError(Errors::getError(325));
			return false;
		}
		return $res;	
	}


		
	/**
	 * 
	 * Enter description here ...
	 * @param Tender $tender | current tender
	 * @param int $pid | tender plan version
	 */
	public function changeTenderVesion(Tender $tender, $planId = null)
	{
		$oldTender = new Tender($tender->toArray()); 
		if ($planId == null) {
			$planId = $this->_mapper->getTenderPlanId($tender->id);
		}
		
		if (empty($planId))
		{
			$this->setError(Errors::getError(306));
			return false;	
		}
		
		// changin version id
		$this->_mapper->setDbTable('DbTable_Tenders');
		$newId = $this->_mapper->getLastKeyVaue('id');
		$tender->id = $newId + 1;	
		$tender->changedDate();
		
		// saving
		$this->_mapper->objectSave($tender);
		
		if ($tender->getError())
		{
			$this->setError($tender->getError());
			return false;	
		}
		
		// 	deleting current relation plan - tender
		$this->_mapper->deleteTenderVersionFromPlan($oldTender, $planId);
		
		// adding new relation plan - tender
		$this->_mapper->addTenderVersionToPlan(null, $tender, $planId);
		return $tender;
	}
	
	/**
	 * 
	 * Checks if tender exists and returns it
	 * @param int $tender_id
	 * @return Tender
	 */
	public function getTender($tender_id)
	{
		$tender = $this->_mapper->getTender($tender_id);
		
		if (empty($tender))
		{
			$this->setError(Errors::getError(312));
			return false;
		}
		return $tender;
	}
	
	/**
	 * 
	 */
	public function changeWorker($id)
	{
		$workerOld = new Core_Model_Worker();
		$this->_mapper->saveTenderWorker($workerOld, $id);
		$workerOld->setEndDate();
		$workerOld->active = 0; 
		$this->_mapper->saveTenderWorker($workerOld);
		return true;		
	}
	
	/**
	 * 
	 */
	public function createWorker($id, $tender, $inviter)
	{
		$worker = new Core_Model_Worker();
		$this->_mapper->createNewWorker($worker, $id);
		$worker->tender_id = $tender->tender_id;
		$worker->setStartDate();
		$worker->active = 1;
		$worker->inviter_id = $inviter;
		$this->_mapper->saveTenderWorker($worker);
		return $worker;
	}

	public function sendNotification($template, $params, $mailTo)
	{
		$mail = new Core_Mailer($template, $params, 1);
		$mail->addTo($mailTo);
		$mail->send();
	}
}