<?php
namespace Chilliapple\GoldBlocking\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Chilliapple\Core\Logger\Logger;
use Magento\Checkout\Model\Cart as CustomerCart;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{

    CONST ALL_CAPITALS = 'ALL CAPITALS';
    CONST UPPER_AND_LOWER_CASE = 'Upper and Lower Case';
    CONST LOGO_POSITION_RIGHT = 'Upper right corner';
    CONST LOGO_POSITION_LOWER = 'Lower right corner';

    CONST KEY_SECTION_A = 'section_a';
    CONST KEY_SECTION_B = 'section_b';
    CONST KEY_SECTION_B_OPTIONS = 'section_b_options';
    CONST KEY_MANUFACTURE_CUSTOM_DIE = 'manufacture_custom_die';
    CONST KEY_SECTION_C_LOGO = 'section_c_logo';
    CONST KEY_COMMENTS = 'comments';
    CONST KEY_DESCRIPTION = 'description';
    CONST KEY_CUSTOM_DIE_DESCRIPTION = 'custom_die_description';
    CONST CUSTOM_DIE_PRICE = 80;
    

    protected $context;
    protected $scopeConfig;

    protected $productOptions;

    protected $customerCart;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Option $productOptions,
        ProductRepositoryInterface $productRepository,
        CustomerCart $customerCart,
        Logger $logger
        )
    {
        parent::__construct($context);
        $this->logger = $logger;
        $this->customerCart = $customerCart;
        $this->productOptions = $productOptions;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('goldblocking/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
    public function getGoldBlockingProductId()
    {
        return $this->scopeConfig->getValue('goldblocking/general/custom_goldblock_product_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
    public function getCustomDieProductId()
    {
        return $this->scopeConfig->getValue('goldblocking/general/custom_die_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    public function loadProductById($id){
        return $this->productRepository->getById($id);
    }

    public function getGoldProductAllOptions($product){
        $productOptions = $this->productOptions->getProductOptionCollection($product);
        return $productOptions;
    }

    public function getGoldBlockProductCustomOptions($goldBlockProduct,$customDieProduct){
        $goldBlockProductOptions = $this->getGoldProductAllOptions($goldBlockProduct);
        $customDieProductOptions = $this->getGoldProductAllOptions($customDieProduct);

        $goldBlockProductOptions = $goldBlockProductOptions->getData();
        $customDieProductOptions = $customDieProductOptions->getData();
        $this->logger->info(__('----------get Mapped Options----------'));
        // Map options ids with keys
        $mappedOptionsIds = [
            self::KEY_SECTION_A => $goldBlockProductOptions[1]['option_id'],
            self::KEY_SECTION_B => $goldBlockProductOptions[2]['option_id'],
            self::KEY_SECTION_B_OPTIONS => $goldBlockProductOptions[3]['option_id'],
            self::KEY_MANUFACTURE_CUSTOM_DIE  => $goldBlockProductOptions[5]['option_id'],
            self::KEY_SECTION_C_LOGO  => $goldBlockProductOptions[4]['option_id'],
            self::KEY_COMMENTS => $goldBlockProductOptions[6]['option_id'],
            self::KEY_DESCRIPTION  => $goldBlockProductOptions[0]['option_id'],
            self::KEY_CUSTOM_DIE_DESCRIPTION => $customDieProductOptions[0]['option_id'],
        ];
        $this->logger->info(print_r($mappedOptionsIds,true));
        return $mappedOptionsIds;

    }

    public function validateGoldBlockProduct(){
        $goldBlockId = $this->getGoldBlockingProductId();
        if(!$goldBlockId){
            $this->logger->info(__('Gold Block product id not found. Please check gold block settings'));
            return false;
        }
        $product = $this->productRepository->getById($goldBlockId);
        if(!$product){
            $this->logger->info(__('Gold Block product id not found. Please check gold block settings'));
            return false;
        }
        return $product;
    }

    public function validateCustomDieProduct(){
        $customDieProductId = $this->getCustomDieProductId();
        if(!$customDieProductId){
            $this->logger->info(__('Custom die product id not found. Please check gold block settings'));
            return false;
        }
        $product = $this->productRepository->getById($customDieProductId);
        if(!$product){
            $this->logger->info(__('Custom die product id not found. Please check gold block settings'));
            return false;
        }
        return $product;
    }


    public function getBlockBuyRequest($productId){
       
        $quote = $this->customerCart->getQuote();
        if($quote->getId()):
            foreach( $quote->getItems() as $item){
            
                if($item->getProductId() == $productId){
                    return $item;
                }
            }
        endif;
        return;
    }

    /// @TODO Have to get edit data from local storage 
    public function getGoldBlockForUpdate(){
        $id = (int)$this->getRequest()->getParam('id'); // QuoteId

    }


}