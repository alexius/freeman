<?php

/**
 * 
 * The login form for authentication
 * @author Fedor Petryk
 *
 */
class Form_LoginForm extends Core_Form
{


    public function init()
	{
		$this->setAttrib('class','forms');

        $this->addElement ('text', 'login', array (
			'label' => Zend_Registry::get('translation')->get('login'),
		  	'required' => true,
            'class' => 'text-input',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('NotEmpty', 'Alnum',
				array ('StringLength', false, array (1, 10 ) )
			)
		));

       $this->addElement ('password', 'password', array (
			'label' => Zend_Registry::get('translation')->get('passwd'),
		  	'required' => true,
            'class' => 'text-input',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('NotEmpty', 'Alnum',
				array ('StringLength', false, array (1, 10 ) )
			)
		));

       $this->addElement ('submit', 'sub', array (
			'label' => Zend_Registry::get('translation')->get('enter'),
            'class' => 'button'
		));

        $this->sub->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'p')),
		));
    }
}