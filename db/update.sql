DROP TABLE IF EXISTS CronTareas;
CREATE  TABLE `CronTareas` (
  `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(100) NULL ,
  `Script` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) )ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS CronTiposProgramaciones;
CREATE  TABLE `CronTiposProgramaciones` (
  `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) )ENGINE=InnoDB;

DROP TABLE IF EXISTS CronProgramaciones;
CREATE TABLE `CronProgramaciones` (
  `Id` int unsigned NOT NULL AUTO_INCREMENT,
  `CronTarea` int unsigned NOT NULL,
  `Hora` time NOT NULL,
  `Dia` tinyint(3) unsigned NOT NULL,
  `Tipo` int unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_CronProgramaciones_1` (`CronTarea`),
  KEY `fk_CronProgramaciones_2` (`Tipo`),
  CONSTRAINT `fk_CronProgramaciones_1` FOREIGN KEY (`CronTarea`) REFERENCES `CronTareas` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_CronProgramaciones_2` FOREIGN KEY (`Tipo`) REFERENCES `CronTiposProgramaciones` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `Jobs` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`handler` BLOB NOT NULL,
`queue` VARCHAR(255) NOT NULL DEFAULT 'default',
`attempts` INT UNSIGNED NOT NULL DEFAULT 0,
`run_at` DATETIME NULL,
`locked_at` DATETIME NULL,
`locked_by` VARCHAR(255) NULL,
`failed_at` DATETIME NULL,
`error` TEXT NULL,
`created_at` DATETIME NOT NULL
) ENGINE = INNODB  DEFAULT CHARSET=utf8;

CREATE  TABLE `vidalac_liquidaciones`.`VariablesTiposDeLiquidaciones` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `TipoDeLiquidacion` INT UNSIGNED NOT NULL ,
  `Variable` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_VariablesTiposDeLiquidaciones_1` (`Variable` ASC) ,
  INDEX `fk_VariablesTiposDeLiquidaciones_2` (`TipoDeLiquidacion` ASC) ,
  CONSTRAINT `fk_VariablesTiposDeLiquidaciones_1`
    FOREIGN KEY (`Variable` )
    REFERENCES `vidalac_liquidaciones`.`Variables` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_VariablesTiposDeLiquidaciones_2`
    FOREIGN KEY (`TipoDeLiquidacion` )
    REFERENCES `vidalac_liquidaciones`.`TiposDeLiquidaciones` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `OrdenesDeProduccionesDetalles` CHANGE COLUMN `Articulo` `ArticuloVersion` INT(11) UNSIGNED NOT NULL  ;
ALTER TABLE `Liquidaciones` DROP COLUMN `EsReliquidacion`;
ALTER TABLE `vidalac_liquidaciones`.`LiquidacionesRecibos` DROP COLUMN `VariablesCalculadas` , ADD COLUMN `Ajuste` INT UNSIGNED NULL  AFTER `Servicio` ;