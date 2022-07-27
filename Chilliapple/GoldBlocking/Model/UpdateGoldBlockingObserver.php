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
use \Chilliapple\GoldBlocking\Model\AddGoldBlockingObserver;
use Magento\Checkout\Model\Cart;

class UpdateGoldBlockingObserver implements ObserverInterface{    

    

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


    protected $cart; 

    protected $mappedKeys;

    protected $quoteRepository;

    public function __construct(
        Session $checkoutSession,
        Logger $logger,
        RequestInterface $requestInterface,
        ProductRepositoryInterface $productRepository,
        GoldBlockHelper $helper,
        Option $productOptions,
        JsonHelper $jsonHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        Cart $cart

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
        $parentProduct = $observer->getItem();       
       
        $request = $this->request->getParams();
        $this->logger->info(__('---------- Update GOLD BLOCK----------'));
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
     
        $this->updateGoldBlockToCart($parentProduct,$goldBlockProduct,$customDieProduct);

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

    public function getGoldBlockProductQuote($product){
        foreach($this->quote->getAllItems() as $item){
            if($item->getProductId() == $product->getId()){
                return $item;
            }
        }
        return;
    }

    public function updateGoldBlockToCart($parentProduct,$goldBlockProduct,$customDieProduct){
        $options = [];
        $productParams = [];
        $request = $this->request->getParams();
        $files = $this->request->getFiles();
        $mappedCustomOptions = $this->mappedKeys;
        
       
        $sectionBfileKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_B].'_file_action';
        $logoFileOptionKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_C_LOGO].'_file_action';
        $logoFileKey = 'options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_C_LOGO].'_file';


        $logoFileDeleteKey = 'delete-options_'.$mappedCustomOptions[GoldBlockHelper::KEY_SECTION_C_LOGO].'_file';

        /******** extract value from section a , b, c ****/
        $sectionA = implode("\n",$this->extractSectionAdata($request));
        $sectionB = $this->extractSectionBdata($request);
        $sectionC = $this->extractSectionCdata($request,$parentProduct);  // Not used in M1, have to check it 
        $this->logger->info(print_r("sections data"));
        /******** Prepare Custom option data in array****/
        $options = [
            $mappedCustomOptions[GoldBlockHelper::KEY_SECTION_A] => $sectionA,
            $mappedCustomOptions[GoldBlockHelper::KEY_DESCRIPTION] => sprintf(__('Custom Gold-Blocking for %1s.'),$parentProduct->getName()),
            $mappedCustomOptions[GoldBlockHelper::KEY_COMMENTS] => $request['gold_blocking_note']
        ];
        
        if(!empty($sectionB['logo_data'])){
             $mappedCustomOptions[GoldBlockHelper::KEY_SECTION_B_OPTIONS] = implode("\n",$sectionB['logo_data']);
        }

        if(!empty($sectionC)){
            $options[$mappedCustomOptions[GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE]] = $sectionC[GoldBlockHelper::KEY_MANUFACTURE_CUSTOM_DIE];
        }
       

        $productParams['qty'] = $request['qty'];
        $productParams['options'] = $options;
        $productParams[$sectionBfileKey]  = $request[$sectionBfileKey]; 
    
        if(empty($sectionC)){ // if "already have logo not ticked
            $productParams[$logoFileOptionKey]  = $request[$logoFileOptionKey]; 
        }


        try{          
            $item = $this->getGoldBlockProductQuote($goldBlockProduct);  
            
            $this->logger->info("Gold Price:".$item->getPrice());
           
            

            $productParams['id'] = $item->getId();
            $productParams['product'] = $goldBlockProduct->getId();
            $productParams['item'] = $item->getId();
           


            $requestData = new \Magento\Framework\DataObject($productParams);     
            
            

           
            $cartItem = $this->cart->updateItem($item->getId(),$requestData);
   
            $this->updateAdditionalData($parentProduct->getProduct(),$request,$goldBlockProduct);
           



                       
            //$this->updateAdditionalData($goldBlockProduct,$productParams);
            

            /****** Remove Custom Die Product if customer not uploaded the logo data  */        
         
            
            if(isset($request[$logoFileDeleteKey]) || !empty($sectionC)): 
                $customDieProductItem = $this->getGoldBlockProductQuote($customDieProduct);
                if($customDieProductItem){
                    $this->cart->removeItem($customDieProductItem->getId())->save();
                }
                
            endif; 

            /****** If logo added , add custom die product */

           

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

    /***
     * update "additional" field data of quote item 
     * update custom price of goldblock product
     * @quoteItem
     * @additionalData request post params
     * 
     */
    public function updateAdditionalData($quoteItem, $additionalData,$goldblockProduct = null){
      
         $quote = $this->quoteRepository->getActive($this->cart->getQuote()->getId());
        foreach($this->quote->getAllVisibleItems() as $item){
            $productId = $item->getProductId();
           
            if($productId == $quoteItem->getId()):
                $item->setAdditionalData($this->jsonHelper->jsonEncode($additionalData));
                
                
            endif;

            if($goldblockProduct):
                if($productId == $goldblockProduct->getId()):
                    $request = $additionalData;
                    $minimumPrice = \Chilliapple\GoldBlocking\Model\GoldBlockFinalPrice::MINIMUM_PRICE;
                    $price = $minimumPrice;
                    $diePrice = \Chilliapple\GoldBlocking\Helper\Data ::CUSTOM_DIE_PRICE;
                    if(!empty($request['gold_blocking_subtotal'])){
                        $qty = (int)$request['qty'];
                        $subtotal = ((float)$request['gold_blocking_subtotal']);
                        if($subtotal < $minimumPrice){             
                            
                            $price = ($minimumPrice / $qty);
                            
                        }else{
                            $price =  ($request['gold_blocking_subtotal']); 
                            
                        }
                        // when update goldblock , the key becomes updated_goldblock_dieprice
                        //if(!empty($request['updated_goldblock_dieprice'])){


                        if(!empty($request['gold_blocking_dieprice']) && $price > $minimumPrice){                            
                            

                            $price = $price - $diePrice;
                        }

                       

                        if($price > 0){
                            $item->setCustomPrice($price);
                            $item->setOriginalCustomPrice($price);
                            $item->getProduct()->setIsSuperMode(true);
                        }

                        
            
                    }
                    
                endif;
            endif; 
        }

       $this->quoteRepository->save($quote);
       return;  
    }


}