<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Chilliapple\SubscriptionNotify\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class PredispatchNewsletterObserver
 */
class SubscriptionNotifySaveAfter implements ObserverInterface
{
    private $configHelper;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    private $header;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $http;

    private $helper;
    private $helperData;

    private $storeManager;
    protected $scopeConfig;
    private $messageManager;

    private $url;

    public function __construct(StoreManagerInterface $storeManagerInterface,
         UrlInterface $url,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Request\Http $http,
        \Magento\Framework\HTTP\Header $header,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Chilliapple\SubscriptionNotify\Helper\Data $helperData
    )
    {
        $this->http  = $http;
        $this->header = $header;
        $this->redirect = $redirect;
        $this->timezone = $timezone;
        $this->url = $url;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->helperData = $helperData;
        $this->storeManagerInterface= $storeManagerInterface;
    }

    /**
     * Redirect newsletter routes to 404 when newsletter module is disabled.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {

        $subscriber = $observer->getEvent()->getSubscriber();
        $email = $subscriber->getEmail();
        $sentToEmail = $this->helperData->getEmailRecipient();
        $emailTemplate = $this->helperData->getEmailTemplate();
        $isEnabled = $this->helperData->getEmailEnable();

        if($isEnabled)
        {
            try
            {
                // Send Mail
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                 
                $sender = [
                    'name' => $sentToEmail,
                    'email' => $sentToEmail
                ];
                 
                 
                $transport = $this->_transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                    )
                    ->setTemplateVars([
                        'email'  => $sentToEmail
                    ])
                    ->setFrom($sender)
                    ->addTo($email)
                    ->getTransport();
                     
                    $transport->sendMessage();
                    $this->messageManager->addSuccess('Email sent successfully');
                     
            } catch(\Exception $e)
            {
                $this->messageManager->addError($e->getMessage());
            }
        }     
          return $this;
        
    }
}
