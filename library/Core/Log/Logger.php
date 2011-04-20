<?php

/**
 * 
 * The access event logger class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Log_Logger 
{
	
	/**
	 * 
	 * Events messages types
	 * @var array
	 */	
	protected static $_eventsTypes = array (
		0 => 'Открытие страници',
		1 => 'Доступ запрещен',
		2 => 'Отправка данных',
		3 => 'Вход в систему',
		4 => 'Открытие несуществующей страници'
	);
	 
	/**
	 * 
	 * Inserting event into db
	 * @param array $data
	 */
	protected static function insertEvent(array $data)
	{
		$date = new Zend_Date();
		$data['event_date'] = $date->toString('y-M-d H:m:s');
		
		$db = Zend_Registry::get('DB');
		$db->insert('events_logs', $data);	
	}

	/**
	 * 
	 * Logging deault access event
	 * @param Zend_Controller_Request_Abstract |  $request
	 * @param Type of the log event String | $type
	 */
	public static function logAccessEvent(
		Zend_Controller_Request_Abstract $request, $type = 0) 
	{
		$data = array();
		$auth = Zend_Auth::getInstance()->hasIdentity();
		$ip = $_SERVER['REMOTE_ADDR'];  
		$action = $request->getActionName();
		$module = $request->getModuleName();
		$controller = $request->getControllerName();	
				
		$aclr = Core_Controller_Plugin_AclNormalizer::fullAclNormalize(
				Zend_Registry::get('fullAcl')
		);
		$act = '';
		
		if ($auth) 
		{
			$user = Core_Model_User::getInstance();
			
			if (array_key_exists($module, $aclr['modules']))
			{
				if (array_key_exists($module . ':' . $controller, 
					$aclr['modules'][$module]['resourses']))
				{
					if (array_key_exists($action, 
							$aclr['modules'][$module]
							['resourses'][$module . ':' . $controller]
							['actions']))
					{	
						$act = ($aclr['modules'][$module]
							['resourses'][$module . ':' . $controller]
							['actions'][$action]);
					}
					else
					{
						$act = 'Неизвестное действие';
					}
				} 
				else
				{
					$act = 'Неизвестный контроллер';
				}
			}
			else
			{
				$act = 'Неизвестный модуль';
			}
			
			$userLog = $user->name . '(' 
				. $user->user_id . ', ' 
				. $ip . ') - ' 
				. $act . ' ';
				
			$data = array( 
				'user' => $user->name,
				'user_id' =>  $user->user_id 
			);
		} 
		else 
		{
			$userLog = 'Анонимный Пользователь (' . $ip . ')';
			if (array_key_exists($module, $aclr['modules']))
			{
				if (array_key_exists($module . ':' . $controller,
					$aclr['modules'][$module]['resourses']))
				{
					$act = ($aclr['modules'][$module]
						['resourses'][$module . ':' .$controller]
						['actions'][$action]);
				}
				else {
					$act = 'Неизвестный контроллер';
				}

			}
			else
			{
				$act = 'Неизвестный модуль';
			}
		}
		
		$data += array( 
			'access_path' => $module . '/' . $controller . '/' .$action,
			'ip' =>  $ip,
			'action_desc' => $act, 
			'message' => self::$_eventsTypes[$type]
		);
			
		self::insertEvent($data);
		
	}
	
	/**
	 * 
	 * Logging error event to db and file
	 * @param String $errors | error object
	 * @param String $auth
	 */
	public static function logErrorEvent($errors, $auth = null) 
	{
		$data = array();
		$request = $errors->request;
		$exception = $errors->exception;
		$auth = Zend_Auth::getInstance()->hasIdentity();
		$ip = $_SERVER['REMOTE_ADDR'];  
		
		$action = $request->getActionName();
		$module = $request->getModuleName();
		$controller = $request->getControllerName();	

		if ($auth) 
		{
			$user = Core_Model_User::getInstance ();
			
			$userLog = 'Пользователь (' 
				. $user->name . '('
				. $user->user_id . '),' . $ip . ')';
			
			$data = array( 
				'user' => $user->name,
				'user_id' =>  $user->user_id 
			);
		} 
		else 
		{
			$userLog = 'Анонимный Пользователь (' . $ip . ')';
		}
		
		$data = $data + array( 
			'action_desc' => $exception->getMessage(),
			'ip' =>  $ip,
			'message' => $exception->getTraceAsString(),
			'access_path'  => $module . '/' . $controller . '/' . $action 
		);
			
		// Logging error into file
		
		if (defined ( 'APPLICATION_PUB' )) 
		{
			$log = new Zend_Log ( new Zend_Log_Writer_Stream ( 
				APPLICATION_PUB . '/log/error.log' ) );
		} 
		else 
		{
			$log = new Zend_Log ( new Zend_Log_Writer_Stream ( 
				APPLICATION_PATH . '/log/error.log' ) );
		}
		$log->debug ( $userLog . '
                 			' . $exception->getMessage () . "\n" 
			. $exception->getTraceAsString () );
			
		self::insertEvent($data);
	
	}
}