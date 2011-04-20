<?php
class Mapper_Pages extends Mapper_Super
{
	protected $_tableName = 'Pages';
	protected $_rowClass = 'Page';    
    protected $_gridFilters = array(1 => 'primary', 2 => 'name',
                                    3 => 'url');
}

?>