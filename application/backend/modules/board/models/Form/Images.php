<?php
class Form_Images extends Moo_Form
{
	public function init()
	{
		$this->setAttrib('class','upload_file');
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setAction('#images-upload') ;
		$this->setDecorators(array(
        	array('ViewScript' , array('viewScript' =>'products/_images.phtml'))));

	$this->addElement ( 'file', 'image_name', array (
			'class' => 'multi',
			'name' => 'file[]',
			'label' => 'Изображения',
			//'required' => true,
			//'maxlength' => 4,
			'accept' => 'gif|png|jpg|jpeg',
			'validators' => array(
				array('Extension', false, 'gif,png,jpg,jpeg,pjpeg'),
				array('Size', false, 10457600),
				array('Count', false, array('min' => 1, 'max' => 4)),
				array('MimeType', false, array('image/pjpeg','image/jpeg','image/png',
					'image/jpg','image/gif')
				)),
			'decorators' => array(
				'File',
				array ('Description', array ('escape' => false ) ),
				'Errors',
				array ('HtmlTag', array ('tag' => 'dd', 'class' => 'input-file' ) ),
				array ('Label', array ('tag' => 'dt' ) )
			)
		));
	//$this->file_name->setIsArray(true);
/*
		$this->addElement ( 'submit', 'save', array (
		'class' => 'textysub',
		'required' => false,
			'ignore' => true,
			'label' => 'Загрузить',
		'decorators' => array(
				array ('Description', array ('escape' => false ) ),
				'ViewHelper',
	        	'Errors',
				array ('HtmlTag', array ('tag' => 'div', 'class' => 'uploadf' ) )
			)
		));
*/
	}
}
?>