<?php
require_once 'Zend/Form/Element/Xhtml.php';


/**
 * 
 * Zend Form Element File 
 * 
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 *
 */
class Moo_Form_Element_File extends Zend_Form_Element_Xhtml
{
    protected $_autoInsertValidFileValidator = true;
    
	public $helper = 'formFile';
}
