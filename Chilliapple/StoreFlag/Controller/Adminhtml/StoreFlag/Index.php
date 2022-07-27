<?php
namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{

    protected $productFactory;
    protected $_urlInterface;
    protected $escaper;
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function __construct( \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Escaper $escaper,
       \Magento\Framework\Json\Helper\Data $jsonHelper)
    {
            
            parent::__construct($context);
            $this->escaper = $escaper;
            $this->productFactory = $productFactory;
            $this->jsonHelper = $jsonHelper;
    }

    public function execute()
    {
    
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Chilliapple_StoreFlag::storeflag_storeflag');
        $resultPage->getConfig()->getTitle()->prepend(__('Store Flag'));
        return $resultPage;
    }
         
}
