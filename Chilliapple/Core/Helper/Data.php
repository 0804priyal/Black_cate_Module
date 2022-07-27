<?php
/**
 * Copyright © 2015 Bradbury . All rights reserved.
 */
namespace Chilliapple\Core\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $option;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Option $option
    )
    {
        $this->option  = $option;
      
    }

    public function getProductCustomOptions($product){
        return $this->option->getProductOptionCollection($product);
    }

}