<?php

/**
 * 
 * Custom unique field validation against non current
 * @author Fedor Petry
 *
 */
class Core_Validate_UniqueOther extends Zend_Validate_Abstract
{
    const USER_EXISTS = 'mailExists';

    /**
     * 
     * Message error
     * @var String
     */
    protected $_messageTemplates = array(
        self::USER_EXISTS => 'Значение "%value%" уже существует в системе',
    );

	protected $_uidValue;
	
	/**
	 * 
	 * Field to check to
	 * @var String
	 */
	protected $_field;
	
	/**
	 * 
	 * Table to look where
	 * @var String
	 */
	protected $_table;
	
	/**
	 * Id of the table to check if object exists againts other rows
	 */
	protected $_uid = null;
	
	/**
	 * 
	 * The validator element constrictur
	 * @param table name String | $table
	 * @param field name String | $field
	 * @param Form view $form
	 */
    public function __construct($table, $field, $id, $value)
    {
        $this->_field = $field;
        $this->_table = $table;
        $this->_uid =  $id;
       	$this->_uidValue = $value;       	
    }

    /**
     * (non-PHPdoc)
     * @see Zend/Validate/Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
    	$rid = null;
        $this->_setValue($value);
        $service = new Core_Service_Super();
        $unique = $service->getMapper()->checkUnique(
      		$this->_table, $this->_field, 
      		$value,  $this->_uidValue, $this->_uid
      	);  
      		
     		  
   		if ($unique == true) {
           	return true;
       	}

       	$this->_error(self::USER_EXISTS);
        return false;
    }
}
