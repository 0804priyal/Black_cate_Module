<?php
namespace Chilliapple\PrintOrderPdf\Model\Pdf;

use Magento\Sales\Model\Order\Pdf\AbstractPdf;

class Quote extends AbstractPdf
{


    protected $quote;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Cart $cart,
        array $data = [],
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase = null
    ) {
        $this->quote = $quoteFactory->create();
        $this->cart = $cart;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data,
            $fileStorageDatabase
        );
    }


     /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 435, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price'), 'feed' => 360, 'align' => 'right'];

        $lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
    
    protected function insertTotals($page, $quote){
        $totals = $quote->getTotals();
        

        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $totalData = $total->getData();
            $lineBlock['lines'][] = array(
                array(
                    'text'      => $total->getTitle(),
                    'feed'      => 475,
                    'align'     => 'right',
                    'font_size' => 8,
                    'font'      => 'bold'
                ),
                array(
                    'text'      => $totalData['value'],
                    'feed'      => 565,
                    'align'     => 'right',
                    'font_size' => 8,
                    'font'      => 'bold'
                ),
            );
        }
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
    /**
     * Return PDF document
     *
     * @param  array $creditmemos
     * @return \Zend_Pdf
     */
    public function getPdf($creditmemos = [])
    {        
       
        $quoteId = $this->cart->getQuote()->getId();
        $quoteObj = $this->quote->load($quoteId);
        
        //echo get_class($quoteObj); exit;
        $this->_beforeGetPdf();
        $this->_initRenderer("quote");

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

            $page = $this->newPage();            
            
            $this->insertLogo($page);
            $this->_setFontBold($page, 14);
            $page->drawText(__('Purchase Order Form'), 320, 790, 'UTF-8');
            $this->_setFontBold($page, 12);
            $headerHeadLine = __("How to submit your Purchase Order:");
            $page->drawText($headerHeadLine, 25, $this->y, "UTF-8");
            $this->y -= 12;

            $this->_setFontRegular($page);
            $headerLines = array(
                "Print out this PDF, scan it and email it to sales@blackcatmusic.co.uk, or send it by post to: ",
                "Black Cat Music, Festival House, Chapman Way, Tunbridge Wells, Kent TN2 3EF"
            );

            foreach ($headerLines as $line) {
                $page->drawText($line, 25, $this->y, "UTF-8");
                $this->y -= 10;
            }



           
            $this->insertQuote($page, $quoteObj);
           
            $this->y -=50;
            $this->_drawHeader($page);
        
            
             /* Add body */
             foreach ($quoteObj->getAllVisibleItems() as $item) {   
               
                /* Draw item */
                $this->_drawQuoteItem($item, $page, $quoteObj);
                $page = end($pdf->pages);
            }
            
            /* Add totals */
            $this->insertTotals($page, $quoteObj);
          
         $this->_afterGetPdf();
       
       return $pdf;
    }


    protected function insertQuote(&$page, $obj, $putOrderId = true)
    {
       
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        //$page->drawRectangle(25, $top, 570, $top - 155);
        //$page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        //$this->setDocHeaderCoordinates([25, $top, 570, $top - 155]);
        $this->_setFontRegular($page, 10);

        $billingAddress = $this->_formatAddress($obj->getBillingAddress()->format('pdf'));

        $shippingAddress = $this->_formatAddress($obj->getShippingAddress()->format('pdf'));
        $shippingMethod  = $obj->getShippingDescription();
        


        $this->y -= 20;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page);

        $page->drawText(_("Billing Address"), 35, $this->y , 'UTF-8');

        $page->drawText(__("Shipping Address"), 285, $this->y , 'UTF-8');

        $this->y -= 10;

        $y = $this->y - (max(count($billingAddress), count($shippingAddress)) * 10 + 15);

        $this->_setFontRegular($page);

        $addressLinesY = $this->y;
        foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $addressLinesY, 'UTF-8');
                $addressLinesY -=10;
            }
        }

        $addressLinesY = $this->y;
        foreach ($shippingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 285, $addressLinesY, 'UTF-8');
                $addressLinesY -=10;
            }

        }
        $this->y = $y;

        $this->_setFontBold($page);

        $page->drawText(__("Order Email"), 35, $this->y, 'UTF-8');

        $page->drawText(__("Purchase Order Number"), 285, $this->y, 'UTF-8');

        $this->y -= 10;
        $this->_setFontRegular($page);
        $page->drawText($obj->getCustomerEmail(), 35, $this->y, 'UTF-8');
        $page->drawText($obj->getPayment()->getPoNumber(), 285, $this->y, 'UTF-8');

        $this->y -= 15;


    }

    
    protected function _drawQuoteItem($item,$page, $quote)
    {
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setQuote($quote);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->draw();
        return $renderer->getPage();
    }

    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
