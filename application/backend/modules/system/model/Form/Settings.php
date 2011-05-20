<?php
class Form_Settings extends Core_Form
{
	public function init()
	{
		$this->setAttrib('class','forms ajax-forms');

	    $this->addElement ('hidden', 'id', array (
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (1, 10 ) )
			)
		));

        $this->addElement ('submit', 'subtop', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->subtop->setLabel(Zend_Registry::get('translation')->get('save'));
		$this->subtop->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'div', 'class'=>'submit-group-top')),
		));

        $this->addElement ('button', 'subtopclear', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->subtopclear->setLabel(Zend_Registry::get('translation')->get('clear'));
		$this->subtopclear->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTagClear', array('tag' => 'div', 'class'=>'submit-group-top')),
		));


        $this->addElement ('text', 'param_name', array (
            'class' => 'text-input medium-input',
            'label' => Zend_Registry::get('translation')->get('param_name'),
            'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 165 ) ),
            )
        ));        
        
		$this->addElement ('text', 'param_value', array (
			'class' => 'text-input medium-input',
            'required' => true,
			'label' => Zend_Registry::get('translation')->get('param_value'),
		//	),
			'filters' => array ('StringTrim', 'StripTags'),
		));



        $this->addElement ('textarea', 'param_description', array (
			'class' => 'editable',
			'label' => Zend_Registry::get('translation')->get('param_description'),
			'filters' => array ('StringTrim'),
		));

		
        $this->addElement ('submit', 'sub', array (
			'filters' => array ('StringTrim','StripTags' ),
		));
		$this->sub->setLabel(Zend_Registry::get('translation')->get('save'));
		$this->sub->setDecorators(array(
		   array('ViewHelper'),
		   array('Description'),
		   array('HtmlTag', array('tag' => 'div', 'class'=>'submit-group')),
		));
	}
}
?>