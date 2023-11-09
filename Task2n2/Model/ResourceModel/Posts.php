<?php
namespace Speroteck\Task2n2\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Posts extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('speroteck_task2_post', 'post_id');
    }
}
