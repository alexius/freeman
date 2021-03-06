<?php

/**
 * Loading user's settings form class
 * @author Kagarlykskiy Aleksey
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Form_Settings extends Core_Form
{
	public function init()
	{
		$this->setAttrib('class','form');
		$this->setAttrib('id','profile');

		$this->addElement ('text', 'name', array (
            'label' => Zend_Registry::get('translation')->get('user_name'),
			'required' => true,
            'class' => 'medium-input text-input',
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (3, 55 ) ),
                array ('Alnum', false, array(true))
            )
        ));


		$this->addElement ('text', 'surname', array (
            'label' => Zend_Registry::get('translation')->get('surname'),
			'required' => true,
            'class' => 'medium-input text-input',
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (3, 55 ) ),
                array ('Alnum', false, array(true))
            )
        ));

		$this->addElement ('text', 'patronymic', array (
            'label' => Zend_Registry::get('translation')->get('patronymic'),
			'required' => true,
            'class' => 'medium-input text-input',
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (3, 55 ) ),
                array ('Alnum', false, array(true))
            )
        ));

        $user = Core_Model_User::getInstance();
        $this->addElement ('text', 'email', array (
            'label' => Zend_Registry::get('translation')->get('email'),
			'required' => true,
            'class' => 'medium-input text-input',
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array ( 'EmailAddress',
        		array('UniqueOther', false,
		        	array('users', 'email', 'user_id', $user->user_id)),
                array ('StringLength', false, array (5, 100 ) ),
            )
        ));

		$this->addElement ('select', 'language', array (
            'label' => Zend_Registry::get('translation')->get('language_selection'),
			'required' => true,
            'class' => 'medium-input text-input',
			'multioptions' => array('ua' => 'українська', 'ru' => 'русский', 'en' => 'english'),
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 2 ) ),
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