<?php

namespace Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag;

class Delete extends \Chilliapple\StoreFlag\Controller\Adminhtml\StoreFlag
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('flag_id');
        if ($id) {
            try {
                $this->storeFlagRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Flag has been deleted.'));
                $resultRedirect->setPath('storeflag/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Flag no longer exists.'));
                return $resultRedirect->setPath('storeflag/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('storeflag/storeflag/edit', ['flag_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Flag'));
                return $resultRedirect->setPath('storeflag/storeflag/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Flag to delete.'));
        $resultRedirect->setPath('storeflag/*/');
        return $resultRedirect;
    }
}
