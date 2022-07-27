<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Setup\Updater;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;

/**
 * Class Data
 * @package Aheadworks\Blog\Setup\Updater
 */
class Data
{
    /**
     * Get full path for category
     *
     * @param array $categories
     * @param int $categoryId
     * @param bool $isInitial
     * @return string
     */
    private function resolvePath($categories, $categoryId, $isInitial = false)
    {
        $path = '';
        foreach ($categories as $category) {
            if ($category['id'] != $categoryId) {
                continue;
            }
            $parentId = $category['parent_id'];
            if ($isInitial && $parentId == 0) {
                return $categoryId;
            }
            if ($parentId != 0) {
                $categoryPath = $this->resolvePath($categories, $parentId);
                if ($categoryPath) {
                    $path .= $categoryPath . '/';
                }
                $path .= $parentId . ($isInitial ? '/' : '');
                if ($isInitial) {
                    $path .= $categoryId;
                    break;
                }
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * Update category path
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    public function updatePath(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $select = $connection->select()
            ->from([ResourceCategory::BLOG_CATEGORY_TABLE]);

        $categories = $connection->fetchAll($select);
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $categoryPath = $this->resolvePath($categories, $categoryId, true);
            $connection->update(
                [ResourceCategory::BLOG_CATEGORY_TABLE],
                [CategoryInterface::PATH => $categoryPath],
                [CategoryInterface::ID . ' = ?' => $categoryId]
            );
        }

        return $this;
    }
}
