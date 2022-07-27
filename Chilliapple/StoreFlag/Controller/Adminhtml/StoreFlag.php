<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml;

abstract class StoreFlag extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    protected $storeFlagRepository;

    protected $resultPageFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface $storeFlagRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->coreRegistry        = $coreRegistry;
        $this->storeFlagRepository = $storeFlagRepository;
        $this->resultPageFactory   = $resultPageFactory;
        parent::__construct($context);
    }
}
