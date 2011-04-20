<?php
class Mapper_News extends Mapper_Super
{
    protected $_tableName = 'News';
    protected $_rowClass = 'News';
    protected $_gridFilters = array(1 => 'primary', 2 => 'name',
                                    3 => 'url', 4 => 'category');
}

?>