<?php

/**
 * user menu builder
 * build header menu according to permited users rights 
 * @author Aleksey Kagatlykskiy
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Zend_View_Helper_Menu {
	
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}
	
	/**
	 * build header menu
	 */
	public function menu()
	{
		$html = '<ul id="main-nav" class="menu-scroll">';
		$acl = Zend_Registry::get('acl');

        $front = Zend_Controller_Front::getInstance();
        $curRes = $front->getRequest()->getControllerName();
        $curModel = $front->getRequest()->getModuleName();
        $curAct = $front->getRequest()->getActionName();

		if (!empty($acl)) {
			$normalizedAcl =(Core_Controller_Plugin_AclNormalizer::normalize($acl));
		}

		if (!empty($normalizedAcl))
		{
			$menuitemsId = 0;
			foreach ($normalizedAcl as $module => $resourses)
			{
				if ($resourses['show'] == 1)
				{
					$menuitemsId++;
					if (!empty($resourses['resourses']))
					{
                        if ($resourses['module_name'] == $curModel){
                            $topItemClass = 'nav-top-item current';
                        } else {
                            $topItemClass = 'nav-top-item';
                        }

						$html .= '<li>
							        <a class="' . $topItemClass . '" href="' .
								        $link = $this->view->domainLink(1)
										. $module . '/">'
								        . Zend_Registry::get('translation')
                                                        ->get($resourses['module_name'])

                                        . '</a>';

						if (count($resourses['resourses']) > 0)
						{
							$html .= '<ul>';
							foreach ($resourses['resourses'] as $controller => $action)
							{
								foreach ($action as $val)
								{
									if ($val['menu'] == 1)
									{
                                        $subItemClass = '';
                                        if ($resourses['module_name'] == $curModel &&
                                                $controller == $curRes &&
                                                $val['action'] == $curAct){
                                            $subItemClass = 'current';
                                        }
										$link = $this->view->domainLink(1)
											. $module . '/' . $controller . '/' . $val['action'];
										$html .= '<li><a class="' . $subItemClass . '"
										    href = "' . $link . '">'
											. Zend_Registry::get('translation')->get($val['name']) .
											'</a></li>';
									}
								}
							}
							$html .= '</ul>';
						}
					}
					$html .= '</li>';
				}
			}
		}
		$html .= '</ul>';

		return $html;
		
	} 
	
	
}
?>