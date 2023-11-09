<?php
namespace Speroteck\Task8\Block\Adminhtml;

class After extends \Magento\Framework\View\Element\Template
{
    /**
     * getContentForDisplay
     * @return string
     */
    public function getContentForDisplay()
    {
        return __("Hello world!!! '\n' Task 1. Number 2");
    }
}
