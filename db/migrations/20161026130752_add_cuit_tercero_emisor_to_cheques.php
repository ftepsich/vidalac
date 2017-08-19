<?php

use Phinx\Migration\AbstractMigration;

class AddCuitTerceroEmisorToCheques extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('Cheques');
        $table->addColumn('CuitTerceroEmisor', 'string',array('limit' => '20'));
        $table->update();
    }

    public function down()
    {
        $table = $this->table('Cheques');
        $table->removeColumn('CuitTerceroEmisor')
              ->save();
    }
}
