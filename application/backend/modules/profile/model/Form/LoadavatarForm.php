<?php

/**
 * Loading user's avatar form class
 * @author Kagarlykskiy Aleksey
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Form_LoadavatarForm extends Core_Form
{
	public function init()
	{
		$this->addElement ( 'file', 'document', array (
			'description' => Zend_Registry::get('translation')->get('available_formats') .
                            ' .jpg, .png, .gif, .pdf,<br> ' .
                    Zend_Registry::get('translation')->get('size_min') .' 120х90.<br>&nbsp;',
			'label' => Zend_Registry::get('translation')->get('path_to_photo'),
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
		
		$this->addElement ('submit', 'sub', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->sub->setLabel(Zend_Registry::get('translation')->get('change'));
		$this->sub->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'div', 'class'=>'submit-group')),
		));				
	}
}
