<?php
namespace  Chilliapple\CmsMetaMigration\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputArgument;

class ImportCmsMetaTitleCommand extends Command
{


    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $_resourceConnection;

    private $pageFactory;
  
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct();
        $this->_resourceConnection = $resourceConnection;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;;

    }

    protected function configure()
    {
        $this->setName('meta:migration')->setDescription('Meta Title Migration from M1 to m2');
        //$this->addArgument('type', InputArgument::REQUIRED, __('Entity type is required'));
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectionM1 = $this->_resourceConnection->getConnection('blackold_setup');
        $output->writeln('<info>'.__('Fetching Meta Title ').'</info>');

        $metaDataSql = $connectionM1->select()
            ->from(
                ['c' => 'cms_page_metatitle']
            );

        $metaData = $connectionM1->fetchAll($metaDataSql);
        $connectionM2  = $this->_resourceConnection->getConnection('blackcatmusic2');
        $tableName = $connectionM2->getTableName("cms_page");
        foreach($metaData as $meta){
            //if (in_array($meta['page_id'])){
                $data = ["meta_title"=>$meta['meta_title']];
                $where = ['page_id = ?' => (int)$meta['page_id']];
                $updatedRows = $connectionM2->update($tableName, $data, $where);
            //}
        }
        $output->writeln('<info>'.__('Finished').'</info>');


    }


}
