<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;

class Edit extends \Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag
{
    CONST REGISTRY_KEY = 'flag_id';
    
    protected function initStoreFlag()
    {
        $flagId = $this->getRequest()->getParam('flag_id');
        $this->coreRegistry->register(self::REGISTRY_KEY, $flagId);

        return $flagId;
    }

    public function execute()
    {
        $flagId = $this->initStoreFlag();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Chilliapple_StoreFlag::storeflag_storeflag');
        $resultPage->getConfig()->getTitle()->prepend(__('Flag'));
        $resultPage->addBreadcrumb(__('Flag'), __('Flag'));
        $resultPage->addBreadcrumb(__('Flag'), __('Flag '), $this->getUrl('storeflag/storeflag'));

        if ($flagId === null) {
            $resultPage->addBreadcrumb(__('New Flag'), __('New Flag'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Flag'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Flag'), __('Edit Flag'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->storeFlagRepository->getById($flagId)->getTitle()
            );
        }
        return $resultPage;
    }
}
