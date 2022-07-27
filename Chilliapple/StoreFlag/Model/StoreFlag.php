<?php

namespace Chilliapple\StoreFlag\Model;

class StoreFlag extends \Magento\Framework\Model\AbstractModel implements \Chilliapple\StoreFlag\Api\Data\StoreFlagInterface
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'chilliapple_store_flag';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'chilliapple_store_flag';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'store_flag';


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getFlagId()
    {
        return $this->getData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_ID);
    }

    public function setFlagId($flagId)
    {
        return $this->setData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_ID, $flagId);
    }

  
    public function setFlagImage($flagImage)
    {
        return $this->setData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_IMAGE, $flagImage);
    }

    /**
     * get Title
     *
     * @return string
     */
    public function getFlagImage()
    {
        return $this->getData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_IMAGE);
    }


    public function setFlagUrl($flagUrl)
    {
        return $this->setData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_URL, $flagUrl);
    }

    /**
     * get Image
     *
     * @return string
     */
    public function getFlagUrl()
    {
        return $this->getData(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::FLAG_URL);
    }

}
