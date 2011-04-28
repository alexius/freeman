<?php

/**
 *  Super Ajax Service Class
 *  @author Fedor Petryk
 *  @package Core_Service
 *  @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Service_Ajax extends Core_Service_Super
{
	/**
	 * Encoded array of data to be returned
	 * @var String
	 */
	protected $_jsonData = null;

	public function getJsonData()
	{
		if ($this->_jsonData != null)
		{
			return $this->_jsonData;
		}

		return false;
	}

    public function setJsonData($json)
	{
	    $this->_jsonData = $json;
	}

    public function getGridData(array $filters)
	{
		$total_rows = $this->getMapper()->countAll();
        if (empty($filters))
        {
            return false;
        }
		$rows = $this->_mapper->fetchForGrid($this->_gridFields, $filters);

        if (!empty($rows))
        {
            $this->setMessage(Core_Model_Messages::getMessage('data_get'));
			$this->setJsonData(array(
                'grid_data' => $rows,
				'total_rows' => $total_rows
            ));
		}
        else
        {
            $this->setMessage(Core_Model_Messages::getMessage('no_data'));
			$this->setJsonData (array ('error' => Errors::getError(111)));
		}
	}
}