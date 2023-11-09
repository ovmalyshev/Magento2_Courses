<?php
namespace Speroteck\Task1\Block;

class Hello extends \Magento\Framework\View\Element\Template
{
    /**
     * GetContentForDisplay
     *
     * @return string
     */
    public function getContentForDisplay()
    {
        return __("Hello world!!!");
    }
}
