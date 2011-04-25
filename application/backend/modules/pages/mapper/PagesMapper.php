<?php
class PagesMapper extends Core_Mapper_Super
{
	/**
	 * DbTable Class
	 * @var DbTable_Profile
	 */
	protected $_tableName = 'DbTable_Pages';
	protected $_rowClass = 'Page';    
    protected $_gridFilters = array(1 => 'primary', 2 => 'name',
                                    3 => 'url');
}

?>