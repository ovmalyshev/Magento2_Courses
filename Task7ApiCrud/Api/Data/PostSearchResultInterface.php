<?php
namespace Speroteck\Task7ApiCrud\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PostSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return Speroteck\Task2\API\Data\PostInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items);
}
