<?php
class Form_Page extends Moo_Form
{
	public function init()
	{
		$this->setAttrib('class','commonform');
		$this->setDecorators(array(
        	array('ViewScript' , 
            array('viewScript' =>'pages/_page.phtml'))
    	));

	    $this->addElement ('hidden', 'id', array (       
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));
        
        $this->addElement ('text', 'name', array (
            'class' => 'field text large',
            'label' => 'Название',
            'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 165 ) ),
            )
        ));        
        

		$this->addElement ('text', 'title', array (
			'class' => 'fiefield text large',
			'label' => 'Мета заголовок',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 165 ) ),
			)
		));		
		
		$this->addElement ('textarea', 'text', array (
			'class' => 'tex_markitup',
			'label' => 'Текст',
			'filters' => array ('StringTrim'),
		));		
		
		$this->addElement ( 'textarea', 'description', array (
			'class' => 'field textarea small',
			'label' => 'Мета Описание',
			'filters' => array ('StringTrim', 'StripTags'),
		));
		
		$this->addElement ('textarea', 'keywords', array (
			'class' => 'field textarea small',
			'label' => 'Мета Ключевые слова',
			'filters' => array ('StringTrim', 'StripTags'),
		));	
		
		$this->addElement ('text', 'url', array (
			'class' => 'field text large',
            'required' => true,   
			'label' => 'Адрес в браузере',
		//	'validators' => array ('alnum'
		//	),
			'filters' => array ('StringTrim', 'StripTags'),
		));	
				
		$this->addElement ('checkbox', 'active', array (
			'class' => 'field checkbox',
			'label' => 'Показывать',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));
	}
}
?>
