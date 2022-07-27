<?php

namespace Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit;

class Reset extends \Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit\GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
