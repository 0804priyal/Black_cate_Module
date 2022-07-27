<?php

namespace Chilliapple\StoreFlag\Model\StoreFlag;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Loaded data cache
     * 
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     * 
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
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
       
        foreach ($items as $level) {
               
                
            $this->loadedData[$level->getId()] = $level->getData();
               
        }
        $data = $this->dataPersistor->get('chilliapple_store_flag');
        if (!empty($data)) {
            $level = $this->collection->getNewEmptyItem();
            $level->setData($data);
            $this->loadedData[$level->getId()] = $level->getData();
            $this->dataPersistor->clear('chilliapple_store_flag');
        }
        return $this->loadedData;
    }
}
