<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_Messenger 
{
	
	protected static $_httpPath = null;
	protected static $_baseHttpPath = null;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * 
	 * Return current module base link path
	 * @param int $base | if needed to return raw path
	 */
	public function messenger($message, $type) 
	{
		if (empty($message)){
			return;
		}
		
		if ($type == 0)
		{
			$icon = 'ui-icon-alert';
			$state = 'ui-state-error';
		} 
		else if ($type == 1)
		{ 
			$icon = 'ui-icon-info';
			$state = 'ui-state-highlight';
		}
		
		$template = '<div class="ui-widget message">
			<div class="' . $state. ' ui-corner-all" style="padding: 0 .7em;"> 
				<p><span class="ui-icon ' . $icon . '" style="float: left; margin-right: .3em;"></span>
					' . $message . ' 
				</p>
			</div>
		</div>';
		return $template;
	}
}
?>