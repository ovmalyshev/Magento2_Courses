<?php
namespace Speroteck\Task2\Model\ResourceModel\Post;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Remittance File Collection Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Speroteck\Task2\Model\Post', 'Speroteck\Task2\Model\ResourceModel\Post');
    }
}
