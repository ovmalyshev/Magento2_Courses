<?php

namespace Speroteck\Task2\Setup;

use \Magento\Framework\Setup\UpgradeDataInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;

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
            && version_compare($context->getVersion(), '0.1.4') < 0
        ) {
            $tableName = $setup->getTable('speroteck_task2_post');

            $data = [
                [
                    'title' => 'Post 1 Title',
                    'content' => 'Content of the first post.',
                ],
                [
                    'title' => 'Post 2 Title',
                    'content' => 'Content of the second post.',
                ],
                [
                    'title' => 'Post 3 Title',
                    'content' => 'Content of the third post.',
                ],
            ];

            $setup
                ->getConnection()
                ->insertMultiple($tableName, $data);
//        } else {
//            $setup->getConnection()
//              ->insert(
//                      'speroteck_task2_post',
//                      ['title' => 'Post 3 Title 3', 'content' => 'Content 3 of the first post.']
//                      );
        }

        $setup->endSetup();
    }
}
