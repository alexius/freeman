<?php

/**
 * 
 * User role form class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Form_UserRole extends Core_Form
{
	public function init()
	{
		$this->setAttrib('class','form');
		$this->setAttrib('id','user_role');

		$this->addElement ('select', 'user_id', array (
			'label' => Zend_Registry::get('translation')->get('user'),
		  	'required' => true,          
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));
		$this->user_id->setRegisterInArrayValidator(false);     
		
		$this->addElement ('select', 'role_id', array ( 
			'label' => Zend_Registry::get('translation')->get('role'),   
		  	'required' => true,      
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));	
		$this->role_id->setRegisterInArrayValidator(false);     


		
		$this->addElement ('submit', 'sub', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->sub->setLabel(Zend_Registry::get('translation')->get('save'));
		
		$this->sub->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'div', 'class'=>'submit-group')),
		));
	}
}
?>
