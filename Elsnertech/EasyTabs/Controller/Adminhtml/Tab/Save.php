<?php

namespace Elsnertech\EasyTabs\Controller\Adminhtml\Tab;

use Elsnertech\EasyTabs\Model\TabFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Save extends Action {

	protected $gridFactory;

	public function __construct(
		Context $context,
		TabFactory $gridFactory
	) {
		parent::__construct($context);
		$this->gridFactory = $gridFactory;
	}

	public function execute() {
		$data = $this->getRequest()->getPostValue();

		if (!$data) {
			$this->_redirect('tab/tab/addrow');

			return;
		}

		try {
			$rowData = $this->gridFactory->create();
			$rowData->setData($data);

			if (isset($data['id'])) {
				$rowData->setEntityId($data['id']);
			}

			$rowData->save();
			$this->messageManager->addSuccess(__('Row data has been successfully saved.'));
		} catch (\Exception $e) {
			$this->messageManager->addError(__($e->getMessage()));
		}

		$this->_redirect('tab/tab/index');
	}

	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Elsnertech_EasyTabs::save');
	}
}
