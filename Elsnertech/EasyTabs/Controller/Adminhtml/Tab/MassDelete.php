<?php

namespace Elsnertech\EasyTabs\Controller\Adminhtml\Tab;

use Elsnertech\EasyTabs\Model\ResourceModel\Tab\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action {

	protected $_filter;

	protected $_collectionFactory;

	public function __construct(
		Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory
	) {
		$this->_filter = $filter;
		$this->_collectionFactory = $collectionFactory;
		parent::__construct($context);
	}

	public function execute() {
		$collection = $this->_filter->getCollection($this->_collectionFactory->create());
		$recordDeleted = 0;

		foreach ($collection->getItems() as $record) {
			$record->setId($record->getEntityId());
			$record->delete();
			$recordDeleted++;
		}

		$this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

		return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
	}

	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Elsnertech_EasyTabs::row_data_delete');
	}
}