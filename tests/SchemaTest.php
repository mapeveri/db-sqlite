<?php

declare(strict_types=1);

namespace Yiisoft\Db\Sqlite\Tests;

use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\InvalidConfigException;
use Yiisoft\Db\Exception\NotSupportedException;
use Yiisoft\Db\Tests\AnyValue;
use Yiisoft\Db\Tests\SchemaTest as AbstractSchemaTest;

class SchemaTest extends AbstractSchemaTest
{
    protected ?string $driverName = 'sqlite';

    public function testGetSchemaNames()
    {
        $this->markTestSkipped('Schemas are not supported in SQLite.');
    }

    public function getExpectedColumns()
    {
        $columns = parent::getExpectedColumns();

        unset(
            $columns['enum_col'],
            $columns['bit_col'],
            $columns['json_col']
        );

        $columns['int_col']['dbType'] = 'integer';
        $columns['int_col']['size'] = null;
        $columns['int_col']['precision'] = null;
        $columns['int_col2']['dbType'] = 'integer';
        $columns['int_col2']['size'] = null;
        $columns['int_col2']['precision'] = null;
        $columns['bool_col']['type'] = 'boolean';
        $columns['bool_col']['phpType'] = 'boolean';
        $columns['bool_col2']['type'] = 'boolean';
        $columns['bool_col2']['phpType'] = 'boolean';
        $columns['bool_col2']['defaultValue'] = true;

        return $columns;
    }

    public function testCompositeFk()
    {
        $db = $this->getConnection();

        $schema = $db->getSchema();

        $table = $schema->getTableSchema('composite_fk');

        $fk = $table->getForeignKeys();
        $this->assertCount(1, $fk);
        $this->assertTrue(isset($fk[0]));
        $this->assertEquals('order_item', $fk[0][0]);
        $this->assertEquals('order_id', $fk[0]['order_id']);
        $this->assertEquals('item_id', $fk[0]['item_id']);
    }

    public function constraintsProvider()
    {
        $result = parent::constraintsProvider();

        $result['1: primary key'][2]->name(null);
        $result['1: check'][2][0]->columnNames(null);
        $result['1: check'][2][0]->expression('"C_check" <> \'\'');
        $result['1: unique'][2][0]->name(AnyValue::getInstance());
        $result['1: index'][2][1]->name(AnyValue::getInstance());

        $result['2: primary key'][2]->name(null);
        $result['2: unique'][2][0]->name(AnyValue::getInstance());
        $result['2: index'][2][2]->name(AnyValue::getInstance());

        $result['3: foreign key'][2][0]->name(null);
        $result['3: index'][2] = [];

        $result['4: primary key'][2]->name(null);
        $result['4: unique'][2][0]->name(AnyValue::getInstance());

        return $result;
    }

    /**
     * @dataProvider quoteTableNameDataProvider
     *
     * @param $name
     * @param $expectedName
     *
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotSupportedException
     */
    public function testQuoteTableName($name, $expectedName): void
    {
        $schema = $this->getConnection()->getSchema();
        $quotedName = $schema->quoteTableName($name);
        $this->assertEquals($expectedName, $quotedName);
    }

    public function quoteTableNameDataProvider(): array
    {
        return [
            ['test', '`test`'],
            ['test.test', '`test`.`test`'],
            ['test.test.test', '`test`.`test`.`test`'],
            ['`test`', '`test`'],
            ['`test`.`test`', '`test`.`test`'],
            ['test.`test`.test', '`test`.`test`.`test`'],
        ];
    }
}
