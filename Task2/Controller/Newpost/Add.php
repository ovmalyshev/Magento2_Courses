<?php
namespace Speroteck\Task2\Controller\Newpost;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Speroteck\Task2\Model\PostFactory;
use Speroteck\Task2\Model\ResourceModel\Post\Collection;

class Add implements HttpGetActionInterface
{
    /** @var Context */
    private $context;

    /** @var PostFactory */
    private $postFactory;

    /** @var Collection */
    private $postCollection;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param Collection $postCollection
     * @param PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        Collection $postCollection,
        PostFactory $postFactory
    ) {
        $this->postFactory = $postFactory;
        $this->postCollection = $postCollection;
        $this->context = $context;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $lastId = $this->postCollection->getLastItem()->getId();
        for ($i = empty($lastId) ? 1 : $lastId+1; $i < $lastId+10; $i++) {
            $post = $this->postFactory->create();
            $post->setData('title', "Post $i, Title " . rand(0, 100))
                ->setData('content', "Content $i of the first post.")
                ->save();
        }

        return $this->context
            ->getResultFactory()
            ->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl('/task2/index/index');
    }
}
