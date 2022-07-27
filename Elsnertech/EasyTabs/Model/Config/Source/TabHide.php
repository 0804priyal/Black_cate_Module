<?php

namespace Elsnertech\EasyTabs\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TabHide implements ArrayInterface {
	public function toOptionArray() {
		return [
			['value' => 0, 'label' => __('No')],
			['value' => 'all', 'label' => __('Hide All')],
			['value' => 'custom', 'label' => __('Custom')],
		];
	}
}
