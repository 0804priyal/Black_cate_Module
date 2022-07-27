<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Chilliapple\PoInvoice\Setup\Patch\Data;

use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddPurchaseOrderStatuses implements DataPatchInterface, PatchVersionInterface
{
    
    const PURCHASEORDER_STATUS = 'purchase_order';

    private $moduleDataSetup;

    private $quoteSetupFactory;

    private $salesSetupFactory;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }


    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $quoteInstaller = $this->quoteSetupFactory->create();
        $salesInstaller = $this->salesSetupFactory->create();
        $data = [];
        $statuses = [
            self::PURCHASEORDER_STATUS => __('Purchase Order'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }
    public static function getDependencies()
    {
        return [];
    }

    public static function getVersion()
    {
        return '2.0.0';
    }
    public function getAliases()
    {
        return [];
    }
}
