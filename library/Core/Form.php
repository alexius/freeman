<?php

/**
 * Extended Form with new decorators and translated errors to russion lang
 *
 * @author      Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Form extends Zend_Form
{
    public  $_elementDecorators = array(
        array('ViewHelper'),
        array('Label'),

        array('Description',
               array("tag" => "small",
               )
         ),
        array('HtmlTagClear',
              array('tag' => 'p', 'class' => 'elements-wrapper')
        ),
        array('Errors', array('class' => 'errors-wrapper input-notification error png_bg')),
   	);

    
    /**
     * 
     * The form and elements decorators
     * @var array
     */
    public  $formDecorators = array(
         'FormElements',
         'Form'
    );

    /**
     * 
     * The constructor
     * Translates errors
     * Sets new decorators
     * Initializes form
     * 
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

		$errors = array(
	        'isEmpty' => Zend_Registry::get('translation')->get('isEmpty'),
	        'stringLengthTooShort' => Zend_Registry::get('translation')->get('stringLengthTooShort'),
	        'stringLengthTooLong'  => Zend_Registry::get('translation')->get('stringLengthTooLong'),
	        'notAlnum' => Zend_Registry::get('translation')->get('notAlnum'),
	        "badCaptcha" => Zend_Registry::get('translation')->get('badCaptcha'),
	        "emailAddressInvalid" => Zend_Registry::get('translation')->get('emailAddressInvalid'),
			"emailAddressInvalidFormat" => Zend_Registry::get('translation')->get('emailAddressInvalidFormat'),
			'fileExtensionFalse' => Zend_Registry::get('translation')->get('fileExtensionFalse'),
	    	'fileSizeTooBig' => Zend_Registry::get('translation')->get('fileSizeTooBig'),
			'hostnameInvalidLocalName' => Zend_Registry::get('translation')->get('hostnameInvalidLocalName'),		
			'hostnameInvalidHostname' => Zend_Registry::get('translation')->get('hostnameInvalidHostname'),
			
			'emailAddressInvalidHostname' => Zend_Registry::get('translation')->get('emailAddressInvalidHostname'),
			'hostnameInvalidHostname' => Zend_Registry::get('translation')->get('hostnameInvalidHostname1'),
			'hostnameLocalNameNotAllowed' => Zend_Registry::get('translation')->get('hostnameLocalNameNotAllowed'),
			'fileMimeTypeFalse' => Zend_Registry::get('translation')->get('fileMimeTypeFalse'),
			'hostnameUndecipherableTld' => Zend_Registry::get('translation')->get('hostnameUndecipherableTld'),
	        'notInt'  => Zend_Registry::get('translation')->get('notInt'),
			'dateFalseFormat' => Zend_Registry::get('translation')->get('dateFalseFormat'),
			'dateInvalidDate' => Zend_Registry::get('translation')->get('dateInvalidDate'),
		
			'fileUploadErrorNoFile' => Zend_Registry::get('translation')->get('fileUploadErrorNoFile'),
			'fileUploadErrorIniSize' => Zend_Registry::get('translation')->get('fileUploadErrorIniSize'),
			'fileMimeTypeNotDetected' => Zend_Registry::get('translation')->get('fileMimeTypeNotDetected'),
			'fileUploadErrorFormSize' => Zend_Registry::get('translation')->get('fileUploadErrorFormSize'),
		
			'notGreaterThan' => Zend_Registry::get('translation')->get('notGreaterThan'),
			'alnumStringEmpty' => Zend_Registry::get('translation')->get('alnumStringEmpty')
		
		);
	    
		$translate = new Zend_Translate('array',$errors,'ru_RU');	
		$this->setTranslator($translate);
		$this->addElementPrefixPath ( 'Core_Validate', 'Core/Validate', 'validate' );
		$this->addPrefixPath('Core_Form_Element', 'Core/Form/Element', 'element');
        $this->addPrefixPath('Core_Form_Decorator', 'Core/Form/Decorator', 'decorator');
		$this->setElementDecorators($this->_elementDecorators);
		$this->setDecorators($this->formDecorators);

		$this->init();

        
    }

	public function addSubmit()
	{
		$this->addElement ('submit', 'sub', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->sub->setLabel(Zend_Registry::get('translation')->get('accept'));
		$this->sub->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'div', 'class'=>'submit-group')),
		));
	}
}