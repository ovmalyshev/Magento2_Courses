<?php
namespace Speroteck\Task2n2\Model\ResourceModel\Posts;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'Speroteck\Task2n2\Model\Posts',
            'Speroteck\Task2n2\Model\ResourceModel\Posts'
        );
    }
}
