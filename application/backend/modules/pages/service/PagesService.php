<?php
class PagesService extends Core_Service_Ajax
{
    protected $_mapperName = 'PagesMapper';
    protected $_validatorName = 'Form_Page';
    protected $_gridFields = array ('id', 'name', 'url','active');
    protected $_slugtype = 'pages';       
    
}