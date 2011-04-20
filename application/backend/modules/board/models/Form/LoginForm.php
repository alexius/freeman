<?php

class Form_LoginForm extends Zend_Form
{	public  $elementDecorators = array(
    	'ViewHelper',
        'Errors',
        array(array('data'  => 'HtmlTag'), array('class' => 'element')),
        array('Label', array('class' => 'desc')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li')),
   	);

    public function init()
	{
		$this->setAttrib('class','forms');        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Логин:')
	        ->setRequired(true)
	        ->setAttrib('id', '')
	        ->setAttrib('class', 'field text full')
	        ->addFilter('StripTags')
	        ->addFilter('StringTrim')
	        ->addValidator('NotEmpty')
	        ->addValidator('Alnum');
        $login->setDecorators($this->elementDecorators);

        $pass = new Zend_Form_Element_Password('password');
		$pass->setLabel('Пароль:')
	        ->setRequired(true)
	        ->setAttrib('class', 'field text full')
	        ->addFilter('StripTags')
	        ->addFilter('StringTrim')
	        ->addValidator('NotEmpty')
	        ->addValidator('Alnum');
        $pass->setDecorators($this->elementDecorators);

        $this->addElements(array($login, $pass));
    }

    public  function loadDefaultDecorators()
    {
    	$this->setDecorators(array(
        	'FormElements',
            array('HtmlTag', array('tag' => 'ul'),
            	array('class' => 'forms')),
            'Form',
        ));
    }

}