<?php

namespace Chilliapple\StoreFlag\Model;
use \Magento\Framework\Api\SearchCriteriaInterface;

class StoreFlagRepository implements \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface
{
    /**
     * Cached instances
     * 
     * @var array
     */
    protected $instances = [];


    protected $resource;

    protected $storeFlagCollectionFactory;

    protected $storeFlagInterfaceFactory;

    protected $dataObjectHelper;


    protected $searchResultsFactory;

    public function __construct(
        \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag $resource,
        \Chilliapple\StoreFlag\Model\ResourceModel\StoreFlag\CollectionFactory $storeFlagCollectionFactory,
        \Chilliapple\StoreFlag\Api\Data\StoreFlagInterfaceFactory $storeFlagInterfaceFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Chilliapple\StoreFlag\Api\Data\StoreFlagSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                   = $resource;
        $this->storeFlagCollectionFactory = $storeFlagCollectionFactory;
        $this->storeFlagInterfaceFactory  = $storeFlagInterfaceFactory;
        $this->dataObjectHelper           = $dataObjectHelper;
        $this->searchResultsFactory       = $searchResultsFactory;
    }

    public function save(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeFlag)
    {
        try {
            $this->resource->save($storeFlag);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__(
                'Could not save the Group: %1',
                $exception->getMessage()
            ));
        }
        return $storeFlag;
    }

    public function getById($flagId)
    {
        if (!isset($this->instances[$flagId])) {
            $storeFlag = $this->storeFlagInterfaceFactory->create();
            $this->resource->load($storeFlag, $flagId);
            if (!$storeFlag->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested BannerGroup doesn\'t exist'));
            }
            $this->instances[$flagId] = $storeFlag;
        }
        return $this->instances[$flagId];
    }

    /**
     * Retrieve Categories matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Rugs\Banner\Api\Data\BannerGroupSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Rugs\Banner\Api\Data\BannerGroupSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->bannerSliderCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'flag_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());


        $flag = [];

        foreach ($collection as $flag) {

            $StoreFlagDataObject = $this->storeFlagInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $StoreFlagDataObject,
                $flag->getData(),
                \Chilliapple\StoreFlag\Api\Data\StoreFlagInterface::class
            );
            $flag[] = $StoreFlagDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($banners);
    }


    public function delete(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeFlag)
    {
        $id = $storeFlag->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($storeFlag);
        } catch (\Magento\Framework\Exception\ValidatorException $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove Group %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }


    public function deleteById($storeFlag)
    {
        $flag = $this->getById($storeFlag);
        return $this->delete($flag);
    }

}
