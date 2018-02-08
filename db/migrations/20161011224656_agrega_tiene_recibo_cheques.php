<?php

use Phinx\Migration\AbstractMigration;

use Phinx\Db\Adapter\MysqlAdapter;

class AgregaTieneReciboCheques extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('Cheques');
        $table->addColumn('TieneRecibo', 'integer',array('limit' => MysqlAdapter::INT_TINY,'signed' => false));
        $table->update();
    }

    public function down()
    {
        $table = $this->table('Cheques');
        $table->removeColumn('TieneRecibo')
              ->save();
    }
}
