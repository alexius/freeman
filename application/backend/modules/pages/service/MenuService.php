<?php
class MenuService extends Core_Service_Ajax
{
    protected $_mapperName = 'MenuMapper';
    protected $_validatorName = 'Form_Menu';
    protected $_gridFields = array ('id', 'name', 'url', 'active', 'default');
    protected $_slugtype = 'menu';
}