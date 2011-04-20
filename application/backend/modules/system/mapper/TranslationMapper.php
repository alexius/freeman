<?php

/**
 * 
 * RolesMapper class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class TranslationMapper extends Core_Mapper_Super
{
	
	/**
	 * 
	 * DbTable Class
	 * @var DbTable_Roles
	 */
	protected $_tableName = 'DbTable_Translation';
	
	/**
	 * 
	 * Rols row class
	 * @var Role
	 */
	protected $_rowClass = 'Translation';

    public function setSystem($system)
    {
        $this->setDbTable('DbTable_' . $system);
    }

	public function add($data, $lang)
	{
		foreach ($lang as $langa => $val)
		{

			$dataIns = array(
				'lang' => $langa,
				'code' => $data['code'],
				'caption' => $data['caption_' . $langa],
				'module' => $data['fmodule'],
				'resourse' => $data['fresourse'],
				'action' => $data['faction'],
			);
            $this->getDbTable()->insert($dataIns);
		}
		return true;
	}


	public function getTranslationRow($code)
	{
		$translation = $this->getDbTable()->fetchRow(
			'code = "' . $code . '"'
		);
		return new Translation($translation->toArray());
	}

	public function saveTranslation($data)
	{
		$db = $this->getDbTable()->getAdapter();
		$db->beginTransaction();
	
		foreach ($data as $code => $trans)
		{
			foreach ($trans as $lang => $caption)
			{
				if (is_array($caption))
				{
					if (array_key_exists('new', $caption))
					{
						$transRow = $this->getTranslationRow($code);
						$transRow->caption = $caption['new'];
						$transRow->lang = $lang;
						$transRow->id = null;

						$db->insert(
							$this->getDbTable()->getName(),
							$transRow->toArray()
						);
					}
				}
				else
				{
					$db->update(
						$this->getDbTable()->getName(),
						array('caption' => $caption),
						'code = "' . $code . '" AND lang = "' . $lang . '"'
					);
				}
			}
		}

		try {
			$db->commit();
		} catch (Exception $e){
			$db->rollBack();
    		return $e->getMessage();
		}

		return true;
	}


	public function getTranslation($module, $resourse, $action, $search = null)
	{
        $table = $this->getDbTable()->getName();
        $translation = array();
		if (!empty($search))
        {
            $search = trim($search);
            $subSQL = $this->getAdapter()->select()
                ->from(array('tr'=>$table), array('code'))
                ->where('tr.caption LIKE "%' . $search . '%"
                    OR tr.code LIKE "%' . $search . '%"');

            if ($module === '0' || $resourse === '0' || $action === '0')
            {

                $sql = $this->getAdapter()->select()
                        ->from(array('tran'=>$table))
                        ->where('tran.code IN (' . ( new Zend_Db_Expr($subSQL)) .')')
                        ->order('tran.code');
                $translation = $this->getAdapter()->fetchAll($sql);
            }
            else
            {
                $sql = $this->getAdapter()->select()
                        ->from(array('tran'=>$table))
                        ->where('tran.module = "' . $module . '"
                            AND tran.resourse = "' . $resourse . '"
                            AND tran.action = "' . $action . '"
                            AND tran.code IN (' . ( new Zend_Db_Expr($subSQL)) .')')
                        ->order('tran.code');

                $translation = $this->getAdapter()->fetchAll($sql);
            }

		}
        else
        {
            if ($module === '0' || $resourse === '0' || $action === '0')
            {
                $translation = $this->getDbTable()->fetchAll();

            }
            else
            {
                $sql = $this->getAdapter()
                        ->select()
                        ->from($table)
                        ->where('module = "' . $module . '"
                            AND resourse = "' . $resourse . '"
                            AND action = "' . $action . '"')
                        ->order('code ASC, lang');
                $translation = $this->getAdapter()->fetchAll($sql);
            }

		}
        
		$collection = new Collection_Translations();
        $collection->populate($translation);
        return $collection;
	}

	public function getFilters()
	{
        $sql = $this->getDbTable()->select()
            ->from($this->getDbTable()->getName(), array('module', 'resourse', 'action'))
            ->group('module')
            ->group('resourse')
            ->group('action');
        $res = $this->getDbTable()->fetchAll($sql);

		if (empty($res)){
			return false;
		}

		$sorted = array();
		foreach ($res as $f)
		{
			$sorted[$f['module']][$f['resourse']][] = $f['action'];
		}

		return $sorted;
	}

}