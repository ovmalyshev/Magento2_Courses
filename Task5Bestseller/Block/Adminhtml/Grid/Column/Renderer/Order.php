<?php

namespace Speroteck\Task5Bestseller\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;

class Order extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var Select
     */
    private Select $select;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var int
     */
    public $storeIds = 0;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(DataObject $row): string
    {
        $orderNumbers = '';
        $id = (int)$row->getProductId();
        $periodType = $this->getColumn()->getPeriodType();
        $period = (string)$row->getPeriod();

        if ($periodType == 'day') {
            $substringCondition = 'SUBSTRING(sales_order_item.updated_at, 1, 10)=SUBSTRING(MAX(\'' . $period . '\'), 1, 10)';
        } else {
            $substringCondition = 'SUBSTRING(sales_order_item.updated_at, 1, 4)=SUBSTRING(MAX(\'' . $period . '\'), 1, 4)';
        }

        $cols=[];
        $connection = $this->resourceConnection->getConnection();
        $cols['order_ids'] = $connection->select()->from(
            'sales_order_item',
            'GROUP_CONCAT(sales_order_item.order_id)'
        )
            ->where(
                'sales_order_item.product_id=sales_bestsellers_aggregated_daily.product_id'
            )
            ->where(
                $substringCondition
            );

        $this->select = $connection->select()
            ->from(
                'sales_bestsellers_aggregated_daily',
                $cols
            )
            ->where("product_id = ?", $id)
            ->where('store_id IN(?) OR store_id IS NULL', $this->storeIds);

        if (!$id == 0) {
            ////            $this->select->reset(\Zend_Db_Select::WHERE)->where("product_id = $id");
            ////            $orderNumbers = $this->select->query()->fetchColumn();
//            $orderNumbers = $this->select->query(\Zend_Db::FETCH_COLUMN, [$id])->fetchColumn();
            $orderNumbers = $this->select->query()->fetchColumn();
        }
        return $orderNumbers;
    }
}
