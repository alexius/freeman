<?php

/**
 * 
 * The ACL normalizer class
 * Builds tree array according to required module and style
 * @author Fedor Petryk
 *
 */
class Core_Controller_Plugin_AclNormalizer extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * 
	 * Default result ACL tree array
	 * @var array
	 */
	protected static $_acl = null;

    /*
     * Set normalized flag
     */
    protected static $_isNormalize = false;
	/**
	 * 
	 * Builds tree for View_Helper_Users
	 * @param Acl registered in Zend_Registry under "acl" | $acl
	 */
	public static function normalize(array $acl)
	{
        if (self::$_isNormalize == true){
            return self::$_acl;
        }

		foreach ( $acl as $a ) 
		{
			if (! empty ( $a ['resourse_code'] )) 
			{
				list ( $module, $resourse ) = explode ( ':', $a ['resourse_code'] );
				self::$_acl [$module] ['module_name'] = $a ['module_name'];
         //       self::$_acl [$module] ['module_code'] = $a ['module_code'];
				self::$_acl [$module] ['show'] = $a ['show'];
				self::$_acl [$module] ['role_name'] = $a ['role_name'];
				
				self::$_acl [$module] ['resourses'] [$resourse] [] = array ('action' => $a ['action'], 'name' => $a ['right_name'], 'menu' => $a ['menu'] );
			}
		}
        self::$_isNormalize = true;
		return self::$_acl;
	}

	/**
	 * 
	 * Builds tree by roles
	 * @param Acl registered in Zend_Registry under "acl" | $acl
	 */
	public static function normalizeByRole(array $acl)
	{
		$fullAcl = array();
		foreach ($acl as $role => $element)
		{
			foreach ($element as $a)
			{
				if (!empty($a['resourse_code']))
				{
					$fullAcl[$role]['role_name'] = $a['role_name'];
					$fullAcl[$role]['role_id'] = $a['role_id'];
					$fullAcl[$role]['role_code'] = $a['role_code'];
					$fullAcl[$role]['editable'] = $a['editable'];
					
					list($module, $resourse) = explode (':', $a['resourse_code']);
					$fullAcl[$role]['modules'][$module]['module_name'] = $a['module_name'];		
					
					
					$fullAcl[$role]['modules'][$module]['resourses'][$resourse][] = array(
						'action' => $a['action'], 
						'name' => $a['right_name']);
				}
			}

		}
		return $fullAcl;
	}
	
	/**
	 * 
	 * Builds full Acl tree
	 * @param Acl registered in Zend_Registry under "acl" | $acl
	 */
	public static function fullAclNormalize(array $acl)
	{
		$fullAcl = array();
		foreach ($acl as $role => $element)
		{
			foreach ($element as $a)
			{
				if (!empty($a['resourse_code']))
				{
					list($module, $resourse) = explode (':', $a['resourse_code']);	
					
					$fullAcl['modules'][$module]['module_name'] = $a['module_name'];		
					$fullAcl['modules'][$module]['resourses'][$a['resourse_code']]['actions']
						[$a['action']] = $a['right_name'];
				}
			}

		}
		return $fullAcl;
	}

	/**
	 * 
	 * Returns acl
	 * @return array
	 */
	public static function getAcl()
	{ 
		if (null != self::$_acl){
			return self::$_acl;
		}
	}
}