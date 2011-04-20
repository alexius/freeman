<?php
class Service_Menu extends Service_Super 
{
    protected $_mapperName = 'Menu';
    protected $_formName = 'Menu';   
    protected $_gridFields = array ('id', 'name', 'url','active');
    protected $_slugtype = 'menu';   
}
