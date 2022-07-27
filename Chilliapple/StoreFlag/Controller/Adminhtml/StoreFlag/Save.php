<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;

use \Magento\Backend\App\Action;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Request\DataPersistorInterface;
use \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface;
use \Chilliapple\StoreFlag\Model\StoreFlagFactory;


/**
* Class Save
* @SuppressWarnings(PHPMD.CouplingBetweenObjects)
*/
class Save extends \Magento\Backend\App\Action
{
/**
* @var DataPersistorInterface
*/
protected $dataPersistor;

/**
* @var StoreManagerInterface
*/
private $storeManager;

protected $repository;

protected $storeFlagFactory;

protected $fileSystem;

/**
* Save constructor.
*
* @param Action\Context $context
* @param Builder $productBuilder
* @param Initialization\Helper $initializationHelper
* @param \Magento\Catalog\Model\Product\Copier $productCopier
* @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
* @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
*/
        public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface $repository,
            \Chilliapple\StoreFlag\Model\StoreFlagFactory $storeFlagFactory
        )
        {
            parent::__construct($context);
            $this->repository = $repository;
            $this->storeFlagFactory = $storeFlagFactory;
        }

        public function execute()
    {
/*        echo "<pre>====";print_r($_REQUEST);
        die;*/

        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->storeFlagFactory->create();
                $data = $this->getRequest()->getPostValue();

                $data = $this->setFlagImageData($data);

                $model->addData($data);


                $this->repository->save($model);


                $this->messageManager->addSuccessMessage(__('You saved the item.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);

                    return;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set('storeModelData', $data);

                if ($productVideoId = (int)$this->getRequest()->getParam('flag_id')) {
                    $this->_redirect('*/*/edit', ['id' => $productVideoId]);
                } else {
                    $this->_redirect('*/*/new');
                }

                return;
            }
        }
        $this->_redirect('*/*/');
    }

        public function setFlagImageData(array $rawData)
        {
            $data = $rawData;
            if (isset($data['flag_image'][0]['file'])) {
                $data['flag_image'] = $data['flag_image'][0]['file'];
            } else {
                $data['flag_image'] = null;
            }
            return $data;
        }
}
