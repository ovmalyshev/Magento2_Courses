<?php
namespace Speroteck\Task2n2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public const ENABLED  = 1;
    public const DISABLED = 0;

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
        return $options;
    }
}
