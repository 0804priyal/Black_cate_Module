<?php

namespace Elsnertech\EasyTabs\Model\Observer;

use Elsnertech\EasyTabs\Helper\Data;
use Elsnertech\EasyTabs\Model\ResourceModel\Tab\CollectionFactory as TabFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class AddRemoveTab implements ObserverInterface {
	protected $config;

	protected $tabCollection;

	protected $filterProvider;

	protected $helper;

	protected $storeManager;

	public function __construct(
		ScopeConfigInterface $config,
		TabFactory $tabCollection,
		FilterProvider $filterProvider,
		StoreManagerInterface $storeManager,
		Data $helper
	) {
		$this->config = $config;
		$this->tabCollection = $tabCollection;
		$this->filterProvider = $filterProvider;
		$this->storeManager = $storeManager;
		$this->helper = $helper;
	}

	public function execute(Observer $observer) {
		$layout = $observer->getLayout();
		$handle = $layout->getUpdate()->getHandles();

		if ($this->helper->isEnabled() && in_array('catalog_product_view', $handle)) {
			$this->customizeDefaultTabs($layout);
			$this->addTabs($layout);
		}
	}

	protected function customizeDefaultTabs($layout) {
		$details = $layout->getBlock('product.info.description');
		$review = $layout->getBlock('reviews.tab');
		$moreInfo = $layout->getBlock('product.attributes');

		if ($details) {
			if ($this->helper->hideDetails()) {
				$layout->unsetElement('product.info.description');
			} else {
				$details->setSortOrder($this->helper->getSortDetails());
			}
		}

		if ($review) {
			if ($this->helper->hideReviews()) {
				$layout->unsetElement('reviews.tab');
			} else {
				$review->setSortOrder($this->helper->getSortReviews());
			}
		}

		if ($moreInfo) {
			if ($this->helper->hideMoreInfo()) {
				$layout->unsetElement('product.attributes');
			} else {
				$moreInfo->setSortOrder($this->helper->getSortMoreInfo());
			}
		}
	}

	protected function addTabs($layout) {
		$tabWrapper = $layout->getBlock('product.info.details');
		$storeId = $this->storeManager->getStore()->getId();

		if ($tabWrapper) {
			$tabCollection = $this->tabCollection->create()->addFieldToFilter('is_active', ['eq' => 1]);

			foreach ($tabCollection as $tab) {
				$layout->addBlock(Template::class, 'tab' . $tab->getId(), 'product.info.details');
				$layout->getBlock('tab' . $tab->getId())->setTemplate('Elsnertech_EasyTabs::renderer.phtml')
					->setTitle($tab->getTitle())
					->setClass($tab->getClass())
					->setSortOrder($tab->getTabSort())
					->setContent($this->filterProvider->getBlockFilter()->setStoreId($storeId)->filter($tab->getContent()));
				$layout->addToParentGroup('tab' . $tab->getId(), 'detailed_info');
			}
		}
	}
}
