<?php
class Mapper_Menu extends Mapper_Super
{
    protected $_tableName = 'Menu';
    protected $_rowClass = 'Menu';    
    protected $_gridFilters = array(1 => 'primary', 2 => 'name',
                                    3 => 'url');
}

?>