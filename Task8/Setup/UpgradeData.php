<?php

namespace Speroteck\Task8\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package Speroteck\Task2\Setup
 */
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
        $setup->startSetup();

        if ($context->getVersion()
            && version_compare($context->getVersion(), '0.0.2') < 0
        ) {
            $tableName = $setup->getTable('custom_test');

            $data = [
                [
                    'title' => 'Post 1 Title',
                    'description' => 'Content of the first post.',
                ],
                [
                    'title' => 'Post 2 Title',
                    'description' => 'Content of the second post.',
                ],
                [
                    'title' => 'Post 3 Title',
                    'description' => 'Content of the third post.',
                ],
            ];

            $setup
                ->getConnection()
                ->insertMultiple($tableName, $data);
        }

        $setup->endSetup();
    }
}
