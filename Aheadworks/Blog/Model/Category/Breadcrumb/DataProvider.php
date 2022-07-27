<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Model\Category\Breadcrumb;

use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\Data\Category;
use Aheadworks\Blog\Model\Url;

/**
 * Class DataProvider
 * @package Aheadworks\Blog\Model\Category\Breadcrumb
 */
class DataProvider
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Url $url
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Url $url,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->url = $url;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Return parent categories
     *
     * @param string $categoryPath
     * @return CategoryInterface[]
     * @throws LocalizedException
     */
    public function getParentCategories($categoryPath)
    {
        $categoryPathIds = explode('/', $categoryPath);
        $pathOrder = $this->sortOrderBuilder
            ->setField(CategoryInterface::PATH)
            ->setAscendingDirection()
            ->create();
        $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::ID, $categoryPathIds, 'in')
            ->addSortOrder($pathOrder);

        return $this->categoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * Return current category path or get it from current category
     *
     * @param Category $category
     * @return array
     * @throws LocalizedException
     */
    public function getBreadcrumbPath($category)
    {
        $parentCategories = $this->getParentCategories($category->getPath());
        $breadcrumbPath = [];
        foreach ($parentCategories as $parentCategory) {
            $breadcrumbPath[] = [
                'label' => $parentCategory->getName(),
                'link' => $parentCategory->getId() != $category->getId() ?
                    $this->url->getCategoryUrl($parentCategory)
                    : ''
            ];
        }

        return $breadcrumbPath;
    }
}
