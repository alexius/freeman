<?php

/**
 * user menu builder
 * build header menu according to permited users rights 
 * @author Aleksey Kagatlykskiy
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Zend_View_Helper_SmallMenu {
	
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}
	
	/**
	 * build header menu
	 */
	public function smallMenu()
	{
		$html = '<ul  id="icons-menu"
		    class="top-menu ui-widget ui-helper-clearfix menu-buttons menu-scroll">';
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

                        $link = $this->view->domainLink(1)
										. $module . '/';
                        $button = $this->view
                                ->iconButton($link, 'ui-icon-wrench',
                                    Zend_Registry::get('translation')
                                        ->get($resourses['module_name']),
                                    '', '', '', 'float-left'
                                );
						$html .= '<div class="sub-wrapper">' . $button;

						if (count($resourses['resourses']) > 0)
						{
							$html .= '<ul class="float-left sub-menu no-display">';
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
										$html .= '<li class="no-float"><a class="' . $subItemClass . '"
										    href = "' . $link . '">'
											. Zend_Registry::get('translation')->get($val['name']) .
											'</a></li>';
									}
								}
							}
							$html .= '</ul>';
						}
                        $html .= '<div class="clear"></div></div>';
					}
					
				}
			}
		}
		$html .= '</ul>';

		return $html;
		
	} 
	
	
}
?>