<?php
class Form_Settings extends Moo_Form
{
	public function init()
	{
        $this->setAttrib('class','commonform');
        $this->setDecorators(array(
            array('ViewScript' , array('viewScript' =>'settings/_settings.phtml'))
        ));

		$this->addElement ( 'hidden', 'id', array (
			'filters'   => array('StringTrim', 'StripTags'),
			'validators' => array ('int', 
				array('StringLength', false, array (1, 10 ) )
			)
		));
		

		$this->addElement ( 'textarea', 'param_value', array (
            'label' => 'Значение параметра',      
			'required' => true,
            'class' => 'tex_markitup',         
			'filters' => array ('StringTrim' )
		));
		
		$this->addElement ( 'text', 'param_description', array (
            'label' => 'Описание параметра',      
			'filters' => array ('StringTrim', 'StripTags' ),
            'class' => 'field text large',
			'validators' => array (
				array('StringLength', false, array (1, 55 ) )
			)
		));
        
        $this->addElement ( 'checkbox', 'active', array (
            'label' => 'Активна настройка',      
            'class' => 'field checkbox',    
            'filters' => array ('StringTrim', 'StripTags'),
            'validators' => array (
                array ('StringLength', false, array (0, 1 ) ),
            )
        ));
	}
}
?>