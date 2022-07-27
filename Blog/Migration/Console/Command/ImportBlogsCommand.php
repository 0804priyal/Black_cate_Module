<?php
namespace  Blog\Migration\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputArgument;
//use \Magefan\Blog\Model\PostFactory;
use \Rokanthemes\Blog\Model\PostFactory;



class ImportBlogsCommand extends Command
{


    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $_resourceConnection;
    /**
     * @var \Magefan\Blog\Model\PostRepository
     */
    private $magefanPostRepository;
    /**
     * @var PostFactory
     */
    private $pageFactory;
    /**
     * @var PostFactory
     */
    private $postFactory;
    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magefan\Blog\Model\PostRepositoryFactory $magefanPostRepository,
        PostFactory $postFactory,
        \Magefan\Blog\Model\CategoryFactory $categoryFactory
    )
    {
        parent::__construct();
        $this->_resourceConnection = $resourceConnection;
        $this->magefanPostRepository = $magefanPostRepository;
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
    }

    protected function configure()
    {
        $this->setName('blog:migration')->setDescription('Blog Migration from M1 to m2');
        //$this->addArgument('type', InputArgument::REQUIRED, __('Entity type is required'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->_resourceConnection->getConnection('blackold_setup');
        $output->writeln('<info>'.__('Fetching Blogs Data ').'</info>');

        /********** Post Categories *****************/
        $categoriesSql = $connection->select()
            ->from(
                ['c' => 'aw_blog_cat']
            );

        $postCategories = $connection->fetchAll($categoriesSql);
        if(count($postCategories) > 0){

            foreach($postCategories as $category){
                $categoryData = [
                    'title' => $category['title'],
                    'identifier' => $category['identifier'],
                    'sort_order' => $category['sort_order'],
                    'meta_keywords' => $category['meta_keywords'],
                    'meta_description' => $category['meta_description'],

                ];

            }

        }
        echo "<pre>";
        print_R( $postCategories); exit;



        /*********** Actual Posts *****************/

        $select = $connection->select()
            ->from(
                ['b' => 'aw_blog']
            );

        $data = $connection->fetchAll($select);

        if(count($data) > 0){

            foreach($data as $blogs){
                $blogData = [
                    'title' => $blogs['title'],
                    'content' => $blogs['post_content'],
                    'is_active' => $blogs['status'],
                    'publish_time' => $blogs['created_time'],
                    'update_time' => $blogs['update_time'],
                    'identifier' => $blogs['identifier'],
                    'meta_keywords' => $blogs['meta_keywords'],
                    'meta_description' => $blogs['meta_description'],
                    'content_heading' => $blogs['short_content']
                ];
                $postFactory = $this->postFactory->create();
                $postFactory->setData($blogData);
                try{
                   // $postRepository = $this->magefanPostRepository->create();
                    //$postRepository->save($postFactory);
                    $postFactory->save();
                }catch(\Exception $e){
                    $output->writeln('<info>'.$e->getMessage().'</info>');
                }


            }

        }

        $output->writeln('<info>'.__('Finished').'</info>');


    }

}
