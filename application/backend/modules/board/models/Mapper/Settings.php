<?php
class Mapper_Settings extends Mapper_Super
{
	protected $_tableName = 'Settings';
	protected $_rowClass = 'Settings';
	protected $_gridFilters = array(1 => 'primary', 2 => 'vision', 3=>'param_description');
								
	public function fetchSettingsGroup($group, $vision = null)
	{
		$settings_group = $this->getDbTable()->fetchGroup($group, $vision);
		if (empty($settings_group)) {
			return false;
		}
		
		$entries = array();
		foreach ($settings_group as $sg)
		{
            $entry = new $this->_rowClass;
            $entry->populate($sg);
            $entries[$sg->param_name] = $entry;
        }
        return $entries;
	}
}