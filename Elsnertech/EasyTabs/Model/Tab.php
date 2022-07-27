<?php

namespace Elsnertech\EasyTabs\Model;

use Elsnertech\EasyTabs\Api\Data\TabInterface;
use Magento\Framework\Model\AbstractModel;

class Tab extends AbstractModel implements TabInterface {

	protected $_idFieldName = 'entity_id';

	protected function _construct() {
		$this->_init('Elsnertech\EasyTabs\Model\ResourceModel\Tab');
	}

	public function getEntityId() {
		return $this->getData(self::ENTITY_ID);
	}

	public function setEntityId($entityId) {
		return $this->setData(self::ENTITY_ID, $entityId);
	}

	public function getTitle() {
		return $this->getData(self::TITLE);
	}

	public function setTitle($title) {
		return $this->setData(self::TITLE, $title);
	}

	public function getClass() {
		return $this->getData(self::TAB_CLASS);
	}

	public function setClass($class) {
		return $this->setData(self::TAB_CLASS, $class);
	}

	public function getContent() {
		return $this->getData(self::CONTENT);
	}

	public function setContent($content) {
		return $this->setData(self::CONTENT, $content);
	}

	public function getIsActive() {
		return $this->getData(self::IS_ACTIVE);
	}

	public function setIsActive($isActive) {
		return $this->setData(self::IS_ACTIVE, $isActive);
	}

	public function getTabSort() {
		return $this->getData(self::TAB_SORT);
	}

	public function setTabSort($tabSort) {
		return $this->setData(self::TAB_SORT, $tabSort);
	}
}
