<?php
class SettingsMapper extends Core_Mapper_Ajax
{
	/**
	 * DbTable Class
	 * @var DbTable_Profile
	 */
	protected $_tableName = 'DbTable_Settings';
	protected $_rowClass = 'Settings';
    protected $_gridFilters = array(1 => 'primary', 2 => 'param_name');
}

?>