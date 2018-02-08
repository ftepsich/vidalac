<?php

use Phinx\Migration\AbstractMigration;

class FuncionCuentaBancaria extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE DEFINER = 'root'@'127.0.0.1' FUNCTION `fCuentaBancaria`(
                    IdCuenta INTEGER(11)
                )
                RETURNS varchar(50) CHARSET utf8 COLLATE utf8_unicode_ci
                NOT DETERMINISTIC
                CONTAINS SQL
                SQL SECURITY DEFINER
                COMMENT ''
            BEGIN

            declare cta varchar(50) default '';



            SELECT  ifnull(concat(`TC`.`Codigo`,' : ',`B`.`Descripcion`,' : ',`CB`.`Numero`) collate utf8_unicode_ci ,' ')
            INTO    cta
            FROM    BancosSucursales BS
            JOIN    Bancos B                on B.Id  = BS.Banco
            JOIN    CuentasBancarias CB     on BS.Id = CB.BancoSucursal
            JOIN    TiposDeCuentas TC       on TC.Id = CB.TipoDeCuenta
            WHERE   CB.Id is not null
            AND     CB.Id = ifnull(IdCuenta,0);

              RETURN cta;
            END;
        ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DROP FUNCTION `fCuentaBancaria`");
    }
}
