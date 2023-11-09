<?php
namespace Speroteck\Task7ApiCrud\Model;

use Speroteck\Task2\Api\Data\PostInterface;
use Speroteck\Task2\Model\PostFactory;
use Speroteck\Task2\Model\ResourceModel\Post as PostResource;
use Speroteck\Task2\Model\ResourceModel\Post\CollectionFactory;
use Speroteck\Task7ApiCrud\Api\CrudPostInterface;
use Speroteck\Task7ApiCrud\Api\Data\PostSearchResultInterface;
use Speroteck\Task7ApiCrud\Api\Data\PostSearchResultInterfaceFactory;

class CrudPost implements CrudPostInterface
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $returnPosts = [];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     *  response
     */
    protected $response = ['success' => false];

    /**
     *  success
     */
    protected $success = ['success: '=> true];
    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @var PostSearchResultInterfaceFactory
     */
    private $postSearchResultFactory;

    public function __construct(
        PostResource $postResource,
        CollectionFactory $collectionFactory,
        PostFactory $postFactory,
        PostSearchResultInterfaceFactory $postSearchResultFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
        $this->postSearchResultFactory = $postSearchResultFactory;
    }

    /**
     * @param PostInterface $postsData
     * @return PostInterface
     */
    public function addPosts($postsData)
    {
        try {
            /**
             * @var PostInterface $postsData
             */
            $this->postResource->save($postsData);
            $this->registry[$postsData->getId()] = $this->getById($postsData->getId());
        } catch (\Exception $exception) {
            throw new StateException(__('Unable to save post #%1', $postsData->getId()));
        }
        return $this->registry[$postsData->getId()];
    }

    /**
     * @return PostSearchResultInterface
     */
    public function getAllPosts()
    {
        $collection = $this->collectionFactory->create();

        /**
         * @var PostSearchResultInterface $searchResult
         */
        $searchResult = $this->postSearchResultFactory->create();
        $searchResult->setItems($collection->getData());
        return $searchResult;
    }

    /**
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id)
    {
        try {
            if ($id) {
                $data = $this->postFactory->create()->load($id);
                $data->delete();
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $id
     * @return PostInterface
     */
    public function getById($id)
    {
        if (!array_key_exists($id, $this->registry)) {
            $post = $this->postFactory->create();
            $this->postResource->load($post, $id);
            $this->registry[$id] = $post;
        }
        return $this->registry[$id];
    }

    /**
     * GET for Post api
     * @param int $id
     * @param  string[] $postData
     * @return PostInterface
     */
    public function editById($id, $postData)
    {
        if (!array_key_exists($id, $this->registry)) {
            $post = $this->postFactory->create();
            $this->postResource->load($post, $id);
            $post->setTitle($postData['title'])->setContent($postData['content']);
            $post->save();
            $this->registry[$id] = $post;
        }
        return $this->registry[$id];
    }
}
