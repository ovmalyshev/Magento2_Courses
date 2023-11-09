<?php

namespace Speroteck\Task4\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * Creates sample blog posts
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $updateColumnInSalesOrderGrid = true;
        if ($updateColumnInSalesOrderGrid) {
            $setup->startSetup();

            if (version_compare($context->getVersion(), '0.1.5', '<')) {
                $connection = $setup->getConnection();
                $gridTable = $setup->getTable('sales_order_grid');
                $deliveryDateTable = $setup->getTable('sales_order');

                $connection->query(
                    $connection->updateFromSelect(
                        $connection->select()
                            ->join(
                                $deliveryDateTable,
                                sprintf('%s.entity_id = %s.entity_id', $gridTable, $deliveryDateTable),
                                'delivery_date'
                            ),
                        $gridTable
                    )
                );
            }

            $setup->endSetup();
        }
    }
}
