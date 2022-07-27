<?php

namespace Chilliapple\WebsiteSwitcher\Block;

class WebsiteSwitcher extends \Magento\Framework\View\Element\Template
{
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

}