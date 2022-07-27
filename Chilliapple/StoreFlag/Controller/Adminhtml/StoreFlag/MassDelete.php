<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;

class MassDelete extends \Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag\MassAction
{
    
    protected function massAction(\Chilliapple\StoreFlag\Api\Data\StoreFlagInterface $storeflag)
    {
        $this->storeflagRepository->delete($storeflag);
        return $this;
    }
}
