<?php
namespace Speroteck\Task2\Controller\Delete;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Speroteck\Task2\Model\PostFactory;
use Speroteck\Task2\Model\ResourceModel\Post\Collection;

class Delete implements HttpGetActionInterface
{
    /** @var ResourceConnection */
    private $resourceConnection;

    /** @var Context */
    private $context;

    /**
     * @var Post
     */
    protected $post;

    /**
     * @var Collection
     */
    private $postCollection;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param PostFactory $postFactory
     * @param Collection $postCollection
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory,
        Collection $postCollection,
        ResourceConnection $resourceConnection
    ) {
        $this->postFactory = $postFactory;
        $this->context = $context;
        $this->postCollection = $postCollection;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $postId = $this->context->getRequest()->getParam('postId');

        $postId
            ? $this->postFactory->create()
                                        ->load($postId)->delete()
            : $this->resourceConnection->getConnection()->delete('speroteck_task2_post');

        return $this->context
            ->getResultFactory()
            ->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl('/task2/index/index');
    }
}
