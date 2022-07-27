<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\PageBuilder\Model\Config;

/**
 * Class PageBuilderConfigFactory
 * @package Aheadworks\Blog\Model
 */
class PageBuilderConfigFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create page builder config factory instance
     *
     * @return Config|null
     */
    public function create()
    {
        if (class_exists(Config::class)) {
            return $this->objectManager->create(Config::class);
        }

        return null;
    }
}
