<?php
class MenuService extends Core_Service_Ajax
{
    protected $_mapperName = 'MenuMapper';

	/**
	 * @var MenuMapper
	 */
	protected $_mapper;
	
    protected $_validatorName = 'Form_Menu';
    protected $_gridFields = array ('id', 'name', 'url', 'active', 'default');
    protected $_slugtype = 'menu';

	protected function postObjectSave($model)
	{
		if ($model->default == 1){
			$this->_mapper->resetDefault($model->id);
		}
	}
}