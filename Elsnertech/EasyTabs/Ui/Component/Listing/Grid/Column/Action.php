<?php

namespace Elsnertech\EasyTabs\Ui\Component\Listing\Grid\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Action extends Column {

	const ROW_EDIT_URL = 'tab/tab/addrow';

	protected $urlBuilder;

	private $editUrl;

	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = [],
		$editUrl = self::ROW_EDIT_URL
	) {
		$this->urlBuilder = $urlBuilder;
		$this->editUrl = $editUrl;

		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as &$item) {
				$name = $this->getData('name');
				if (isset($item['entity_id'])) {
					$item[$name]['edit'] = [
						'href' => $this->urlBuilder->getUrl(
							$this->editUrl,
							['id' => $item['entity_id']]
						),
						'label' => __('Edit'),
					];
				}
			}
		}

		return $dataSource;
	}
}
