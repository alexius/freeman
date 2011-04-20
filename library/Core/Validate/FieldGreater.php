<?php
 /**
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
*/

require_once 'Zend/Validate/Abstract.php';

/**
 * @uses       ZExt_Validate_IdenticalField
 * @package    ZExt_Validate
 * @author     Sean P. O. MacCath-Moran
 * @email      zendcode@emanaton.com
 * @website    http://www.emanaton.com
 * @copyright  This work is licenced under a Attribution Non-commercial Share Alike Creative Commons licence
 * @license    http://creativecommons.org/licenses/by-nc-sa/3.0/us/
*/

class Core_Validate_FieldGreater extends Zend_Validate_Abstract {
  const NOT_MATCH = 'notMatch';
  const MISSING_FIELD_NAME = 'missingFieldName';
  const INVALID_FIELD_NAME = 'invalidFieldName';

  /**
   * @var array
  */
  protected $_messageTemplates = array(
    self::MISSING_FIELD_NAME  =>
      'DEVELOPMENT ERROR: Field name to match against was not provided.',
    self::INVALID_FIELD_NAME  =>
      'DEVELOPMENT ERROR: The field "%fieldName%" was not provided to match against.',
    self::NOT_MATCH =>
      'Поле должно быть больше чем %fieldValue%'
  );

  /**
   * @var array
  */
  protected $_messageVariables = array(
    'fieldName' => '_fieldName',
    'fieldTitle' => '_fieldTitle',
  	'fieldValue' => '_fieldValue',
  );

  /**
   * Name of the field as it appear in the $context array.
   *
   * @var string
   */
  protected $_fieldName;

  /**
   * Title of the field to display in an error message.
   *
   * If evaluates to false then will be set to $this->_fieldName.
   *
   * @var string
  */
  protected $_fieldTitle;

  /**
   * 
   * Enter description here ...
   * @var unknown_type
   */
  protected $_fieldValue;
  
  /**
   * Sets validator options
   *
   * @param  string $fieldName
   * @param  string $fieldTitle
   * @return void
  */
  public function __construct($fieldName, $fieldTitle = null) {
    $this->setFieldName($fieldName);
    $this->setFieldTitle($fieldTitle);
  }

  /**
   * Returns the field name.
   *
   * @return string
  */
  public function getFieldName() {
    return $this->_fieldName;
  }

  /**
   * Sets the field name.
   *
   * @param  string $fieldName
   * @return Zend_Validate_IdenticalField Provides a fluent interface
  */
  public function setFieldName($fieldName) {
    $this->_fieldName = $fieldName;
    return $this;
  }

  /**
   * Returns the field title.
   *
   * @return integer
  */
  public function getFieldTitle() {
    return $this->_fieldTitle;
  }

  /**
   * Sets the field title.
   *
   * @param  string:null $fieldTitle
   * @return Zend_Validate_IdenticalField Provides a fluent interface
  */
  public function setFieldTitle($fieldTitle = null) {
    $this->_fieldTitle = $fieldTitle ? $fieldTitle : $this->_fieldName;
    return $this;
  }

  /**
   * Defined by Zend_Validate_Interface
   *
   * Returns true if and only if a field name has been set, the field name is available in the
   * context, and the value of that field name matches the provided value.
   *
   * @param  string $value
   *
   * @return boolean
  */
  public function isValid($value, $context = null) {
    $this->_setValue($value);
    $field = $this->getFieldName();

    $this->_fieldValue = $context[$field];
    
    if (empty($field)) {
      $this->_error(self::MISSING_FIELD_NAME);
      return false;
    } elseif (!isset($context[$field])) {
      $this->_error(self::INVALID_FIELD_NAME);
      return false;
    } elseif (is_array($context)) {
      if ($value > $context[$field]) {
        return true;
      }
    } elseif (is_string($context) && ($value >= $context)) {
      return true;
    }
    $this->_error(self::NOT_MATCH);
    return false;
  }
}
