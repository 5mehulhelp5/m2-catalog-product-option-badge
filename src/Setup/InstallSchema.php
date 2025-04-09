<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $optionTableName = $connection->getTableName('catalog_product_option');
        $optionValueTableName = $connection->getTableName('catalog_product_option_type_value');

        foreach ([$optionTableName, $optionValueTableName] as $tableName) {
            if (! $connection->tableColumnExists(
                $tableName,
                'badge'
            )) {
                $connection->addColumn(
                    $tableName,
                    'badge',
                    ['type' => Table::TYPE_TEXT, 'length' => 5000, 'nullable' => true, 'comment' => 'Badge']
                );
            }
        }

        $setup->endSetup();
    }
}
