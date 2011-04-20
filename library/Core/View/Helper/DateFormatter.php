<?php

/**
 * Date formatter
 *
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_DateFormatter
{
	
	/**
	 * 
	 * Returns formmated date
	 * @param String $date
	 * @param boolean $time | with or without time string
	 * @return String
	 */
	public function dateFormatter($date, $time = false) 
	{
		if (empty($date) || null == $date) {
			return false;
		}
		
		if ($time == false) {
			list($y, $m, $d) = explode('-', $date);
			return $d . '/' . $m . '/' . $y;
		}	
		else {
			list($date, $timer) = explode (' ', $date);
			list($y, $m, $d) = explode('-', $date);
			return $d . '/' . $m . '/' . $y . ' ' . $timer;
		}
	}
}