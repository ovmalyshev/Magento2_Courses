<?php

namespace Speroteck\Task2\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Speroteck\Task2\Model\Post;
use Speroteck\Task2\Model\PostFactory;

class View extends Template
{
    /** @var Post */
    protected $post;

    /** @var PostFactory */
    protected $postFactory;

    /** @var RequestInterface */
    private $request;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param RequestInterface $request
     * @param PostFactory $postFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        PostFactory $postFactory,
        array $data = []
    ) {
        $this->postFactory = $postFactory;
        $this->request = $request;

        parent::__construct($context, $data);
    }

    /**
     * Lazy loads the requested post
     *
     * @return Post
     * @throws LocalizedException
     */
    public function getPost()
    {
        if ($this->post === null) {
            $post = $this->postFactory->create();
            $post->load($this->getPostId());

            if (!$post->getId()) {
                throw new LocalizedException(__('Post not found'));
            }
            $this->post = $post;
        }
        return $this->post;
    }

    /**
     * Get Post Id
     *
     * @return int
     */
    protected function getPostId()
    {
        return (int) $this->request->getParam('id');
    }

    /**
     * Delete Current Post Url
     *
     * @param string $postId
     *
     * @return string
     */
    public function delCurrentPostUrl($postId): string
    {
        return '/task2/delete/delete?postId=' . $postId;
    }
}
