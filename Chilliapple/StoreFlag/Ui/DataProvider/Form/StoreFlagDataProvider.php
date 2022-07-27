<?php

namespace Chilliapple\StoreFlag\Ui\DataProvider\Form;

use \Magento\Framework\App\Request\DataPersistorInterface;
use \Magento\Framework\App\ObjectManager;
use \Chilliapple\StoreFlag\Model\StoreFlag\FileInfo as FileInfo;

class StoreFlagDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    const MEDIA_THUMB_PATH = \Chilliapple\StoreFlag\Ui\Component\Listing\Columns\MediaThumbnail::THUMB_PATH;

    protected $loadedData;

    protected $collection;


    private $dataPersistor;

    protected $_storeManager;

    private $fileInfo;

    protected $formElements= [
        'flag_url' => 'input',
        'flag_image' => 'fileUploader',
    ];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag\CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager = $storeManager;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->prepareMeta($meta);
        return $meta;
    }

    public function prepareMeta($meta)
    {
        $meta = array_replace_recursive($meta, $this->prepareFieldsMeta(
            $this->getFieldsMap()
        ));

        return $meta;
    }

    private function prepareFieldsMeta($fieldsMap)
    {
        $result = [];


        foreach ($fieldsMap as $fieldSet => $fields) {


            foreach ($fields as $field) {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config']['formElement'] = $this->formElements[$field];

                }
            }

        return $result;
    }
    /**
     * @return array
     * @since 101.0.0
     */
    protected function getFieldsMap()
    {
        return [
            'general' => [
                'flag_image',
                'flag_url',
            ],
            'select_products' => [
            ]

        ];
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


        foreach ($items as $model) {
            $data = $model->getData();

            $data = $this->convertValues($model, $data);

            $this->loadedData[$model->getId()] = $data;

        }

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('chilliapple_store_flag');
        }

        return $this->loadedData;
    }

    private function convertValues($storeFlag, $storeFlagData)
    {


        $flagImage = 'flag_image';
        $flagData = $storeFlag->getData();

        $fileName = $flagData[$flagImage];

        if (!empty($fileName)) {
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_THUMB_PATH;

            $stat = $this->getFileInfo()->getStat(self::MEDIA_THUMB_PATH,$fileName);
            $mime = $this->getFileInfo()->getMimeType(self::MEDIA_THUMB_PATH,$fileName);


            unset($storeFlagData['flag_image']);
            $storeFlagData[$flagImage][0]['name'] = $fileName;
            $storeFlagData[$flagImage][0]['url'] = $mediaUrl . $fileName;
            $storeFlagData[$flagImage][0]['size'] = isset($stat) ? $stat['size'] : 0;
            $storeFlagData[$flagImage][0]['type'] = $mime;
        }


        return $storeFlagData;
    }

    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }

}
