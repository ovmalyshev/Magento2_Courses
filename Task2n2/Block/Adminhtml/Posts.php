<?php
namespace Speroteck\Task2n2\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Posts extends Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_posts';
        $this->_blockGroup = 'Speroteck_Task2n2';
        $this->_headerText = __('Manage Posts');
        $this->_addButtonLabel = __('Add New Post');

        parent::_construct();

//        $this->buttonList->remove('add'); //to remove add button
    }
}
