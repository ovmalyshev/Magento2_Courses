<?php
namespace Speroteck\Task1n2\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Hello Landing page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        ////        Variant 1
//
//        $resultPage = $this->_resultPageFactory->create();
//        $resultPage->getConfig()->getTitle()->set(__('Title. Task1. Number 2. Ver 1. Create module HelloWorld'));
//        $resultPage->getLayout()
//            ->addBlock('Speroteck\Task1n2\Block\Hello', 'task1n2', 'content')
//            ->setTemplate('Speroteck_Task1n2::success.phtml');
//
//        return $resultPage;
        //--------------
//          Variant 2

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Title. Task1. Number 2. Ver 2. Create module HelloWorld'));

        $layout = $resultPage->getLayout();
        $layout
            ->createBlock('Speroteck\Task1n2\Block\Hello', 'task1n2')
            ->setTemplate('Speroteck_Task1n2::success.phtml');

        $layout->setChild('content', 'task1n2', null);

        return $resultPage;
        //--------------
//          Variant 3
//        $this->_view->loadLayout();
//        $this->_view->getPage()->getConfig()->getTitle()
//              ->set(__('Title. Task1. Number 2. Ver 3. Create module HelloWorld'));
//
//        $this->_view->getLayout()
//            ->addBlock('Speroteck\Task1n2\Block\Hello', 'task1n2', 'content')
//            ->setTemplate('Speroteck_Task1n2::success.phtml');
//        return $this->_view->getPage();
//--------------

//          Variant 4 Drafts

//        $resultPage = $this->_resultPageFactory->create();
//        $resultPage->getConfig()->getTitle()->set(__('Title. Task1. Number 2. Create module HelloWorld'));
//
//        $layout = $resultPage->getLayout();
//        $block = $layout->createBlock('Speroteck\Task1n2\Block\Hello','task8')
//            ->setTemplate('Speroteck_Task1n2::success.phtml');
//        $layout->unsetElement('task8');
//
//        $layout->setBlock('block123', $block);
//        $layout->setChild('content', 'block123', null);
//        return $resultPage;
    }
}
