<?php

namespace Speroteck\Task2\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Post extends AbstractDb
{
    /**
     * Post Abstract Resource Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('speroteck_task2_post', 'post_id');
    }
}
