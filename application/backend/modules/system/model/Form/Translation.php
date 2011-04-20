<?php

/**
 * 
 * Role form class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Form_Translation extends Core_Form
{
	public function init()
	{
		$this->setAttrib('class','form');
		$this->setAttrib('id','translation');

		$conf = Zend_Registry::get('app_config');

		$this->addElement ('text', 'code', array (
			'label' => Zend_Registry::get('translation')->get('code'),
			'filters' => array ('StringTrim','StripTags' ),
			'required' => true,
			'validators' => array (
				array ('regex', true,
				   	array(
		            	'pattern'=> '/^[0-9a-z_]+$/i',
		                'messages'=>array(
		                	'regexNotMatch'=>Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
                                             ' \'"-,.'
		            	)
		           )
		        ),
				array ('StringLength', false, array (0, 500 ) )
			)
		));

		foreach ($conf['languages'] AS $lang => $val)
		{
			$this->addElement ('text', 'caption_' . $lang, array (
				'label' => $val,
				'required' => true,
				'filters' => array ('StringTrim'),
				'validators' => array (
					array ('regex', true,
						array(
							'pattern'=> '/^[0-9a-z\x80-\xFF³²\s\.\,"\'\-\(\)\$\<\>\/%]+$/i',
							'messages'=>array(
								'regexNotMatch'=>Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
												 ' \'"-,.'
							)
					   )
					),
					array ('StringLength', false, array (0, 500 ) )
				)
			));
		}

		$this->addElement ('text', 'fmodule', array (
			'label' => Zend_Registry::get('translation')->get('module'),
			'filters' => array ('StringTrim','StripTags' ),
			'required' => true,
			'validators' => array (
				array ('regex', true,
				   	array(
		            	'pattern'=> '/^[0-9a-z_]+$/i',
		                'messages'=>array(
		                	'regexNotMatch'=>Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
                                             ' \'"-,.'
		            	)
		           )
		        ),
				array ('StringLength', false, array (0, 500 ) )
			)
		));

		$this->addElement ('text', 'fresourse', array (
			'label' => Zend_Registry::get('translation')->get('resourse'),
			'required' => true,
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('regex', true,
				   	array(
		            	'pattern'=> '/^[0-9a-z_]+$/i',
		                'messages'=>array(
		                	'regexNotMatch'=>Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
                                             ' \'"-,.'
		            	)
		           )
		        ),
				array ('StringLength', false, array (0, 500 ) )
			)
		));

		$this->addElement ('text', 'faction', array (
			'label' => Zend_Registry::get('translation')->get('action'),
			'required' => true,  
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('regex', true,
				   	array(
		            	'pattern'=> '/^[0-9a-z_]+$/i',
		                'messages'=>array(
		                	'regexNotMatch'=>Zend_Registry::get('translation')->get('allow_letters_numbers_symbols') .
                                             ' \'"-,.'
		            	)
		           )
		        ),
				array ('StringLength', false, array (0, 500 ) )
			)
		));
	}
}
?>
