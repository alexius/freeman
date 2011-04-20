<?php
/**
 * Extended Form with new decorators and translated errors to russion lang
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2006-2011 S2B (http://www.s2b.com.ua)
 */

class Core_Translation
{
    protected $_translation = array();

    /**
     * Db gateway
     *
     * @var Zend_Db_Adapter_Abstract
     */
	protected $_db = NULL;

    /**
     * Initializes DB from registry
     *
     */
    public function init()
    {
        $this->_db = Zend_Registry::get('DB');
    }

    public function getTranslation($lang)
    {
        $select = $this->_db->select()
            ->from('translation',array('code','caption'))
            ->where('lang="' . $lang . '"');
        $result = $this->_db->fetchPairs($select);

        if(empty($result))
        {
            return false;
        }
        $this->_translation = $result;

        return true;
    }

    public function get($code)
    {
        if (array_key_exists($code, $this->_translation)){
			return $this->_translation[$code];
		}
		return $code;
    }

}
?>
