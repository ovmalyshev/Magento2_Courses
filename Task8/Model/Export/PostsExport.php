<?php
namespace Speroteck\Task8\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Layout;
use Magento\ImportExport\Model\Export\Factory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManagerInterface;
use Speroteck\Task2\Model\ResourceModel\Post\Collection;

class PostsExport extends \Magento\ImportExport\Model\Export\AbstractEntity
{
    /**
    * Column names.
    *
    */
    const COL_POST_ID = 'post_id';
    const COL_TITLE = 'title';
    const COL_CONTENT = 'content';
    const COL_CREATED_AT = 'created_at';

    /** @var string[] */
    protected $_permanentAttributes = [self::COL_POST_ID, self::COL_TITLE, self::COL_CONTENT, self::COL_CREATED_AT];

    /** @var Collection */
    protected $_customerCollection;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Factory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        Layout $layout,
        Collection $_customerCollection,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $storeManager,
            $collectionFactory,
            $resourceColFactory,
            $data
        );
        $this->_customerCollection = $_customerCollection;

        $layout->unsetElement('export.filter');
    }

    /**
     * Export process
     *
     * @return string
     */
    public function export()
    {
        $writer = $this->getWriter();

        // create export file
        $writer->setHeaderCols($this->_getHeaderColumns());
        $exportData = $this->_getEntityCollection()->getData();
        foreach ($exportData as $dataRow) {
            $writer->writeRow($dataRow);
        }
        return $writer->getContents();
    }

    public function exportItem($item)
    {
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return  'posts_export';
    }

    /**
     * Get columns header
     *
     * @return string[]
     */
    protected function _getHeaderColumns(): array
    {
        return $this->_permanentAttributes;
    }

    /**
     * Get customers collection
     *
     * @return Collection
     */
    protected function _getEntityCollection()
    {
        return $this->_customerCollection;
    }
}
