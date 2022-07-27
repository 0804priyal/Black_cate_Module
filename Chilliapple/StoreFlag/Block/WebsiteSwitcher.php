<?php

namespace Chilliapple\StoreFlag\Block;

class WebsiteSwitcher extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Chilliapple\StoreFlag\Model\StoreFlagFactory $storeFlagFactory,

        array $data = array()
    ) {
        $this->storeFlagFactory = $storeFlagFactory;
        parent::__construct($context, $data);
    }
    public function getWebsites()
    {

        return $this->_storeManager->getWebsites();
    }

    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getWebsite()->getId();
    }
    public function getMediaUrl()
    {
    	return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
    }

    public function getFlagCollection(){

    $collection = $this->storeFlagFactory->create()->getCollection();
    return $collection;

}

}