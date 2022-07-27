<?php

namespace Chilliapple\StoreFlag\Api\Data;

/**
 * @api
 */
interface StoreFlagSearchResultInterface
{
    public function getItems();


    public function setItems(array $items);
}
