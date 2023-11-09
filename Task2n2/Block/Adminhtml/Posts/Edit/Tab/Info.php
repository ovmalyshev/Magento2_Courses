<?php
namespace Speroteck\Task2n2\Block\Adminhtml\Posts\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Speroteck\Task2\Model\ResourceModel\Post\Collection;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /** @var Collection */
    private $postCollection;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Collection $postCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Collection $postCollection,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->postCollection = $postCollection;
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \Speroteck\Task2n2\Model\PostsFactory */
        $model = $this->_coreRegistry->registry('speroteck_posts');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        // new filed

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

//        -------------------------
        ////        Working code
//        if ($model->getId()) {
//            $fieldset->addField(
//                'post_id',
//                'hidden',
//                ['name' => 'post_id']
//            );
//        };
        //--------------------------------
        $fieldset->addField(
            'post_id',
            'text',
            [
                'name' => 'post_id',
                'label'     => __('Post id'),
                'title' => __('Post id'),
                'readonly' => false
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name'      => 'title',
                'label'     => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'content',
            'editor',
            [
            'name'      => 'content',
            'label'   => 'Content',
            'config'    => $this->_wysiwygConfig->getConfig(),
            'wysiwyg'   => true,
            'required'  => false
            ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Posts Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Posts Info');
    }

    /**
     * Can Show Tab
     *
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     *
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }
}
