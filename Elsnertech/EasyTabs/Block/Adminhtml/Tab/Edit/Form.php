<?php

namespace Elsnertech\EasyTabs\Block\Adminhtml\Tab\Edit;

use Elsnertech\EasyTabs\Model\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends Generic {

	protected $wysiwygConfig;

	protected $options;

	public function __construct(
		Context $context,
		Registry $registry,
		FormFactory $formFactory,
		Config $wysiwygConfig,
		Status $options,
		array $data = []
	) {
		$this->options = $options;
		$this->wysiwygConfig = $wysiwygConfig;

		parent::__construct($context, $registry, $formFactory, $data);
	}

	protected function _prepareForm() {
		$model = $this->_coreRegistry->registry('row_data');
		$form = $this->_formFactory->create(
			['data' => [
				'id' => 'edit_form',
				'enctype' => 'multipart/form-data',
				'action' => $this->getData('action'),
				'method' => 'post',
			],
			]
		);

		$form->setHtmlIdPrefix('wkgrid_');
		if ($model->getEntityId()) {
			$fieldset = $form->addFieldset(
				'base_fieldset',
				['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
			);
			$fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
		} else {
			$fieldset = $form->addFieldset(
				'base_fieldset',
				['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
			);
		}

		$fieldset->addField(
			'title',
			'text',
			[
				'name' => 'title',
				'label' => __('Title'),
				'id' => 'title',
				'title' => __('Title'),
				'class' => 'required-entry',
				'required' => true,
			]
		);

		$fieldset->addField(
			'class',
			'text',
			[
				'name' => 'class',
				'label' => __('Tab Class Name'),
				'id' => 'class',
				'title' => __('Tab Class Name'),
				'class' => 'required-entry',
				'required' => true,
			]
		);

		$fieldset->addField(
			'tab_sort',
			'text',
			[
				'name' => 'tab_sort',
				'label' => __('Tab Sort Number'),
				'id' => 'tab_sort',
				'title' => __('Tab Sort Number'),
				'class' => 'required-entry',
				'note' => 'Sort numbers of magento default tabs are 10,20,30',
				'required' => true,
			]
		);

		$wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

		$fieldset->addField(
			'content',
			'editor',
			[
				'name' => 'content',
				'label' => __('Content'),
				'style' => 'height:36em;',
				'required' => true,
				'config' => $wysiwygConfig,
			]
		);

		$fieldset->addField(
			'is_active',
			'select',
			[
				'name' => 'is_active',
				'label' => __('Status'),
				'id' => 'is_active',
				'title' => __('Status'),
				'values' => $this->options->getOptionArray(),
				'class' => 'status',
				'required' => true,
			]
		);

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
