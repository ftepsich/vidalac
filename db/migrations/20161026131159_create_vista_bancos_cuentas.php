<?php

use Phinx\Migration\AbstractMigration;

class CreateVistaBancosCuentas extends AbstractMigration
{
    public function up()
    {
        $this->execute("DROP VIEW IF EXISTS `VBancosCuentas`");
        $this->execute("
            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`127.0.0.1` SQL SECURITY DEFINER VIEW `VBancosCuentas` AS
              select
                (concat(`TC`.`Codigo`,' : ',`B`.`Descripcion`,' : ',`CB`.`Numero`) collate utf8_unicode_ci) AS `Descripcion`,
                `CB`.`Numero` AS `CuentaBancariaNumero`,
                `CB`.`Cbu` AS `Cbu`,
                `CB`.`Persona` AS `Persona`,
                `CB`.`Propia` AS `Propia`,
                `B`.`Descripcion` AS `Banco`,
                `CB`.`Id` AS `CuentaBancariaId`,
                `BS`.`Id` AS `BancoSucursalId`
              from
                (((`BancosSucursales` `BS` join `Bancos` `B` on((`BS`.`Banco` = `B`.`Id`))) join `CuentasBancarias` `CB` on((`BS`.`Id` = `CB`.`BancoSucursal`))) join `TiposDeCuentas` `TC` on((`TC`.`Id` = `CB`.`TipoDeCuenta`)))
              where
                (`CB`.`Id` is not null)
              order by
                `CB`.`TipoDeCuenta`,`B`.`Descripcion`;
        ");
    }

    public function down()
    {
        $this->execute("DROP VIEW  `VBancosCuentas`");
    }
}
