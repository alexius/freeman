<?php
class Mapper_Manuf extends Mapper_Super
{
    protected $_tableName = 'Manuf';
    protected $_rowClass = 'Manuf';    
    protected $_gridFilters = array(1 => 'primary', 2 => 'name');
     
    public function getManufs()
    {
        $db = $this->getDbTable()->getAdapter();
        return $db->fetchPairs(
                $db->select()->from('manufacturers', array('id', 'name')));
    }
}

?>