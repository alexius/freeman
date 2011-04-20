<?php

/**
 * 
 * Base static messages class
 * @author Fedor Petryk
 * @package Core_Model
 *
 */
class Core_Model_Messages
{
	/**
	 * Messages codes
	 * @var array
	 */
   /** protected static $_messages = array (
		// statuses
    	90 => '"$0" активирован(а)',
    	91 => '"$0" деактивирован(а)',
    	92 => '"$0" завершен(а)',
    	93 => '"$0" отменен(а)',
    	94 => '"$0": начата новая итерация',
		95 => 'Временные рамки статуса "$0" изменены',
		// suppliers
		100 => 'Квалификационная заявка поставщика "<b>$0</b>" изменина',
		101 => 'Приглашение было добавлено успешно',

		// tenders
		201 => 'Тендер активирован',
		202 => 'Вопрос добавлен в заявку',
	);*/


	/**
	 * 
	 * Gets an error string and replace params if needed
	 * @param the code of error int|$code
	 * @param the replacment values array|$params
	 * @return String
	 */
    static public function getMessage($code, $params = null)
    {
		$code = 'm' . $code;
    	if (empty($params))
    	{
        	return Zend_Registry::get('translation')->get($code);
    	}
    	$error = Zend_Registry::get('translation')->get($code);
    	foreach ($params as $key => $p)
    	{
    		$error = str_replace( '$' . $key, $p, $error);	
    	}
    	return $error; 
    }

}