<?php
class MenuMapper extends Core_Mapper_Ajax
{
	/**
	 * DbTable Class
	 * @var DbTable_Profile
	 */
	protected $_tableName = 'DbTable_Menu';
	protected $_rowClass = 'Menu';
    protected $_gridFilters = array(1 => 'primary', 2 => 'name',
                                    3 => 'url');
}

?>