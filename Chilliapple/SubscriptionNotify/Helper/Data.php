<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Chilliapple\SubscriptionNotify\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $context;

    public function __construct(Context $context,ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getEmailEnable()
     { 
      return $this->scopeConfig->getValue('subscriptionnotify/subscription/email_admin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
    public function getEmailTemplate()
     { 
       return $this->scopeConfig->getValue('subscriptionnotify/subscription/notification_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
    public function getEmailSender()
     { 
       return $this->scopeConfig->getValue('subscriptionnotify/subscription/email_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
    public function getEmailRecipient()
     { 
       return $this->scopeConfig->getValue('subscriptionnotify/subscription/email_recipient', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
}
