<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Speroteck\Task5Bestseller\Model\ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection
{

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns($mainTable = 'sales_bestsellers_aggregated_daily')
    {
        $connection = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
                $this->_selectedColumns['order_ids'] = $this->getOrderIDSelect($mainTable);
            } else {
                $this->_selectedColumns = [
                    'period' => sprintf('MAX(%s)', $connection->getDateFormatSql('period', '%Y-%m-%d')),
                    $this->getOrderedField() => 'SUM(' . $this->getOrderedField() . ')',
                    'product_id' => 'product_id',
                    'product_name' => 'MAX(product_name)',
                    'product_price' => 'MAX(product_price)',
                    'order_ids' => $this->getOrderIDSelect($mainTable, true),
                ];
                if ('year' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y');
                } elseif ('month' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y-%m');
                }
            }
        }
        return $this->_selectedColumns;
    }

    protected function _applyStoresFilterToSelect(\Magento\Framework\DB\Select $select)
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _applyAggregatedTable()
    {
        $select = $this->getSelect();

        //if grouping by product, not by period
        if (!$this->_period) {
            if ($this->_from || $this->_to) {
                $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
            } else {
                $mainTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
            }

            $cols = $this->_getSelectedColumns($mainTable);
            $cols[$this->getOrderedField()] = 'SUM(' . $this->getOrderedField() . ')';

            $select->from($mainTable, $cols);

            //exclude removed products
            $select->where(new \Zend_Db_Expr($mainTable . '.product_id IS NOT NULL'))->group(
                'product_id'
            )->order(
                $this->getOrderedField() . ' ' . \Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                $this->_ratingLimit
            );

            return $this;
        }

        if ('year' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
            $select->from($mainTable, $this->_getSelectedColumns($mainTable));
        } elseif ('month' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('monthly'));
            $select->from($mainTable, $this->_getSelectedColumns($mainTable));
        } else {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
            $select->from($mainTable, $this->_getSelectedColumns());
        }
        if (!$this->isTotals()) {
            $select->group(['period', 'product_id']);
        }

        $select->where('rating_pos <= ?', $this->_ratingLimit);

        return $this;
    }

    /**
     * Make select object for date boundary
     *
     * @param string $from
     * @param string $to
     * @return \Magento\Framework\DB\Select
     */
    protected function _makeBoundarySelect($from, $to)
    {
        $connection = $this->getConnection();
        $cols = $this->_getSelectedColumns();
        $cols[$this->getOrderedField()] = 'SUM(' . $this->getOrderedField() . ')';
        $cols['order_ids'] = $this->getOrderIDSelect();
        $select = $connection->select()->from(
            $this->getResource()->getMainTable(),
            $cols
        )->where(
            'period >= ?',
            $from
        )->where(
            'period <= ?',
            $to
        )->group(
            'product_id'
        )->order(
            $this->getOrderedField() . ' DESC'
        )->limit(
            $this->_ratingLimit
        );

        $this->_applyStoresFilterToSelect($select);

        return $select;
    }

    protected function getOrderIDSelect($mainTable='sales_bestsellers_aggregated_daily', $periodDayOption = false)
    {
        $salesOrderItemTable = 'sales_order_item';
        if ($mainTable == 'sales_bestsellers_aggregated_daily' && $periodDayOption) {
            $substringCondition = 'SUBSTRING(' . $salesOrderItemTable . '.updated_at, 1, 10)=SUBSTRING(MAX(period), 1, 10)';
        } else {
            $substringCondition = 'SUBSTRING(' . $salesOrderItemTable . '.updated_at, 1, 4)=SUBSTRING(MAX(period), 1, 4)';
        }
        $select = $this->getConnection()->select()->from(
            $salesOrderItemTable,
            'GROUP_CONCAT(' . $salesOrderItemTable . '.order_id)'
        )
            ->where(
                $salesOrderItemTable . '.product_id=' . $mainTable . '.product_id'
            )
            ->where(
                $substringCondition
            );
        return $select;
    }
}
