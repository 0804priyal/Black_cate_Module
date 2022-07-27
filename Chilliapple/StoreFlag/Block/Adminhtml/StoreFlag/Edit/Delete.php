<?php

namespace Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit;

class Delete extends \Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit\GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getFlagId()) {
            $data = [
                'label' => __('Delete Flag'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['flag_id' => $this->getFlagId()]);
    }
}
