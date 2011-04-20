<?php
class Form_Manuf extends Moo_Form
{
    public function init()
    {
        $this->setAttrib('class','commonform');


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
    }
}
?>
