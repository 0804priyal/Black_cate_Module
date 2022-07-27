<?php

namespace Chilliapple\Sortby\Block\Rewrite\Catalog\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{

    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $searchParam = $this->getRequest()->getParam('q'); 
        
        
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int) $this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if ($this->getCurrentOrder() == 'popularity' && empty($searchParam)) {
                $this->_collection->getSelect()
                    ->joinLeft(
                        'sales_order_item',
                        'e.entity_id = sales_order_item.product_id',
                        array('qty_ordered' => 'SUM(sales_order_item.qty_ordered)')
                    )
                    ->group('e.entity_id')
                    ->order('qty_ordered ' . $this->getCurrentDirection());

            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
      
        return $this->_collection;
    }
}
