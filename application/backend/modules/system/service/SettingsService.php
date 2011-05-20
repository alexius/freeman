<?php
class SettingsService extends Core_Service_Ajax
{
    protected $_mapperName = 'SettingsMapper';
    protected $_validatorName = 'Form_Settings';
    protected $_gridFields = array ('id', 'param_name', 'param_value','param_description');
}