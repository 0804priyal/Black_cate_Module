<?php
namespace Chilliapple\PrintOrderPdf\Controller\PrintPdf;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
 
class Index extends \Magento\Framework\App\Action\Action 
{
       protected $_resultPageFactory;

       protected $pdfQuote;

       protected $date;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Chilliapple\PrintOrderPdf\Model\Pdf\QuoteFactory $pdfQuote,        
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->date = $date;
        $this->pdfQuote = $pdfQuote;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);

    }

    public function execute()
    {

        $pdf = $this->pdfQuote->create()->getPdf([]);
        $date = $this->date->date('Y-m-d_H-i-s');
        return $this->_fileFactory->create(
            __('order') . '_' . $date . '.pdf',
            $pdf->render(),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/pdf'
        );

       
    }
}