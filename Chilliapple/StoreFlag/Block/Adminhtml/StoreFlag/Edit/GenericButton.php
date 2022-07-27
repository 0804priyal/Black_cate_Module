<?php
namespace Chilliapple\StoreFlag\Block\Adminhtml\StoreFlag\Edit;

use \Magento\Search\Controller\RegistryConstants;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    protected $storeFlagRepository;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Chilliapple\StoreFlag\Api\StoreFlagRepositoryInterface $storeFlagRepository
    ) {
        $this->context  = $context;
        $this->registry = $registry;
        $this->storeFlagRepository = $storeFlagRepository;
    }

    public function getFlagId()
    {
        try {
            return $this->storeFlagRepository->getById(
                $this->context->getRequest()->getParam('flag_id')
            )->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}