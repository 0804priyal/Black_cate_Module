<?php
namespace Chilliapple\StoreFlag\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag\CollectionFactory;
use \Magento\Framework\App\ObjectManager;
use \Magento\Framework\App\Request\DataPersistorInterface;
use \Magento\Ui\DataProvider\Modifier\PoolInterface;
use \Magento\Framework\AuthorizationInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var AuthorizationInterface
     */
    private $auth;


    protected $registry;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     * @param AuthorizationInterface|null $auth
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null,
        ?AuthorizationInterface $auth = null,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->auth = $auth ?? ObjectManager::getInstance()->get(AuthorizationInterface::class);
        $this->meta = $this->prepareMeta($this->meta);
        $this->registry = $registry;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    public function getCurrentStoreFlag(){
        $flag = $this->registry->registry('flag_id');
        if ($flag) {
            return $flag;
        }
        return  $flag;
    }


    private function convertValues($currentFlag,$flagData)
    {
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageDirectory = \Chilliapple\StoreFlag\Model\ImageUploader::STORE_FLAG_IMAGE_PATH;
        $desktopImageArray = [
                [
                    'name' => $currentFlag->getData('desktop_image'),
                    'url' => $baseurl.$imageDirectory.'/'.$currentFlag->getData('desktop_image')
                ]
        ];

        $mobileImageArray = [
            [
                'name' => $currentFlag->getData('mobile_image'),
                'url' => $baseurl.$imageDirectory.'/'.$currentFlag->getData('mobile_image')
            ]
        ];

        $flagData['desktop_image'] = $desktopImageArray;
        $flagData['mobile_image'] = $mobileImageArray;

        return $flagData;
    }
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {


        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $page \Magento\Cms\Model\Page */
        foreach ($items as $page) {
            $this->loadedData[$page->getFlagId()] = $page->getData();
        }

        $currentFlag = $this->getCurrentStoreFlag();

        if($currentFlag->getId()){

            $flagData = $currentFlag->getData();

            $flagData = $this->convertValues($currentFlag, $flagData);

            $this->loadedData[$currentFlag->getFlagId()] = $flagData;

        }



        $data = $this->dataPersistor->get('chilliapple_store_flag');
        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();
            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('chilliapple_store_flag');
        }

        return $this->loadedData;
    }

}
