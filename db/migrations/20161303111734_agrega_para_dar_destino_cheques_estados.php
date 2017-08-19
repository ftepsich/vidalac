<?php

use Phinx\Migration\AbstractMigration;

use Phinx\Db\Adapter\MysqlAdapter;

class AgregaParaDarDestinoChequesEstados extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('ChequesEstados');
        $table->addColumn('ParaDarDestino', 'integer',array('limit' => MysqlAdapter::INT_TINY,'signed' => false));
        $table->update();
    }

    public function down()
    {
        $table = $this->table('ChequesEstados');
        $table->removeColumn('ParaDarDestino')
              ->save();
    }
}
