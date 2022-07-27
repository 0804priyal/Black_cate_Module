<?php

namespace Elsnertech\EasyTabs\Controller\Adminhtml\Tab;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action {

	protected $_resultPageFactory;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->_resultPageFactory = $resultPageFactory;
	}

	public function execute() {
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Elsnertech_EasyTabs::overview');
		$resultPage->getConfig()->getTitle()->prepend(__('Tab List'));

		return $resultPage;
	}

	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Elsnertech_EasyTabs::overview');
	}
}
