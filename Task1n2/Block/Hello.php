<?php
namespace Speroteck\Task1n2\Block;

class Hello extends \Magento\Framework\View\Element\Template
{
    /**
     * GetContentForDisplay
     *
     * @return string
     */
    public function getContentForDisplay()
    {
        return __("Hello world!!! Task 1. Number 2");
    }
}
