<?php
		
/**
 * 
 * FOr parsing xls files into table structure class
 * @author Fedor Petryk
 * @package Core_Model
 *
 */
class Core_Model_XlsParser
{
	protected $_parser;

	public function initParser($type)
	{
		if ($type == 'xls'){
			$this->_parser = new PHPExcel_Reader_Excel5();
		} else if ($type == 'xlsx'){
			$this->_parser = new PHPExcel_Reader_Excel2007();
		} else {
			throw new Exception(Core_Model_Errors::getError(80));
		}
	}


	/**
	 * Parses xls file to table structure
	 * 1. header
	 * 2. rows
	 * @param String $filePath | path to file
	 * @param boolean $isLots | table or table lots flag 
	 * @return array
	 */
	public function parse($filePath, $isLots = false)
	{
        $objReader = $this->_parser;

		$objPHPExcel = $objReader->load($filePath);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        
        $lot = '0';
        $table = array();
        foreach ($objWorksheet->getRowIterator() as $key => $row) 
        {
        	$cellIterator = $row->getCellIterator();
      		$cellIterator->setIterateOnlyExistingCells(true);
      
      		$cells = array();
      		$i = 0;
         	foreach ($cellIterator as $cell) {
         		if ($i == 0 && $isLots){
         			$lot = $cell->getValue();
         		}
            	$cells[] = $cell->getValue();
            	$i++;
			}
			if ($key == 1)
			{
				$lot = 'header';
				$table[$lot] = $cells;	
			}
			else 
			{
				$table['body'][$lot][] = $cells;			
			}


        }
        return $table;
	}
}