<?php
/**
 * Copyright © 2015 Bradbury . All rights reserved.
 */
namespace Chilliapple\MatrixRate\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $matrixFactory;
	protected $registry;
	protected $storeManager;

    protected $pricingHelper;
	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Registry $registry,
		\WebShopApps\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory $matrixFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,        
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
	) {
        $this->pricingHelper = $pricingHelper;
		$this->matrixFactory = $matrixFactory;
		$this->registry = $registry;
		$this->storeManager = $storeManager;
		parent::__construct($context);
	}

	public function getDeliveryFrom() {
		$product = $this->getCurrentProduct();
		$weight = $product->getWeight();
		$matrix = $this->matrixFactory->create();
		$adapter = $matrix->getConnection();
        $data = $matrix->getMainTable();
        $select = $adapter->select()->from(
            $matrix->getMainTable()
        )->where(
            'website_id = :website_id'
        )->where(
            'condition_from_value < :weight'
        )->where(
            'condition_to_value >= :weight'
        )->order(
            ['condition_from_value ASC','price']
        );
        $bind = array(
        	':weight' => (int)$weight,
        	':website_id' => (int)$this->storeManager->getStore()->getWebsiteId()
        );
        $results = $adapter->fetchAll($select, $bind);
        foreach($results as $result ) {
        	return $result['price'];
        }
        return 0;
    }
     public function getCurrentProduct()
    {        
        return $this->registry->registry('current_product');
    }  

    public function getFormattedPrice($amount){
        return $this->pricingHelper->currency(number_format($amount,2),true,false);
        

    }
}