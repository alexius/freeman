<?php

/**
 * 
 * Default document form form
 * 
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 *
 */
class Core_Form_Document extends Core_Form
{
	public function init()
	{
		$this->addBasicElements();
		$this->addSubmit();
	}
	
	protected function addBasicElements()
	{
		$this->setAttrib('class','commonform');
		$this->setAttrib ( 'enctype', 'multipart/form-data' );

		$this->addElement ('textarea', 'document_name', array (
            'label' => Zend_Registry::get('translation')->get('name_document'),
			'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 255 ) ),
            )
        )); 
        
        $this->addElement ('textarea', 'document_description', array (
            'label' => Zend_Registry::get('translation')->get('doc_description'),
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 255 ) ),
            )
        )); 
        
		$this->addElement ( 'file', 'document', array (
			'description' => Zend_Registry::get('translation')->get('available_formats') .' .jpg, .png, .gif, .pdf.<br>&nbsp;',
			'label' => Zend_Registry::get('translation')->get('scan_copy'),
			'required' => true,   
			'validators' => array(
				array('Extension', false, 'jpeg,jpg,png,gif,pdf'),
				array('Size', false, 16777216),
				array('Count', false, 1),
				//array('ImageSize', false, array(100,100,100,100)),
				array('MimeType', false, 
					array('image/jpeg','image/pjpeg','image/png',
							'image/gif','application/pdf',
					       	'application/x-pdf', 'application/acrobat',
							'applications/vnd.pdf', 'text/pdf', 'text/x-pdf'
					)
				)
			),
			'decorators' => array(
				'File',
				array ('Description', array ('escape' => false ) ),
				'Errors',
				array ('HtmlTag', array ('tag' => 'dd' ) ),
				array ('Label', array ('tag' => 'dt' ) )
			)
		));
		

	}
	

}
?>