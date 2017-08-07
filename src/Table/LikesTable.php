<?php

namespace Bolt\Extension\TwoKings\Likes\Table;

use Bolt\Storage\Database\Schema\Table\BaseTable;

/**
 * Likes Table
 *
 * @author Bob den Otter <bob@twokings.nl>
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class LikesTable extends BaseTable
{
    /**
     * {@inheritdoc}
     */
    protected function addColumns()
    {
        $this->table->addColumn('id', 'integer', ['autoincrement' => true]);
        $this->table->addColumn('contenttype', 'string', ['notnull' => false]);
        $this->table->addColumn('contentid', 'integer', ['notnull' => false]);
        $this->table->addColumn('totals', 'json_array');
        $this->table->addColumn('ips', 'json_array');
    }

    /**
     * {@inheritdoc}
     */
    protected function addIndexes()
    {
        $this->table->addIndex(['contenttype', 'contentid']);
    }

    /**
     * {@inheritdoc}
     */
    protected function setPrimaryKey()
    {
        $this->table->setPrimaryKey(['id']);
    }
}
