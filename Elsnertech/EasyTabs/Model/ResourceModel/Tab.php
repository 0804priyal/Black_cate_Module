<?php

namespace Elsnertech\EasyTabs\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tab extends AbstractDb {

	protected $_idFieldName = 'entity_id';

	protected function _construct() {
		$this->_init('elsnertech_tabs', 'entity_id');
	}
}