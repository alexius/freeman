<?php

/**
 * 
 * Role form class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Form_Role extends Core_Form
{
	public function init()
	{
		$this->setAttrib('class','form');
		$this->setAttrib('id','role-add');

		
		$this->addElement ('hidden', 'default_module', array (       
			'filters' => array ('StringTrim','StripTags' ),
		   	'required' => true,   
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));
		
		$this->addElement ('hidden', 'role_id', array (       
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));
        
        $this->addElement ('text', 'role_name', array (
            'label' => Zend_Registry::get('translation')->get('title'),
            'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
        		array('Unique', false, array('roles', 'role_name', 'role_id', $this)),
                array ('StringLength', false, array (1, 165 ) ),
            )
        )); 

        $this->addElement ('text', 'role_code', array (
            'label' => Zend_Registry::get('translation')->get('code'),
            'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
        		array('Unique', false, array('roles', 'role_code', 'role_id', $this)),
                array ('StringLength', false, array (1, 165 ) ),
            )
        ));  

		$this->addElement ('select', 'active', array (
			'label' => Zend_Registry::get('translation')->get('just_activated'),
			'multioptions' => array(0=>Zend_Registry::get('translation')->get('no'),
                                    1 =>Zend_Registry::get('translation')->get('yes')),
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));
		

	}
}
?>
