<?php
namespace Speroteck\Task2n2\Controller\Adminhtml\Posts;

use Speroteck\Task2n2\Controller\Adminhtml\Posts;

class Save extends Posts
{
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $formData = $this->getRequest()->getParam('post');

        if (is_array($formData)) {
            $model = $this->_postsFactory->create();
            $id = $formData['post_id'] ?? null;

            if (!empty($id)) {
                $model->load($id);
            } else {
                unset($formData['post_id']);
            }

            $model->setData($formData);

            try {
                // Save news
                $model->save();

                // Display success message
                $this->messageManager->addSuccess(__('The news has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $id]);
        }
    }
}
