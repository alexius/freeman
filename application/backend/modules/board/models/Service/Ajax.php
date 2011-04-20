<?php
class Service_Ajax extends Service_Super 
{
	protected $_mapperName = 'Ajax';
	
	public function getCities($id)
	{
		$cities = $this->_mapper->getCities($id);
		
		if (empty($cities))	{
			return false;
		}
		
		$chtml = '';
		foreach ($cities as $key => $c)
		{
			$chtml .= '<option value = "' . $key . '">' . $c . '</option>';	
		}
		return $chtml;
	}
}