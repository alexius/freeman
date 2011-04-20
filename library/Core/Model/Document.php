<?php

/**
 * Base document class
 * 
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Model_Document extends Core_Model_Super
{
	/**
	 * 
	 * @see Core_Model_Super
	 */
	protected $_data = array (
		'id' => null,
		'document_id' => null,
		'document_name' => null,
		'file_name' => null,
		'document_description' => null,
		'date_add'=> null,
		'date_changed' => null,
		'control_sum' => null,
		'extension' => null,
		'document_content' => null,
		'document_size' => null,
		'mime_type' => null,
		'public' => 0
	);

	protected $_formField = 'document';
	
	/**
	 * 
	 * Creates new document object from a given validator
	 * @param Zend_File_Transfer_Adapter_Http $validator 
	 */
	public function create($validator)
	{
		$field = $this->_formField;
		
		$data = $validator->getValues();
		$adapter = $validator->$field->getTransferAdapter();
		
		$file = $adapter->getFileInfo();
	
		$this->document_description = $data['document_description'];
		$this->document_name = $data['document_name'];
		$this->mime_type = $validator->$field->getMimeType();
		
		$tmpName = $file[$field]['tmp_name'];
		$fp      = fopen($tmpName, 'r');
		$content = fread($fp, filesize($tmpName));  
		$this->document_content = $content;
		$this->control_sum = sha1_file($tmpName);
		$this->document_size = filesize($tmpName);
		fclose($fp);	
	
		$date = new Zend_Date();
		$this->date_add = $date->toString('y-M-d H:m:s');
		$this->date_changed = $date->toString('y-M-d H:m:s');
	
		if (function_exists('pathinfo'))
		{
			$inf = pathinfo ($tmpName);
			$this->extension = $inf['extension'];
			$this->file_name = $inf['filename'];
		}
		else 
		{
			return $this->setError(Errors::getError(98));	
		}
	}
	
	
}