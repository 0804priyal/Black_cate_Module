<?php

namespace Elsnertech\EasyTabs\Model\ResourceModel\Tab;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection {

	protected $_idFieldName = 'entity_id';

	protected function _construct() {
		$this->_init('Elsnertech\EasyTabs\Model\Tab', 'Elsnertech\EasyTabs\Model\ResourceModel\Tab');
	}
}