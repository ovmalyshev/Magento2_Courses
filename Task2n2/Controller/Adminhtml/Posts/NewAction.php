<?php
namespace Speroteck\Task2n2\Controller\Adminhtml\Posts;

use Speroteck\Task2n2\Controller\Adminhtml\Posts;

class NewAction extends Posts
{
    /**
     * Create new news action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
