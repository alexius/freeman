<?php
class Form_Image extends Moo_Form
{
	public function init()
	{
		$this->setAttrib('class','commonform');
		$this->setAttrib ( 'enctype', 'multipart/form-data' );

		$this->addElement ( 'file', 'image', array (
			'class' => 'texty',
		//	'description' => 'Допускаются изображения формата .jpg, .png, .gif размерами 100x100 пикселей.<br>&nbsp;',
			'label' => 'Аватар',
			'validators' => array(
				array('Extension', false, 'jpeg,jpg,png,gif'),
				array('Size', false, 10024000),
				array('Count', false, 1),
				//array('ImageSize', false, array(100,100,100,100)),
				//array('MimeType', false, array('image/jpeg','image/pjpeg','image/png','image/gif'))
			),
			'decorators' => array(
				'File',
				array ('Description', array ('escape' => false ) ),
				'Errors',
				array ('HtmlTag', array ('tag' => 'dd' ) ),
				array ('Label', array ('tag' => 'dt' ) )
			)
		));
	}
}
?>