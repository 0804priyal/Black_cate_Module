<?php
namespace Chilliapple\Tabs\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $filterProvider;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
        
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context);
    }

    public function getWysiwygFilter(){

    	$filterManager = $this->filterProvider->getPageFilter();
    	return $filterManager;
    }
}

    