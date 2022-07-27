<?php

namespace Elsnertech\EasyTabs\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(
		SchemaSetupInterface $setup,
		ModuleContextInterface $context
	) {
		$installer = $setup;
		$installer->startSetup();

		$table = $installer->getConnection()->newTable(
			$installer->getTable('elsnertech_tabs')
		)->addColumn(
			'entity_id',
			Table::TYPE_INTEGER,
			null,
			['identity' => true, 'nullable' => false, 'primary' => true],
			'Tab Id'
		)->addColumn(
			'class',
			Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Tab Class Name'
		)->addColumn(
			'title',
			Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Title'
		)->addColumn(
			'content',
			Table::TYPE_TEXT,
			'2M',
			['nullable' => false],
			'Content'
		)->addColumn(
			'tab_sort',
			Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Tab Sort Number'
		)->addColumn(
			'is_active',
			Table::TYPE_SMALLINT,
			null,
			[],
			'Active Status'
		)->setComment(
			'Elsnertech Tabs Table'
		);

		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}