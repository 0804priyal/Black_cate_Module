<?php

namespace Chilliapple\GoldBlocking\Model;
use \Chilliapple\GoldBlocking\Helper\Data as GoldBlockHelper;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Chilliapple\Core\Logger\Logger;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Catalog\Model\Product\Option;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Magento\Checkout\Model\CartFactory;

class AddGoldBlockingObserver implements ObserverInterface{ 

 
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $productOptions;


    protected $sectionsData;


    protected $mappedKeys;

    protected $quoteRepository;

    protected $cart;
    public function __construct(
        Session $checkoutSession,
        Logger $logger,
        RequestInterface $requestInterface,
        ProductRepositoryInterface $productRepository,
        GoldBlockHelper $helper,
        Option $productOptions,
        JsonHelper $jsonHelper,
         \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
         CartFactory $cart

    ){
         $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->quote = $checkoutSession->getQuote();
        $this->logger = $logger;
        $this->request = $requestInterface;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->productOptions = $productOptions;
        $this->jsonHelper = $jsonHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $parentProduct = $observer->getEvent()->getProduct();
        $request = $this->request->getParams();
        $fileRequest = $this->request->getFiles();
        if(empty($request['require_gold_blocking'])){
            return;
        }
        $goldBlockProduct = $this->helper->validateGoldBlockProduct();
        if(!$goldBlockProduct){
            return;
        }
        $customDieProduct = $this->helper->validateCustomDieProduct();
        if(!$customDieProduct){
            return;
        }
        $this->mappedKeys = $this->helper->getGoldBlockProductCustomOptions($goldBlockProduct,$customDieProduct);
        $this->addGoldBlockToCart($parentProduct,$goldBlockProduct,$customDieProduct);

        return $this;
    }
    /** retrive section A data
     * @param $request
     * @return array
     */
    public function extractSectionAdata($request){
        $sectionA = [];
        $i=1;
        foreach($request['sectionA'] as $key=>$section){
            if(trim($section)){
                $sectionA[$key] = sprintf("Line %1s: %2s",$i,$section);
            }
            $i++;
        }
        $sectionA['letterCase'] = sprintf("Letter Case: %1s",GoldBlockHelper::UPPER_AND_LOWER_CASE);
        if(!empty($request['sectionA']['letterCase'])){
            if($request['sectionA']['letterCase'] == 1){
                $sectionA['letterCase'] = sprintf("Letter Case: %1s",GoldBlockHelper::ALL_CAPITALS);
            }
        }
        return $sectionA;
    }

    /** Retrive section b data
     * @param $request
     * @return array
     */
    public function extractSectionBdata($request){
        $sectionB = [];
     
        $mappedCustomOptions = $this->mappedKeys;
        if (!empty($request['sectionB']))
        {
            $secB = $request['sectionB'];
            $sectionB['logo_data'] = [];
            $sectionB['logo_data']['letterCase'] = sprintf("Letter case: %1s",GoldBlockHelper::UPPER_AND_LOWER_CASE);
            
            $letterCase = !empty($secB['letterCase']) ? $secB['letterCase'] : '';

            if ($letterCase == 1) {
                $sectionB['logo_data']['letterCase'] = sprintf("Letter case: %1s",GoldBlockHelper::ALL_CAPITALS);
            }
            $sectionB['logo_data']['position'] = sprintf("Position: %1s",GoldBlockHelper::LOGO_POSITION_LOWER);

            $position = !empty($secB['position']) ? $secB['position'] : '';

            if ($position==1) {
                $sectionB['logo_data']['position'] = sprintf("Position: %1s",GoldBlockHelper::LOGO_POSITION_RIGHT);
            }
        }

        return $sectionB;
    }

    /** retrive section C data
     * @param $request
     */
    public function extractSectionCdata($request,$mainProduct){
        $sectionC = [];
        $mappedCustomOptions = $this->mappedKeys;
        $files = $this->request->getFiles();      

        if (!empty($request['sectionC']))
        {
            $tmp = $request['sectionC'];

            if (!empty($tmp['alreadyHaveLogo'])):
                    //$sectionC['options_data'][GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE] = 'We already have artwork on file (we have created a die in the past)';
                    $sectionC[GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE] = 'We already have artwork on file (we have created a die in the past)';
               
            endif;
        }
        return $sectionC;
    }


    public function addGoldBlockToCart($parentProduct,$goldBlockProduct,$customDieProduct){
        $options = [];
        $productParams = [];
        $request = $this->request->getParams();
        $files = $this->request->getFiles();
        $mappedCustomOptions = $this->mappedKeys;
        
        $sectionBfileKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_B].'_file_action';
        $logoFileOptionKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_C_LOGO].'_file_action';
        $logoFileKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_C_LOGO].'_file';

        /******** extract value from section a , b, c ****/
        $sectionA = implode("\n",$this->extractSectionAdata($request));
        $sectionB = $this->extractSectionBdata($request);
        $sectionC = $this->extractSectionCdata($request,$parentProduct);  // Not used in M1, have to check it 
       


        /******** Prepare Custom option data in array****/
        $options = [
            $mappedCustomOptions[GoldBlockHelper::KEY_SECTION_A] => $sectionA,
            $mappedCustomOptions[GoldBlockHelper::KEY_DESCRIPTION] => sprintf(__('Custom Gold-Blocking for %1s.'),$parentProduct->getName()),
            $mappedCustomOptions[GoldBlockHelper::KEY_COMMENTS] => $request['gold_blocking_note']
        ];

        if(!empty($sectionB)):
            $options[$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_B_OPTIONS]] = implode("\n",$sectionB['logo_data']);
        endif;

        if(!empty($sectionC)){
            $options[$mappedCustomOptions[GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE]] = $sectionC[GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE];
        }


        $productParams['qty'] = $request['qty'];
        $productParams['price'] = (int)($request['gold_blocking_subtotal'] * $request['qty']);
        $productParams['options'] = $options;
        $productParams[$sectionBfileKey]  = $request[$sectionBfileKey];

        if(empty($sectionC)){ // if "already have logo not ticked
            $productParams[$logoFileOptionKey]  = $request[$logoFileOptionKey]; 
        }
        
      
        try{          
            $customerCart  = $this->cart->create();
            $requestData = new \Magento\Framework\DataObject($productParams);   
            $customerCart->addProduct($goldBlockProduct,$requestData);
            
            $this->updateAdditionalData($parentProduct,$request,$goldBlockProduct);
            

            $customerCart->save();

            
            
            $this->_checkoutSession->setLastAddedProductId($goldBlockProduct->getId());

            /****** Add Custom Die Product if customer uploaded the logo data  */        
            if(!empty($files[$logoFileKey]['name']) && empty($sectionC)):             
                $optionsForCustomData = [
                    $mappedCustomOptions[GoldBlockHelper::KEY_CUSTOM_DIE_DESCRIPTION] => sprintf('Manufacture of custom die for %1s',$parentProduct->getName())
               ];
               $customDieParams = array(
                    'qty' => 1,
                    'options'=> $optionsForCustomData
                );
                $customDieData = new \Magento\Framework\DataObject($customDieParams);
                $this->quote->addProduct($customDieProduct,$customDieData);
                //$this->updateAdditionalData($customDieProduct,$customDieParams);
                $this->cart->create()->save();

                $this->_checkoutSession->setLastAddedProductId($customDieProduct->getId());

            endif;

        } catch (\Magento\Framework\Exception\LocalizedException $e){
            $this->logger->info("Gold Blocking Error:".$e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));

        }
 
        $quote = $this->quote;
        $quote->setTriggerRecollect(1); 
        $quote->collectTotals();
        $quote->save();

        return $this;

    }


    public function updateAdditionalData($quoteItem, $additionalData,$goldBlockProduct=null){
      $cartId = $this->cart->create()->getQuote()->getId();     
      $quote = $this->quoteRepository->getActive($cartId);
        foreach($this->quote->getAllVisibleItems() as $item){
            $productId = $item->getProductId();
            if($productId == $quoteItem->getId()):         
                 $item->setAdditionalData($this->jsonHelper->jsonEncode($additionalData));
                 $item->save();
                 return;
            endif;
            
        }
             
        $this->quoteRepository->save($quote);
        
        
    }


}
