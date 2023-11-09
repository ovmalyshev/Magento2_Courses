<?php

namespace Speroteck\Task7ApiCrud\Api;


use Speroteck\Task2\Api\Data\PostInterface;
use Speroteck\Task7ApiCrud\Api\Data\PostSearchResultInterface;

interface CrudPostInterface
{
    /**
     * @param PostInterface $postsData
     * @return PostInterface
     */
    public function addPosts($postsData);

    /**
     * @return PostSearchResultInterface
     */
    public function getAllPosts();

    /**
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * @param int $id
     * @return PostInterface
     */
    public function getById($id);

    /**
     * GET for Post api
     * @param int $id
     * @param string[] $postData
     * @return PostInterface
     */
    public function editById($id, $postData);
}
