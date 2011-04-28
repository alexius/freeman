<?php
class PagesService extends Core_Service_Ajax
{
    protected $_mapperName = 'PagesMapper';
    protected $_formName = 'Page';
    protected $_gridFields = array ('id', 'name', 'url','active');
    protected $_slugtype = 'pages';       
    
}