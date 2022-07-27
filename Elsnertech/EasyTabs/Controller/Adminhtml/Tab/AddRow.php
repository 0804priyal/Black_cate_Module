<?php

namespace Elsnertech\EasyTabs\Controller\Adminhtml\Tab;

use Elsnertech\EasyTabs\Model\TabFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class AddRow extends Action {

	private $coreRegistry;

	private $gridFactory;

	public function __construct(
		Context $context,
		Registry $coreRegistry,
		TabFactory $gridFactory
	) {
		parent::__construct($context);
		$this->coreRegistry = $coreRegistry;
		$this->gridFactory = $gridFactory;
	}

	public function execute() {
		$rowId = (int) $this->getRequest()->getParam('id');
		$rowData = $this->gridFactory->create();

		if ($rowId) {
			$rowData = $rowData->load($rowId);
			$rowTitle = $rowData->getTitle();
			if (!$rowData->getEntityId()) {
				$this->messageManager->addError(__('row data no longer exist.'));
				$this->_redirect('tab/tab/rowdata');

				return;
			}
		}

		$this->coreRegistry->register('row_data', $rowData);
		$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$title = $rowId ? __('Edit Row Data ') . $rowTitle : __('Add Row Data');
		$resultPage->getConfig()->getTitle()->prepend($title);

		return $resultPage;
	}

	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Elsnertech_EasyTabs::add_row');
	}
}
