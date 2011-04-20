<?php

/**
 * 
 * Base static errors class
 * @author Fedor Petryk
 * @package Core_Model
 *
 */
class Core_Model_AjaxErrors
{
	/**
	 * 
	 * Error codes
	 * @var array
	 */
    protected static $errors = array ( 
    
 			600 => 'Ошибка: У вас нету прав для выполнения данного действия. Код 600'
			
		);

	/**
	 * 
	 * System Error codes
	 * @var array
	 */
    protected static $server_errors = array (
        404 => 'Страница, которую вы запросили, отсутствует.
        Возможно, вы ошиблись при наборе адреса или перешли по неверной ссылке.',
        500 => 'Возникла ошибка приложения, попробуйте позже. 
        Просим извинения за неудобства.');

	/**
	 * 
	 * Gets an error string and replace params if needed
	 * @param the code of error int|$code
	 * @param the replacment values array|$params
	 * @return String
	 */
    static public function getError($code, $params = null)
    {
    	if (empty($params))
    	{
    		$errorData = array ('error' => 'true', 
    			'error_message' =>  self::$errors[$code]);
        	return $errorData;    		
    	}
    	$error = self::$errors[$code];
    	foreach ($params as $key => $p)
    	{
    		$error = str_replace( '$' . $key, $p, $error);	
    	}
    	
    	$errorData = array ('error' => 'true', 'error_message' => $error);
    	return $errorData; 
    }

	/**
	 * 
	 * Gets a system error
	 * @param the code of error int|$code
	 * @return String
	 */
    static public function getServerError($code)
    {
        return self::$server_errors[$code];
    }
}