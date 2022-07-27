<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Ui\Component\Form;

use Magento\Ui\Component\Form\Field;
use Magento\PageBuilder\Model\Config;
use Aheadworks\Blog\Model\PageBuilderConfigFactory;

/**
 * Class FieldPlugin
 * @package Aheadworks\Blog\Ui\Component\Form
 */
class FieldPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Fields name
     *
     * @var array
     */
    private $fields = ['short_content', 'content'];

    /**
     * @param PageBuilderConfigFactory $configFactory
     */
    public function __construct(PageBuilderConfigFactory $configFactory)
    {
        $this->config = $configFactory->create();
    }

    /**
     * @param Field $subject
     * @return Field
     */
    public function beforePrepare(Field $subject)
    {
        if (in_array($subject->getData('name'), $this->fields)
            && is_object($this->config)
            && $this->config->isEnabled()
            && $subject->getData('config/formElement') == 'wysiwyg'
        ) {
            $config = $subject->getData('config');
            $config['component'] = 'Aheadworks_Blog/js/ui/form/element/wysiwyg';
            $subject->setData('config', $config);
        }

        return $subject;
    }
}
