<?php
class Form_Product extends Moo_Form
{
	public function init()
	{
		$this->setAttrib('class','commonform');
		$this->setDecorators(array(
        	array('ViewScript' , array('viewScript' =>'products/_product.phtml'))
    	));

	    $this->addElement ('hidden', 'id', array (
    		'id' => 'id',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));

        $manuf = new Service_Manuf;
        $manuf = $manuf->getMapper()->getManufs();
        
		$this->addElement ('text', 'name', array (
			'class' => 'field text large',
			'label' => 'Название',
			'required' => true,
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 100 ) ),
			)
		));		
		
		$this->addElement ('text', 'model', array (
			'class' => 'field text large',
			'label' => 'Модель',
			'required' => true,
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 100 ) ),
			)
		));	
		
		$this->addElement ('select', 'manufacturer', array (
			'class' => 'field select large',
			'label' => 'Производитель',
            'required' => true,    
            'multioptions' => $manuf,
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 5 ) ),
			)
		));	
        $this->manufacturer->setRegisterInArrayValidator(false);     
		
		$this->addElement ('checkbox', 'status', array (
			'class' => 'field checkbox',
			'label' => 'Показывать',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));

                
        $this->addElement ('checkbox', 'show_mainpage', array (
            'class' => 'field checkbox',
            'label' => 'Показывать на главной',
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array ('int',
                array ('StringLength', false, array (0, 1) )
            )
        ));
        
		// TODO regexp pattern validation form a-z0-9 and "_"
		$this->addElement ('text', 'url', array (
			'class' => 'field text large',
			'label' => 'Адрес в браузере',
			//'validators' => array (			),
			'filters' => array ('StringTrim', 'StripTags'),
		));	
				
		$this->addElement ('text', 'date_add', array (
			'class' => 'field text large',
			'label' => 'Дата',
			'filters' => array ('StringTrim', 'StripTags'),
		));	
		
		$this->addElement ('textarea', 'catalog_preview', array (
			'class' => 'tex_markitup',
			'label' => 'Дополнительное описание в товаре',
			'filters' => array ('StringTrim'),
		));	
        
        $this->addElement ('textarea', 'short_desc', array (
            'class' => 'tex_markitup',
            'label' => 'Краткое описание',
            'filters' => array ('StringTrim'),
        ));        	
		
		$this->addElement ( 'textarea', 'desc', array (
			'class' => 'tex_markitup',
			'label' => 'Описание',
			'filters' => array ('StringTrim'),
		));
		
		$this->addElement ('textarea', 'meta_title', array (
			'class' => 'field textarea small',
			'label' => 'Заголовок',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255) )
			)
		));	
		
		$this->addElement ('textarea', 'meta_keywords', array (
			'class' => 'field textarea small',
			'label' => 'Ключевые слова',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255) )
			)
		));	
		
		$this->addElement ('textarea', 'meta_desc', array (
			'class' => 'field textarea small',
			'label' => 'Мета описание',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255) )
			)
		));	
		
		$this->addElement ('text', 'price', array (
			'class' => 'field text large',
			'label' => 'Цена',
			'required' => true,
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array ('float',
				array ('StringLength', false, array (0, 20) )
			)
		));	
              
		$this->addElement ('text', 'position', array (
			'class' => 'field text large',
			'label' => 'Позиция',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 20) )
			)
		));	
		
		
	}
}
?>
