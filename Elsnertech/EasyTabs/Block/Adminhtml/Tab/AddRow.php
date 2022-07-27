<?php

namespace Elsnertech\EasyTabs\Block\Adminhtml\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class AddRow extends Container {

	protected $_coreRegistry = null;

	public function __construct(
		Context $context,
		Registry $registry,
		array $data = []
	) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}

	protected function _construct() {
		$this->_objectId = 'row_id';
		$this->_blockGroup = 'Elsnertech_EasyTabs';
		$this->_controller = 'adminhtml_tab';

		parent::_construct();

		if ($this->_isAllowedAction('Elsnertech_EasyTabs::add_row')) {
			$this->buttonList->update('save', 'label', __('Save'));
		} else {
			$this->buttonList->remove('save');
		}

		$this->buttonList->remove('reset');
	}

	public function getHeaderText() {
		return __('Add RoW Data');
	}

	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed($resourceId);
	}

	public function getFormActionUrl() {
		if ($this->hasFormActionUrl()) {
			return $this->getData('form_action_url');
		}

		return $this->getUrl('*/*/save');
	}
}
