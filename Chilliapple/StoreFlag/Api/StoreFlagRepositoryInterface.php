<?php

namespace Chilliapple\StoreFlag\Api;

/**
 * @api
 */
interface StoreFlagRepositoryInterface
{
    public function save(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeFlag);

    public function getById($flagId);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    public function delete(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeFlag);

    public function deleteById($flagId);
}
