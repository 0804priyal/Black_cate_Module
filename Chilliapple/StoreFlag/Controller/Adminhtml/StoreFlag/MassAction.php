<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;

abstract class MassAction extends \Magento\Backend\App\Action
{
 
    protected $storeFlagRepository;

    protected $filter;

    protected $collectionFactory;

    protected $successMessage;


    protected $errorMessage;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface $storeFlagRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag\CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->storeFlagRepository = $storeFlagRepository;
        $this->filter              = $filter;
        $this->collectionFactory   = $collectionFactory;
        $this->successMessage      = $successMessage;
        $this->errorMessage        = $errorMessage;
        parent::__construct($context);
    }


    abstract protected function massAction(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeFlag);

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $storeFlag) {
                $this->massAction($storeFlag);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('storeflag/*/index');
        return $redirectResult;
    }
}
