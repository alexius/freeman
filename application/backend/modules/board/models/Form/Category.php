<?php
class Form_Category extends Moo_Form
{
	public function init()
	{
		$this->setAttrib('class','commonform');
		$this->setDecorators(array(
        	array('ViewScript' , 
        	array('viewScript' =>'categories/_category.phtml'))
    	));
		$this->setAttrib ( 'enctype', 'multipart/form-data' );


		
	    $this->addElement ('hidden', 'id', array (
    		'id' => 'id',
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));
		
		$this->addElement ('hidden', 'parent_id', array (
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));

		$this->addElement ('text', 'name', array (
			'class' => 'texty',
			'label' => 'Название',
			'required' => true,
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 100 ) ),
			)
		));		
		
		$this->addElement ('textarea', 'desc', array (
			'class' => 'tex_markitup',
			'label' => 'Описание категории',
			'filters' => array ('StringTrim'),
		));		
		
		$this->addElement ( 'textarea', 'meta_title', array (
			'class' => 'tex',
			'label' => 'Заголовок (Meta title)',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255 ) ),
			)
		));
		
		$this->addElement ( 'textarea', 'meta_desc', array (
			'class' => 'tex',
			'label' => 'Описание (Meta description)',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255 ) ),
			)
		));
		
		$this->addElement ('textarea', 'meta_keywords', array (
			'class' => 'tex',
			'label' => 'Ключевые слова (Meta keywords)',
			'filters' => array ('StringTrim', 'StripTags'),
			'validators' => array (
				array ('StringLength', false, array (0, 255 ) ),
			)
		));	
		
		$this->addElement ('text', 'url', array (
			'class' => 'texty',
			'required' => true,
			'label' => 'Адрес в браузере',
			'validators' => array (
				array ('StringLength', false, array (3, 50 ) ),
			),
			'filters' => array ('StringTrim', 'StripTags'),
		));	
		
		$this->addElement ('checkbox', 'active', array (
			'class' => 'cheky',
            'value' => '1',
            'checked' => 'checked',
			'label' => 'Включен',
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));
		
		$this->addElement ('hidden', 'image', array (

		));
		

		
		$this->addElement ( 'file', 'image_loader', array (
				'class' => 'texty',
			//	'description' => 'Допускаются изображения формата .jpg, .png, .gif размерами 100x100 пикселей.<br>&nbsp;',
				'label' => 'Изображение',
				'validators' => array(
					array('Extension', false, 'jpeg,jpg,png,gif'),
					array('Size', false, 1024000),
					array('Count', false, 1),
					//array('ImageSize', false, array(100,100,100,100)),
				//	array('MimeType', false, 'image')
				),
				'decorators' => array(
					'File',
					array ('Description', array ('escape' => false ) ),
					'Errors',
					array ('HtmlTag', array ('tag' => 'dd' ) ),
					array ('Label', array ('tag' => 'dt' ) )
				)
		));
		$this->image_loader->setValueDisabled(true);
		
		$this->addElement ('submit', 'savestay', array (
			'label' => 'Сохранить',
			'decorators' => array(
				array ('Description', array ('escape' => false ) ),
				'ViewHelper',
	        	'Errors',
				array ('HtmlTag', array ('tag' => 'div', 'class' => 'savedata') )
			)
		));
		
		$this->addElement ('button', 'delete', array (
			'label' => 'Удалить',
			'decorators' => array(
				array ('Description', array ('escape' => false ) ),
				'ViewHelper',
	        	'Errors',
				array ('HtmlTag', array ('tag' => 'div', 'class' => 'savedata') )
			)
		));
	}
}
?>