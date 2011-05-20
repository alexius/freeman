<?php
class Form_Menu extends Core_Form
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


        $this->addElement ('text', 'name', array (
            'class' => 'text-input medium-input',
            'label' => Zend_Registry::get('translation')->get('name'),
            'required' => true,   
            'filters' => array ('StringTrim','StripTags' ),
            'validators' => array (
                array ('StringLength', false, array (1, 165 ) ),
            )
        ));        
        
		$this->addElement ('text', 'url', array (
			'class' => 'text-input medium-input',
            'required' => true,
			'label' => Zend_Registry::get('translation')->get('url'),
			'filters' => array ('StringTrim', 'StripTags'),
		));

		$this->addElement ('checkbox', 'active', array (
			'class' => 'checkbox',
			'checked' => 'checked',
			'label' => Zend_Registry::get('translation')->get('enable'),
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));

		$this->addElement ('checkbox', 'default', array (
			'class' => 'checkboxs',
			'label' => Zend_Registry::get('translation')->get('menu_default'),
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array ('int',
				array ('StringLength', false, array (0, 1) )
			)
		));

        $this->addElement ('textarea', 'text', array (
			'class' => 'ckeditor editable',
			'label' => Zend_Registry::get('translation')->get('text'),
			'filters' => array ('StringTrim'),
		));

		$this->addElement ('text', 'title', array (
			'class' => 'text-input large-input',
			'label' => Zend_Registry::get('translation')->get('meta_title'),
			'filters' => array ('StringTrim','StripTags' ),
			'validators' => array (
				array ('StringLength', false, array (1, 165 ) ),
			)
		));		
		
    	$this->addElement ( 'textarea', 'description', array (
			'class' => 'text-input textarea-small',
			'label' => Zend_Registry::get('translation')->get('meta_description'),
			'filters' => array ('StringTrim', 'StripTags'),
		));
		
		$this->addElement ('textarea', 'keywords', array (
			'class' => 'text-input textarea-small',
			'label' => Zend_Registry::get('translation')->get('meta_keywords'),
			'filters' => array ('StringTrim', 'StripTags'),
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