<?php

namespace Elsnertech\EasyTabs\Plugin;

use Elsnertech\EasyTabs\Helper\Data;
use Magento\Catalog\Block\Product\View\Details;

class TabOrder {

	protected $helper;

	public function __construct(
		Data $helper
	) {
		$this->helper = $helper;
	}

	public function beforeGetGroupSortedChildNames(Details $subject, $groupName, $callback) {
		if ($this->helper->isEnabled()) {
			$groupChildNames = $subject->getGroupChildNames($groupName, $callback);
			$layout = $subject->getLayout();

			$childNamesSortOrder = [];

			foreach ($groupChildNames as $childName) {
				$alias = $layout->getElementAlias($childName);
				$sortOrder = $subject->getChildData($alias, 'sort_order');

				$childNamesSortOrder[$alias] = $sortOrder;
			}

			asort($childNamesSortOrder);
			$iteration = 1;

			foreach ($childNamesSortOrder as $alias => $sort) {
				$subject->getChildBlock($alias)->setData('sort_order', $iteration);
				$iteration++;
			}
		}
	}
}