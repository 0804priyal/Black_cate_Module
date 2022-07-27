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

class GoldBlockFinalPrice implements ObserverInterface{ 

    protected $requestInterface;
    protected $checkoutSession;
    protected $logger;
    protected $helper;
    CONST MINIMUM_PRICE = 10;

    public function __construct(
        Session $checkoutSession,
        Logger $logger,
        RequestInterface $requestInterface,
        GoldBlockHelper $helper
    ){
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->request = $requestInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {  
        $request = $this->request->getParams();
        $quote_item = $observer->getEvent()->getQuoteItem();
        $goldBlockProduct = $this->helper->validateGoldBlockProduct();
        $productId = $quote_item->getProduct()->getId();    
        $price = self::MINIMUM_PRICE;
        if(!empty($request['gold_blocking_subtotal'])){
            $subtotal = ((float)$request['gold_blocking_subtotal'] * $request['qty']);
            if($subtotal < self::MINIMUM_PRICE){             
                
                $price = (self::MINIMUM_PRICE / $request['qty']);
            }else{
                $price =  ($request['gold_blocking_subtotal'] / $request['qty']); 
            }

        }
        
        if(!empty($request['gold_blocking_dieprice'])){
            $price = $price - (int)$request['gold_blocking_dieprice'];
        }
        //echo "<Pre>";
       // print_r($request);
        //echo $price; exit;
        if(!$goldBlockProduct){
            return;
        }
        $goldblockId = $goldBlockProduct->getId();
        if($goldblockId == $productId){ 
            $quote_item->setCustomPrice($price);
            $quote_item->setOriginalCustomPrice($price);
            $quote_item->getProduct()->setIsSuperMode(true);            
        }
       
        return $this;
      
    }

}
