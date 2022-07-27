<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_VatValidator
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\VatValidator\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Vat extends \Magento\Framework\App\Action\Action
{
    public $customerSessionProxy;
    public $address;
    public $customerVat;

    /**
     * Vat constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param \Magento\Customer\Model\Address $address
     * @param \Magento\Customer\Model\Vat $customerVat
     */

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        \Magento\Customer\Model\Address $address,
        \Magento\Customer\Model\Vat $customerVat
    ) {
        $this->customerSession = $customerSession;
        $this->address = $address;
        $this->customerVat = $customerVat;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        /** @var $post */
        $post = $this->getRequest()->getPost();
        if (isset($post['vat']) && $post['vat']) {
            if (array_key_exists('country', $post)) {
                $country_code = $post['country'];
            } else {
                $sessionData = $this->customerSession;
                if ($sessionData->getCustomer()->getId()) {
                    $billingID =  $sessionData->getCustomer()->getDefaultBilling();
                    $address = $this->address->load($billingID);
                    $addressData = $address->getData();
                    if (array_key_exists('country_id', $addressData)) {
                        $country_code = $addressData['country_id'];
                    }
                }
            }
            
            $vat_num = $post['vat'];
            $address = $this->customerVat->checkVatNumber($country_code, $vat_num);
        }
        
        if (isset($address['is_valid']) && $address['is_valid']) {
            return $this->getResponse()->setBody('done');
        } else {
            return $this->getResponse()->setBody('fail');
        }
    }
}
