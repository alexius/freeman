<?php
class Service_Page extends Service_Super 
{
    protected $_mapperName = 'Pages';
    protected $_formName = 'Page';
    protected $_gridFields = array ('id', 'name', 'url','active');
    protected $_slugtype = 'pages';       
    
}