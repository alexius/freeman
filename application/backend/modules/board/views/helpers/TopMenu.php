<?php
class Zend_View_Helper_TopMenu
{
	protected $_menu = array (
			'0' =>
				array(	'name' => 'Главная', 
						'module' => 'index', 
						'action' => 'index'),
		    '4' =>
				array(	'name' => 'Настройки', 
						'module' => 'settings', 
						'action' => 'index',
						'subitems' => array(
							'0' => array (	'name' => 'Системные настройки',
											'module' => 'settings',
											'action' => 'frontend'),
                       /*     '3' => array (    'name' => 'Клиентской части',
                                            'module' => 'settings',
                                            'action' => 'frontend'),
                            '4' => array (   'name' => 'Администраторская часть',
                                            'module' => 'settings',
                                            'action' => 'backend')         */
							)),
			'5' =>
				array(	'name' => 'Каталог',
						'module' => 'categories', 
						'action' => 'edit', 
						'subitems' => array(
							
							'1' => array (	'name' => 'Категории',
											'module' => 'categories',
											'action' => 'edit'),	
							'0' => array (	'name' => 'Товары',
											'module' => 'products',
											'action' => 'index'),
                            '2' => array (    'name' => 'Производители',
                                            'module' => 'manufacturer',
                                            'action' => 'index'
                                            ))),
  	
            '2' =>
                array(  'name' => 'Статические страници', 
                        'module' => 'pages', 
                        'action' => 'index',
                        'subitems' => array(
                            
                            '1' => array (    'name' => 'Страници',
                                            'module' => 'pages',
                                            'action' => 'index'),    
                            '0' => array (    'name' => 'Новости',
                                            'module' => 'news',
                                            'action' => 'index'))),
            '6' =>
                array(    'name' => 'Меню Сайта', 
                        'module' => 'menu', 
                        'action' => 'index'),
			);

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function TopMenu()
	{
        $permiss = Zend_Registry::get('acl');
		$baseMenu = '<ul id="navigation" class="sf-navbar">';
		$baseMenu .= $this->buildMenu($this->_menu);
		$baseMenu .= "</ul>";
		return $baseMenu;
	}

	public function buildMenu($menulist)
	{
		$menu = '';
		foreach ($menulist as $item)
		{
			$menu .= "
						<li>
							<a href='/admin/". $item['module'] . '/' . $item['action'] . "'>" . $item['name'] . "</a>";
			if (array_key_exists('subitems', $item))
			{
				$menu .= "
					<ul>";
				unset($item[0]);
				$menu .= $this->buildMenu($item['subitems']);
    			$menu .=  "</ul>";
			}
			$menu .= "</li>";
  		}
		return $menu;
	}
}
?>