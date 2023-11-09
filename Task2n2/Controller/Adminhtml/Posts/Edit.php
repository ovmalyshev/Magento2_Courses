<?php
namespace Speroteck\Task2n2\Controller\Adminhtml\Posts;

use Speroteck\Task2n2\Controller\Adminhtml\Posts;

class Edit extends Posts
{
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');

        $model = $this->_postsFactory->create();

        if ($postId) {
            $model->load($postId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('speroteck_posts', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Backend::content_elements');
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));

        return $resultPage;
    }
}
