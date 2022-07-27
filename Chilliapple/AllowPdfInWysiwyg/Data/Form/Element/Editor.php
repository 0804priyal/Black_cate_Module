<?php
namespace Chilliapple\AllowPdfInWysiwyg\Data\Form\Element;
use Magento\Framework\Escaper;

class Editor extends \Magento\Framework\Data\Form\Element\Editor
{
    private $serializer;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        if ($this->isEnabled()) {
            $this->setType('wysiwyg');
            $this->setExtType('wysiwyg');
        } else {
            $this->setType('textarea');
            $this->setExtType('textarea');
        }
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * Returns buttons translation
     *
     * @return array
     */
   
    protected function _getPluginButtonsHtml($visible = true)
    {
        $buttonsHtml = '';

        // Button to widget insertion window
        if ($this->getConfig('add_widgets')) {
            $buttonsHtml .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Insert Widget...'),
                    'onclick' => "widgetTools.openDialog('"
                        . $this->getPluginConfigOptions('magentowidget', 'window_url')
                        . "widget_target_id/" . $this->getHtmlId() . "/')",
                    'class' => 'action-add-widget plugin',
                    'style' => $visible ? '' : 'display:none',
                ]
            );
        }

        // Button to media images insertion window
        if ($this->getConfig('add_images')) {
            $buttonsHtml .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Insert Image...'),
                    'onclick' => "MediabrowserUtility.openDialog('"
                        . $this->getConfig('files_browser_window_url')
                        . "target_element_id/" . $this->getHtmlId() . "/"
                        . (null !== $this->getConfig('store_id') ? 'store/'
                            . $this->getConfig('store_id') . '/' : '')
                        . "')",
                    'class' => 'action-add-image plugin',
                    'style' => $visible ? '' : 'display:none',
                ]
            );
        } 

            $buttonsHtml .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Insert Pdf...'),
                    'onclick' => "MediabrowserUtility.openDialog('"
                        . $this->getConfig('files_browser_window_url')
                        . "target_element_id/" . $this->getHtmlId() . "/"
                        . (null !== $this->getConfig('store_id') ? 'store/'
                            . $this->getConfig('store_id') . '/' : '')
                        . "')",
                    'class' => 'action-add-file plugin',
                    'style' => $visible ? '' : 'display:none',
                ]
            );


        if (is_array($this->getConfig('plugins'))) {
            foreach ($this->getConfig('plugins') as $plugin) {
                if (isset($plugin['options']) && $this->_checkPluginButtonOptions($plugin['options'])) {
                    $buttonOptions = $this->_prepareButtonOptions($plugin['options']);
                    if (!$visible) {
                        $configStyle = '';
                        if (isset($buttonOptions['style'])) {
                            $configStyle = $buttonOptions['style'];
                        }
                        $buttonOptions = array_merge($buttonOptions, ['style' => 'display:none;' . $configStyle]);
                    }
                    $buttonsHtml .= $this->_getButtonHtml($buttonOptions);
                }
            }
        }

        return $buttonsHtml;
    }
}
