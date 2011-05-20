<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class System_SettingsController extends Core_Controller_DefaultActions
{
	
	/**
     * Default service class name for current controller
     *
     * @var String
     */
	protected $_defaultServiceName = 'SettingsService';

    /**
     * @var SettingsService
     */
    protected $_service;
}