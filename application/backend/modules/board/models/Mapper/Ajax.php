<?php
class Mapper_Ajax extends Mapper_Super
{
	protected $_tableName = 'Base';
	protected $_rowClass = 'Super';

	public function getCities($id)
	{
		$db = $this->getDbTable()->getAdapter();
		return $db->fetchPairs(
				$db->select()->from('cities', array('city_id', 'name'))
				->where('country_id = ' . $id)
			);
	}
}