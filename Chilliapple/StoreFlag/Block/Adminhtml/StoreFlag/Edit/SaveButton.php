<?php

namespace Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit;

use \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use \Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit\GenericButton;

/**
 * Class SaveButton
 */
class SaveButton extends \Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit\GenericButton implements ButtonProviderInterface
{
    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
       
            return [
                'label' => __('Save Flag'),
                'class' => 'primary save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save']],
                    'form-role' => 'save',
                ],
                'sort_order' => 50,
            ];
    }
}
