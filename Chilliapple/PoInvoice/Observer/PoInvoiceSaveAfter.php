<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Chilliapple\PoInvoice\Observer;
use Magento\Framework\Event\ObserverInterface;

class PoInvoiceSaveAfter implements ObserverInterface
{
    protected $orderFactory;

    public function __construct(\Magento\Sales\Model\Service\InvoiceService $invoiceService,
    \Magento\Framework\DB\TransactionFactory $transactionFactory)
    {
        $this->_invoiceService = $invoiceService;
       $this->_transactionFactory = $transactionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getEvent()->getOrder();
        $method = $order->getPayment()->getMethod();
        if ($method == \Magento\OfflinePayments\Model\Purchaseorder::PAYMENT_METHOD_PURCHASEORDER_CODE){
        $order->setStatus(\Chilliapple\PoInvoice\Setup\Patch\Data\AddPurchaseOrderStatuses::PURCHASEORDER_STATUS);
        $order->addStatusToHistory($order->getStatus(), __('Order status has been changed to Purchase Order.'));
        $order->save();
        try {
            if(!$order->canInvoice()) {
                return null;
            }
/*            if(!$order->getState() == 'new') {
                return null;
            }*/

            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();

            $transaction = $this->_transactionFactory->create()
              ->addObject($invoice)
              ->addObject($invoice->getOrder());

            $transaction->save();

            } 
            catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
          }
        return $this;
        }
    }

}
