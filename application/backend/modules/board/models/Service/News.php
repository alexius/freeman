<?php
class Service_News extends Service_Super 
{
    protected $_mapperName = 'News';
    protected $_formName = 'News';
    protected $_gridFields = array ('id', 'name', 'category', 'url','active');
    protected $_slugtype = 'news';
    
}