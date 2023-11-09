<?php
namespace Speroteck\Task2n2\Controller\Adminhtml\Posts;

use Speroteck\Task2n2\Controller\Adminhtml\Posts;

class Delete extends Posts
{
    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $postId = (int) $this->getRequest()->getParam('id');

        if ($postId) {
            /** @var $postModel Speroteck\Task2n2\Model\Posts */
            $postModel = $this->_postsFactory->create();
            $postModel->load($postId);

            // Check this news exists or not
            if (!$postModel->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
            } else {
                try {
                    // Delete news
                    $postModel->delete();
                    $this->messageManager->addSuccess(__('The news has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $postModel->getId()]);
                }
            }
        }
    }
}
