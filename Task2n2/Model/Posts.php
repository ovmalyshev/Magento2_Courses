<?php
namespace Speroteck\Task2n2\Model;

use Magento\Framework\Model\AbstractModel;

class Posts extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Speroteck\Task2n2\Model\ResourceModel\Posts');
    }
}
