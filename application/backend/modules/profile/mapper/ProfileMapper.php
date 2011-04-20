<?php
/**
 * Mapper UpdateAvatar 
 *
 * @author     Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class ProfileMapper extends Core_Mapper_Super {
	
	/**
	 * DbTable Class
	 * @var DbTable_Profile
	 */
	protected $_tableName = 'DbTable_Users';
	
	public function setAvatar($id, $content){
		$db = $this->getDbTable()->getAdapter();
		$data = array('avatar' => $content);
		$res = $db->update('users',$data,'user_id='.$id);
	}
	
	public function getAvatar($id) {
		$db = $this->getDbTable()->getAdapter();
		$sql = $db->select()
			->from(array('u'=>'users'),array('avatar'))
			->where('u.user_id='.$id);
		$result = $db->fetchOne($sql);
		if (empty($result)){
			return false;
		}
		return $result;
	}

	public function setSettings($id, $data){
		$db = $this->getDbTable();
		return $db->update($data,'user_id = "' . $id . '"');
	}
}
?>