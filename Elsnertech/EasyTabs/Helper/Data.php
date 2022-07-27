<?php

namespace Elsnertech\EasyTabs\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {
	const XML_PATH_ENABLED = 'custom_tab_general/general/enabled';
	const XML_PATH_HIDE_DETAILS = 'custom_tab_general/default_tabs/details';
	const XML_PATH_HIDE_DETAILS_SORT = 'custom_tab_general/default_tabs_sort/details_sort';
	const XML_PATH_HIDE_MORE = 'custom_tab_general/default_tabs/more';
	const XML_PATH_HIDE_MORE_SORT = 'custom_tab_general/default_tabs_sort/more_sort';
	const XML_PATH_HIDE_REVIEWS = 'custom_tab_general/default_tabs/review';
	const XML_PATH_HIDE_REVIEWS_SORT = 'custom_tab_general/default_tabs_sort/review_sort';

	protected $config;

	public function __construct(
		Context $context,
		ScopeConfigInterface $config
	) {
		$this->config = $config;
		parent::__construct($context);
	}

	public function isEnabled() {
		return $this->config->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	public function hideDetails() {
		return $this->config->getValue(self::XML_PATH_HIDE_DETAILS, ScopeInterface::SCOPE_STORE);
	}

	public function getSortDetails() {
		return $this->config->getValue(self::XML_PATH_HIDE_DETAILS_SORT, ScopeInterface::SCOPE_STORE);
	}

	public function hideMoreInfo() {
		return $this->config->getValue(self::XML_PATH_HIDE_MORE, ScopeInterface::SCOPE_STORE);
	}

	public function getSortMoreInfo() {
		return $this->config->getValue(self::XML_PATH_HIDE_MORE_SORT, ScopeInterface::SCOPE_STORE);
	}

	public function hideReviews() {
		return $this->config->getValue(self::XML_PATH_HIDE_REVIEWS, ScopeInterface::SCOPE_STORE);
	}

	public function getSortReviews() {
		return $this->config->getValue(self::XML_PATH_HIDE_REVIEWS_SORT, ScopeInterface::SCOPE_STORE);
	}
}